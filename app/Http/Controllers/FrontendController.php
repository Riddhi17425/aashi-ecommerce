<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\Models\ProductReview;
use App\Models\CheckoutForgotPasswordOtp;
use App\User;
use Auth;
use Session;
use Newsletter;
use DB;
use Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\Settings;
use App\Services\SeoMeta;

use GuzzleHttp\Client;

class FrontendController extends Controller
{
    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home()
    {
        $featured = Product::with('cat_info', 'getReview')->where('status', 'active')->where('is_featured', 1)->whereNull('deleted_at')->get()->unique('cat_id')->take(2);
        $posts=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $banners=Banner::where('status','active')->limit(3)->orderBy('id','ASC')->get();
        $products = Product::with('getReview')->where('status', 'active')
        ->where('is_featured', 1)
        ->whereNull('deleted_at')
        ->whereHas('cat_info', function ($query) {
            $query->where('status', 'active');
        })
        ->whereNotNull('product_sequence')
        ->orderBy('product_sequence', 'ASC')
        ->limit(8)
        ->get();
    
        $category=Category::where('status','active')->where('is_parent',1)->orderBy('title','ASC')->get();
        // SEO: title and description built from the active category names
        $categoryNames = $category->pluck('title')->implode(', ');
        $meta_title = Str::limit('Aashi Retail | ' . $categoryNames, 60, '...');
        $meta_description = SeoMeta::description('', 'Shop ' . $categoryNames . ' online at Aashi Retail.');

        return view('frontend.index')
                ->with('page_heading', 'Aashi Retail - ' . $categoryNames)
                ->with('meta_title', $meta_title)
                ->with('meta_description', $meta_description)
                ->with('canonical_url', route('home'))
                ->with('schemas', [
                    // Organization schema is hardcoded in frontend/index.blade.php (home page only).
                    SeoMeta::websiteSchema(),
                ])
                ->with('featured',$featured)
                ->with('posts',$posts)
                ->with('banners',$banners)
                ->with('product_lists',$products)
                ->with('category_lists',$category);
    }   
    
    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
        if (!$product_detail) {
            abort(404, 'Product not found');
        }
        $canReview = false;
        $orderId = null;
        if (auth()->check()) {
            $cart = Cart::with('order')
                ->where('product_id', $product_detail->id)
                ->where('user_id', auth()->id())
                ->whereNotNull('order_id')
                ->first();
            if($cart){
                $orderId = $cart->order_id;
            }
            if ($cart && $cart->order && strtolower($cart->order->status) === 'delivered') {
                $canReview = true;
            }
        }
        $hasReviewed = false;
        if($orderId!= null){
            $hasReviewed = ProductReview::where(['product_id' => $product_detail->id, 'order_id' => $orderId, 'user_id' => auth()->id()])->exists();
        }
       
        // SEO values for the page head
        $category_title = $product_detail->cat_info ? $product_detail->cat_info->title : null;
        $meta_title = $product_detail->meta_title ?: SeoMeta::title($product_detail->title, $category_title);
        $meta_description = $product_detail->meta_description ?: SeoMeta::description(
            $product_detail->summary ?: $product_detail->description,
            'Buy ' . $product_detail->title . ' online from Aashi Retail.'
        );
        $meta_image = SeoMeta::image($product_detail->photo);
        $canonical_url = route('product-detail', $product_detail->slug);

        // Structured data: breadcrumb (Home > Category > Sub-category > Product) and Product
        $crumbs = [['Home', route('home')]];
        if ($product_detail->cat_info) {
            $crumbs[] = [$product_detail->cat_info->title, route('productlist', $product_detail->cat_info->slug)];
            if ($product_detail->sub_cat_info) {
                $crumbs[] = [$product_detail->sub_cat_info->title, route('productlist-with-sub', [$product_detail->cat_info->slug, $product_detail->sub_cat_info->slug])];
            }
        }
        $crumbs[] = [$product_detail->title, $canonical_url];
        $schemas = [
            SeoMeta::breadcrumbSchema($crumbs),
            SeoMeta::productSchema($product_detail, $meta_description),
        ];

        return view('frontend.pages.product_detail', compact('product_detail', 'canReview', 'hasReviewed', 'orderId', 'meta_title', 'meta_description', 'meta_image', 'canonical_url', 'schemas'));
    }

    public function productGrids(Request $request)
    {
        $slug = $request->slug ?? null;
        $category = null;

        $productsQuery = Product::query();
        $productsQuery->where('is_featured', 0)->where('status', 'active');

        if ($slug) {
            $category = Category::where('slug', $slug)->first();
            if (!$category) {
                abort(404);
            }
            $productsQuery->where('cat_id', $category->id);
        }

        if ($request->has('brand') && !empty($request->brand)) {
            $slugs = explode(',', $request->brand);
            $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            if (!empty($brand_ids)) {
                $productsQuery->whereIn('brand_id', $brand_ids);
            }
        }

        if ($request->has('sortBy') && !empty($request->sortBy) && $request->sortBy != 'default') {
            if ($request->sortBy == 'title') {
                $productsQuery->orderBy('title', 'ASC');
            } elseif ($request->sortBy == 'price') {
                $productsQuery->orderByRaw(
                    "CASE WHEN JSON_VALID(`size`) THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(`size`, '$.price[0]')) AS UNSIGNED) ELSE 0 END ASC"
                );
            }
        } else {
            $productsQuery->orderBy('id', 'DESC');
        }

        if ($request->has('show') && is_numeric($request->show)) {
            $perPage = (int) $request->show;
            $products = $productsQuery->paginate($perPage)->appends($request->query());
        } else {
            $products = $productsQuery->paginate(9)->appends($request->query());
        }

        $recent_products = Product::where('status', 'active')
            ->where('is_featured', '0')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        return view('frontend.pages.product-grids', [
            'products' => $products,
            'recent_products' => $recent_products,
            'category' => $category,
        ]);
    }

    public function productSubGrids(Request $request)
    {
        $sub_slug = $request->sub_slug;
        $category = Category::where('slug', $request->slug)->first();

        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();
    
        $products = Category::getProductBySubCat($sub_slug)->sub_products;
    
        return view('frontend.pages.product-grids', [
            'products' => $products,
            'recent_products' => $recent_products,
            'sub_slug' => $sub_slug,
            'category' => $category,
        ]);
    }

    public function showProductList(Request $request, $slug, $sub_slug = null)
    {
        // SEO: old sub-category slug or "?page=1" -> one 301 redirect to the clean URL
        $new_sub_slug = $sub_slug ? (SeoMeta::OLD_SUB_CATEGORY_SLUGS[$sub_slug] ?? null) : null;
        if ($new_sub_slug || $request->query('page') === '1') {
            $query = $request->query();
            if (($query['page'] ?? null) === '1') {
                unset($query['page']);
            }
            $sub_slug = $new_sub_slug ?: $sub_slug;
            $url = $sub_slug ? route('productlist-with-sub', [$slug, $sub_slug]) : route('productlist', $slug);
            return redirect($url . ($query ? '?' . http_build_query($query) : ''), 301);
        }

        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            abort(404, 'Category not found');
        }

        $productsQuery = Product::query();
        $productsQuery->where('status', 'active');
        $subcategory = null;

        if ($sub_slug) {
            $subcategory = Category::where('slug', $sub_slug)
                            ->where('parent_id', $category->id)
                            ->where('is_parent', 0)
                            ->first();
            if (!$subcategory) {
                abort(404, 'Subcategory not found');
            }
            $productsQuery->where('child_cat_id', $subcategory->id);
        } else {
            $productsQuery->where('cat_id', $category->id);
        }

        if ($request->has('brand') && !empty($request->brand)) {
            $slugs = SeoMeta::brandSlugs($request->brand);
            $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            if (!empty($brand_ids)) {
                $productsQuery->whereIn('brand_id', $brand_ids);
            }
        }

        if ($request->has('sortBy') && !empty($request->sortBy) && $request->sortBy != 'default') {
            if ($request->sortBy == 'title') {
                $productsQuery->orderBy('title', 'ASC');
            } elseif ($request->sortBy == 'price') {
                $productsQuery->orderByRaw(
                    "CASE WHEN JSON_VALID(`size`) THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(`size`, '$.price[0]')) AS UNSIGNED) ELSE 0 END ASC"
                );
            }
        } else {
            $productsQuery->orderBy('id', 'DESC');
        }

        if ($request->has('price') && !empty($request->price)) {
            $priceRange = explode('-', $request->price);
            $minPrice = (int) ($priceRange[0] ?? 0);
            $maxPrice = (int) ($priceRange[1] ?? 0);

            if ($minPrice || $maxPrice) {
                $productsQuery->where(function ($q) use ($minPrice, $maxPrice) {
                    $q->whereRaw('JSON_VALID(`size`) = 1')
                        ->whereRaw(
                            'COALESCE(JSON_UNQUOTE(JSON_EXTRACT(`size`, "$.price[0]")), 0) BETWEEN ? AND ?',
                            [$minPrice, $maxPrice]
                        );
                });
            }
        }

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        if ($request->has('show') && is_numeric($request->show)) {
            $perPage = (int) $request->show;
            $products = $productsQuery->paginate($perPage)->appends($request->query());
        } else {
            $products = $productsQuery->paginate(12)->appends($request->query());
        }

        // SEO: sorting, show-count, price and brand filters all point to the clean URL.
        // Only the page number is kept (page 1 = base URL).
        if ($subcategory) {
            $meta_title = $subcategory->meta_title ?: SeoMeta::title($category->title . ' ' . $subcategory->title);
            $meta_description = $subcategory->meta_description ?: SeoMeta::description(
                $subcategory->summary ?: $category->summary,
                'Shop ' . $category->title . ' ' . $subcategory->title . ' at Aashi Retail.'
            );
            $meta_image = SeoMeta::image($subcategory->photo ?: $category->photo);
            $base_url = route('productlist-with-sub', [$category->slug, $subcategory->slug]);
            $page_heading = $category->title . ' ' . $subcategory->title;
        } else {
            $meta_title = $category->meta_title ?: SeoMeta::title($category->title . ' for Men & Women');
            $meta_description = $category->meta_description ?: SeoMeta::description($category->summary, 'Shop ' . $category->title . ' at Aashi Retail.');
            $meta_image = SeoMeta::image($category->photo);
            $base_url = route('productlist', $category->slug);
            $page_heading = $category->title . ' for Men & Women';
        }
        $canonical_url = SeoMeta::listCanonical($base_url, $request->query('page'));

        // Structured data: breadcrumb (Home > Category [> Sub-category]) and the products on this page
        $crumbs = [['Home', route('home')], [$category->title, route('productlist', $category->slug)]];
        if ($subcategory) {
            $crumbs[] = [$subcategory->title, $base_url];
        }
        $schemas = [
            SeoMeta::breadcrumbSchema($crumbs),
            SeoMeta::itemListSchema($products, $products->firstItem() ?: 1),
        ];

        // Editable intro text and FAQ (sub-category uses its own, otherwise the category). Shown on page 1 only.
        $content_category = $subcategory ?: $category;
        $intro_content = $products->currentPage() == 1 ? $content_category->intro_content : null;
        $faqs = $products->currentPage() == 1 ? SeoMeta::faqList($content_category->faq) : [];
        if ($faqs) {
            $schemas[] = SeoMeta::faqSchema($faqs);
        }

        return view('frontend.pages.product-lists', compact('products', 'category', 'sub_slug', 'recent_products', 'subcategory', 'intro_content', 'faqs', 'page_heading', 'meta_title', 'meta_description', 'meta_image', 'canonical_url', 'schemas'));
    }

    public function productLists($brand_slug = null){
        \DB::enableQueryLog();
        $products = Product::query();
        $products = $products->where('status', 'active');

        if (!empty($_GET['category'])) {
            $slug = explode(',', $_GET['category']);
            $cat_ids = Category::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
            $products->whereIn('cat_id', $cat_ids);
        }

        $brand_param = $brand_slug ?: ($_GET['brand'] ?? '');
        if (!empty($brand_param)) {
            $slugs = SeoMeta::brandSlugs($brand_param);
            $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            $products->whereIn('brand_id', $brand_ids);
        }

        if (!empty($_GET['sortBy']) && $_GET['sortBy'] != 'default') {
            if ($_GET['sortBy'] == 'title') {
                $products = $products->where('status', 'active')->orderBy('title', 'ASC');
            } elseif ($_GET['sortBy'] == 'price') {
                $products = $products->orderByRaw(
                    "CASE WHEN JSON_VALID(`size`) THEN CAST(JSON_UNQUOTE(JSON_EXTRACT(`size`, '$.price[0]')) AS UNSIGNED) ELSE 0 END ASC"
                );
            }
        } else {
            $products = $products->orderBy('id', 'DESC');
        }

        if (!empty($_GET['price'])) {
            $priceRange = explode('-', $_GET['price']);
            $minPrice = (int) $priceRange[0];
            $maxPrice = (int) $priceRange[1];

            $products->where(function ($query) use ($minPrice, $maxPrice) {
                $query->whereRaw('JSON_VALID(`size`) = 1')
                    ->whereRaw(
                        'COALESCE(JSON_UNQUOTE(JSON_EXTRACT(`size`, \'$.price[0]\')), 0) BETWEEN ? AND ?',
                        [$minPrice, $maxPrice]
                    );
            });
        }

        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        $show = request()->get('show');
        if (!empty($show) && is_numeric($show)) {
            $perPage = (int) $show;
            $products = $products->where('status', 'active')->paginate($perPage)->appends(request()->query());
        } else {
            $products = $products->where('status', 'active')->paginate(12)->appends(request()->query());
        }

        // SEO: the canonical URL never carries filter parameters (brand, price, sorting, show-count).
        // /product-brand/{slug} is the brand page and has its own canonical; every /product-lists URL points to /product-lists.
        // Only "?page=N" (page 2 and above) is kept, as page 1 is the base URL.
        $page_heading = 'Aashi Retail Products';
        if ($brand_slug) {
            $brand = Brand::where('slug', $brand_slug)->first();
            if (!$brand) {
                abort(404, 'Brand not found');
            }
            $page_heading = $brand->title . ' Products';
            $canonical_url = SeoMeta::listCanonical(route('product-brand', $brand->slug), request()->query('page'));
            $meta_title = SeoMeta::title($page_heading);
            $meta_description = SeoMeta::description('', 'Shop ' . $brand->title . ' products online at Aashi Retail.');
        } else {
            $canonical_url = SeoMeta::listCanonical(route('product-lists'), request()->query('page'));
        }

        return view('frontend.pages.product-lists', compact('products', 'recent_products', 'canonical_url', 'page_heading'))
            ->with('meta_title', $meta_title ?? null)
            ->with('meta_description', $meta_description ?? null);
    }

    public function productFilter(Request $request){
            $data= $request->all();
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }
    
    public function productSearch(Request $request)
    {
        $search = $request->search;
        $recent_products = Product::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        $query = Product::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('price', 'like', "%{$search}%");
            })
            ->orderBy('id', 'DESC');

        $show = request()->get('show');
        if (!empty($show) && is_numeric($show)) {
            $perPage = (int) $show;
        } else {
            $perPage = 9;
        }

        $products = $query->paginate($perPage)->appends(request()->query());

        return view('frontend.pages.product-lists', compact('products', 'recent_products'));
    }


    public function searchSuggestions(Request $request)
    {
        $search = trim($request->get('search'));
        $categorySlug = $request->get('category');

        if (empty($search)) {
            return response()->json([]);
        }

        $query = Product::with('cat_info')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhereHas('cat_info', function ($q2) use ($search) {
                      $q2->where('title', 'like', "%{$search}%");
                  });
            });

        if (!empty($categorySlug) && strtolower($categorySlug) !== 'all') {
            $query->whereHas('cat_info', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $products = $query->limit(8)->get();

        $results = $products->map(function ($product) {
            $photo = explode(',', $product->photo);
            return [
                'title'    => $product->title,
                'code'     => $product->product_code,
                'category' => $product->cat_info->title ?? '',
                'photo'    => asset('public/' . trim($photo[0])),
                'url'      => route('product-detail', $product->slug),
            ];
        });

        return response()->json($results);
    }

    public function productBrand(Request $request){
        // Renamed brand slug: one permanent redirect to the new brand URL (query string kept)
        if (isset(SeoMeta::OLD_BRAND_SLUGS[$request->slug])) {
            $query = $request->getQueryString();
            return redirect(route('product-brand', SeoMeta::OLD_BRAND_SLUGS[$request->slug]) . ($query ? '?' . $query : ''), 301);
        }

        return $this->productLists($request->slug);
    }
    
    public function productCat(Request $request) {
        $query = $this->queryWithoutFirstPage($request);
        $url = route('productlist', $request->slug) . ($query ? '?' . $query : '');
        return redirect($url, 301);
    }

    // Query string for a redirect, without "page=1" (page 1 is the base URL, so this avoids a second redirect)
    private function queryWithoutFirstPage(Request $request)
    {
        $query = $request->query();
        if (($query['page'] ?? null) === '1') {
            unset($query['page']);
        }

        return http_build_query($query);
    }

    public function productSubCat(Request $request)
    {
        $query = $this->queryWithoutFirstPage($request);
        $sub_slug = SeoMeta::OLD_SUB_CATEGORY_SLUGS[$request->sub_slug] ?? $request->sub_slug;
        $url = route('productlist-with-sub', ['slug' => $request->slug, 'sub_slug' => $sub_slug]) . ($query ? '?' . $query : '');
        return redirect($url, 301);
    }

    public function blog(){
        $post=Post::query();
        
        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            $post->where('post_tag_id',$tag_ids);
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(9);
        }
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post=Post::getPostBySlug($slug);
        if (!$post) {
            abort(404, 'Post not found');
        }
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // SEO values for the page head
        $meta_title = $post->meta_title ?: SeoMeta::title($post->title);
        $meta_description = $post->meta_description ?: SeoMeta::description($post->summary, $post->title . ' - read more on the Aashi Retail blog.');
        $meta_image = SeoMeta::image($post->photo);
        $canonical_url = route('blog.detail', $post->slug);
        $og_type = 'article';
        $schemas = [
            SeoMeta::breadcrumbSchema([
                ['Home', route('home')],
                ['Blog', route('blog')],
                [$post->title, $canonical_url],
            ]),
        ];

        return view('frontend.pages.blog-detail', compact('meta_title', 'meta_description', 'meta_image', 'canonical_url', 'og_type', 'schemas'))->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $posts=Post::orwhere('title','like','%'.$request->search.'%')
            ->orwhere('quote','like','%'.$request->search.'%')
            ->orwhere('summary','like','%'.$request->search.'%')
            ->orwhere('description','like','%'.$request->search.'%')
            ->orwhere('slug','like','%'.$request->search.'%')
            ->orderBy('id','DESC')
            ->paginate(8);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        $post=Post::getBlogByTag($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request)
    {
        $credentials = $request->only('email','password');
        $credentials['role'] = 'user';
        $credentials['status'] = 'active';
    
        if (Auth::check()) {
            if (Auth::user()->role !== 'user') {
                Auth::logout();
            }
        }
    
        if (Auth::attempt($credentials)) {
            Session::regenerate();
            $data= $request->all();
            Session::put('user',$data['email']);
            session()->flash('success','Login successful');
            return redirect()->route('home');
        }
    
        return redirect()->route('login.form')->with('error','Invalid credentials');
    }

    public function adminLogin(){
        return view('backend.login');
    }
    public function adminLoginSubmit(Request $request)
    {
        $credentials = $request->only('email','password');
        $credentials['role'] = 'admin';
        $credentials['status'] = 'active';
    
        if (Auth::check()) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
            }
        }
    
        if (Auth::attempt($credentials)) {
            Session::regenerate();
            $data= $request->all();
            Session::put('user',$data['email']);
            session()->flash('success','Admin login successful');
            return redirect()->route('admin');
        }
    
        return redirect()->route('admin.login')->with('error','Invalid credentials');
    }

    public function logout(){
        Session::forget('user');
        if(Auth::user()->role=='admin'){
            Auth::logout();
            request()->session()->flash('success','Logout successfully');
            return redirect()->route('admin.login');
        }else{
            Auth::logout();
            request()->session()->flash('success','Logout successfully');
            return back();
        }
    }

    public function register(){
        return view('frontend.pages.register');
    }
    public function registerSubmit(Request $request){
        $registeredMessage = 'This email is already registered. Please login or use another email.';
        if (User::where('email', $request->email)->exists()) {
            return redirect()->route('register.form')
                ->withErrors(['email' => $registeredMessage])
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', $registeredMessage);
        }

        $validator = Validator::make($request->all(), [
            'name'=>'string|required|min:2',
            'email'=>'string|required|email|unique:users,email',
            'password'=>'required|min:6|confirmed',
        ], [
            'email.unique' => $registeredMessage,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data=$request->all();
        $check = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'status'=>'active'
            ]);

        Session::put('user',$data['email']);
        if($check){
            request()->session()->flash('success','Successfully registered');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Please try again!');
            return back();
        }
    }
    
    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }

    /**
     * STEP 1: Check if email is already registered (Checkout Auth Flow)
     */
    public function checkoutCheckEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $userExists = User::where('email', $request->email)->exists();

        return response()->json([
            'success'    => true,
            'registered' => $userExists,
        ]);
    }

    /**
     * STEP 2a: Login existing user during checkout + merge guest cart
     */
    public function checkoutLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|max:255|exists:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $oldSessionId = session()->getId();

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'user',
            'status'   => 'active',
        ];

        if (Auth::attempt($credentials)) {
            Session::regenerate();
            Session::put('user', $request->email);

            $guestCarts = Cart::where('session_id', $oldSessionId)->whereNull('order_id')->get();

            foreach ($guestCarts as $guestCart) {
                $sizeData     = json_decode($guestCart->size_price, true);
                $guestSize    = $sizeData['size'] ?? null;

                $existing = Cart::where('user_id', Auth::id())
                    ->where('product_id', $guestCart->product_id)
                    ->where('color_id', $guestCart->color_id)
                    ->whereNull('order_id')
                    ->whereJsonContains('size_price->size', $guestSize)
                    ->first();

                if ($existing) {
                    $newQty = $existing->quantity + $guestCart->quantity;
                    if ($guestCart->product && $guestCart->product->stock < $newQty) {
                        $newQty = $guestCart->product->stock;
                    }
                    $existing->quantity = $newQty;
                    $existing->amount   = $existing->price * $newQty;
                    $existing->save();
                    $guestCart->delete();
                } else {
                    $guestCart->session_id = null;
                    $guestCart->user_id    = Auth::id();
                    $guestCart->save();
                }
            }

            return response()->json([
                'success'      => true,
                'redirect_url' => route('checkout'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.',
        ]);
    }

    /**
     * STEP 2b: Register new user during checkout + merge guest cart
     */
    public function checkoutRegister(Request $request)
    {
        $registeredMessage = 'This email is already registered. Please login or use another email.';

        $validator = Validator::make($request->all(), [
            'email'        => 'required|email|max:255|unique:users,email',
            'name'         => 'required|string|min:2|max:255',
            'reg_password' => 'required|min:6|confirmed',
        ], [
            'email.unique'          => $registeredMessage,
            'reg_password.confirmed' => 'Password and confirm password must match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $oldSessionId = session()->getId();

        $user = User::create([
            'name'     => trim($request->name),
            'email'    => $request->email,
            'password' => Hash::make($request->reg_password),
            'role'     => 'user',
            'status'   => 'active',
        ]);

        if ($user) {
            Auth::login($user);
            Session::put('user', $request->email);

            $guestCarts = Cart::where('session_id', $oldSessionId)->whereNull('order_id')->get();

            foreach ($guestCarts as $guestCart) {
                $guestCart->session_id = null;
                $guestCart->user_id    = Auth::id();
                $guestCart->save();
            }

            return response()->json([
                'success'      => true,
                'redirect_url' => route('checkout'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Registration failed, please try again.',
        ]);
    }

    /**
     * CHECKOUT FORGOT PASSWORD - STEP 1: Send OTP (DB based, HNOWW pattern)
     */
    public function checkoutSendForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        // Purane us email ke saare OTP records hata do (fresh OTP ke liye)
        CheckoutForgotPasswordOtp::where('email', $request->email)->delete();

        $otp = rand(100000, 999999);

        CheckoutForgotPasswordOtp::create([
            'email'       => $request->email,
            'otp'         => $otp,
            'is_verified' => false,
            'expires_at'  => Carbon::now()->addMinutes(10),
        ]);

        Mail::send('emails.password-reset-otp', ['otp' => $otp], function ($message) use ($request) {
            $message->to($request->email)->subject('Your Password Reset OTP');
        });

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your registered email address.',
        ]);
    }

    /**
     * CHECKOUT FORGOT PASSWORD - STEP 2: Verify OTP
     */
    public function checkoutVerifyForgotPasswordOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $record = CheckoutForgotPasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>=', Carbon::now())
            ->latest('id')
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP. Please try again.',
            ]);
        }

        $record->is_verified = true;
        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }

    /**
     * CHECKOUT FORGOT PASSWORD - STEP 3: Reset password + remove OTP record from DB
     */
    public function checkoutResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:users,email',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $verifiedRecord = CheckoutForgotPasswordOtp::where('email', $request->email)
            ->where('is_verified', true)
            ->where('expires_at', '>=', Carbon::now())
            ->latest('id')
            ->first();

        if (!$verifiedRecord) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification expired. Please try again.',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Password use ho gaya, ab OTP record ko database se hata do
        CheckoutForgotPasswordOtp::where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully. Please login with your new password.',
        ]);
    }

    /**
     * HEADER MINI CART - live refresh partial
     */
    public function miniCart()
    {
        return response(view('frontend.partials.mini-cart')->render());
    }

    /**
     * HEADER MINI WISHLIST - live refresh partial
     */
    public function miniWishlist()
    {
        return response(view('frontend.partials.mini-wishlist')->render());
    }

}