<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\PostSize;
use App\Models\ProductImage;
use App\Models\ProductVariant;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        // $products=Product::getAllProduct();
        // return $products;
        return view('backend.product.index')->with('products',$products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $brand=Brand::get();
        $category=Category::where('is_parent',1)->whereNull('deleted_at')->get();
        $postSize=PostSize::get();
        // return $category;
        return view('backend.product.create')->with('categories',$category)->with('brands',$brand)->with('postSize',$postSize);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateProduct($request);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $manualSlug = $request->filled('slug') ? Str::slug($request->slug) : null;
        $slug       = $manualSlug ?: Str::slug($request->product_code);
        $slugTaken  = Product::where('slug', $slug)->exists();

        $data = $this->productPayload($request, $slug);
        $photos = $this->uploadPhotos($request);
        if ($photos !== null) {
            $data['photo'] = $photos;
        }

        $product = Product::create($data);

        // Auto-generated slug that already exists gets the product id appended
        if (!$manualSlug && $slugTaken) {
            $product->slug = $slug . '-' . $product->id;
            $product->save();
        }

        $colorIds = $this->saveColors($product, $request);
        $this->syncVariants($product, $request, $colorIds);

        request()->session()->flash('success', 'Product Successfully added');
        return redirect()->route('product.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand=Brand::get();
        $product=Product::findOrFail($id);
        $category=Category::where('is_parent',1)->whereNull('deleted_at')->get();
        $items=Product::where('id',$id)->get();
        $postSize=PostSize::get();
        // return $items;
        return view('backend.product.edit')->with('product',$product)
                    ->with('brands',$brand)
                    ->with('categories',$category)->with('items',$items)->with('postSize',$postSize);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = $this->validateProduct($request, $product);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $manualSlug = $request->filled('slug') ? Str::slug($request->slug) : null;
        $slug       = $manualSlug ?: Str::slug($request->product_code);
        if (!$manualSlug && Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
            $slug .= '-' . $product->id;
        }

        $data = $this->productPayload($request, $slug);
        $photos = $this->uploadPhotos($request);
        if ($photos !== null) {
            $data['photo'] = $photos;
        }
        $product->update($data);

        $this->deleteRemovedColors($product, $request->deleted_colors);
        $colorIds = $this->saveColors($product, $request);
        $this->syncVariants($product, $request, $colorIds);

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image) {
                    $image->delete();
                }
            }
        }

        request()->session()->flash('success', 'Product Successfully updated');
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        $status=$product->delete();
        
        if($status){
            request()->session()->flash('success','Product successfully deleted');
        }
        else{
            request()->session()->flash('error','Error while deleting product');
        }
        return redirect()->route('product.index');
    }
    private function validateProduct(Request $request, ?Product $product = null)
    {
        $manualSlug = $request->filled('slug') ? Str::slug($request->slug) : null;

        $slugRule = Rule::unique('products', 'slug')->whereNull('deleted_at');
        if ($product) {
            $slugRule->ignore($product->id);
        }

        return Validator::make(array_merge($request->all(), ['slug' => $manualSlug]), [
            'product_code'    => 'required|string',
            'title'           => 'nullable|string',
            'description'     => 'nullable|string',
            'cat_id'          => 'required|exists:categories,id',
            'child_cat_id'    => 'nullable|exists:categories,id',
            'status'          => 'required|in:active,inactive',
            'condition'       => 'nullable|in:default,new,hot',
            'discount'        => 'nullable|numeric',
            'hsn_code'        => 'nullable',
            'gst_percent'     => 'required|numeric|min:0|max:100',
            'size'            => 'required|array|min:1',
            'size.*'          => 'required|string|max:50|distinct',
            'price'           => 'required|array|min:1',
            'price.*'         => 'required|numeric|min:0',
            'color_id'        => 'nullable|array',
            'color_name'      => 'nullable|array',
            'color_name.*'    => 'required|string|max:100',
            'color_code'      => 'nullable|array',
            'color_code.*'    => 'nullable|string|max:20',
            'variant_qty'     => 'nullable|array',
            'variant_qty.*'   => 'nullable|array',
            'variant_qty.*.*' => 'nullable|integer|min:0',
            'slug'            => ['nullable', 'string', 'max:255', $slugRule],
        ], [
            'size.required'     => 'Please add at least one size.',
            'size.*.distinct'   => 'Each size can be added only once.',
            'price.*.numeric'   => 'Price must be a number.',
            'price.*.min'       => 'Price must be zero or greater.',
            'color_name.*.required' => 'Every colour row needs a colour name.',
            'slug.unique'       => 'This slug is already in use. Please choose another.',
        ]);
    }

    private function productPayload(Request $request, string $slug): array
    {
        return [
            'product_code'     => $request->product_code,
            'title'            => $request->title,
            'description'      => $request->description,
            'product_features' => $request->product_features,
            'size_chart'       => $request->size_chart,
            'cat_id'           => $request->cat_id,
            'child_cat_id'     => $request->child_cat_id,
            'discount'         => $request->discount,
            'hsn_code'         => $request->hsn_code,
            'gst_percent'      => $request->gst_percent,
            'brand_id'         => $request->brand_id,
            'condition'        => $request->condition,
            'status'           => $request->status,
            'size'             => json_encode([
                'size'  => $request->input('size'),
                'price' => $request->input('price'),
            ]),
            'slug'             => $slug,
            'is_featured'      => $request->input('is_featured', 0),
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
        ];
    }

    private function uploadPhotos(Request $request): ?string
    {
        if (!$request->hasFile('photo')) {
            return null;
        }

        $paths = [];
        foreach ($request->file('photo') as $sheet) {
            $filename = mt_rand(10000000000, 99999999999) . '.' . $sheet->getClientOriginalExtension();
            $sheet->move(public_path('storage/photos/1/Products'), $filename);
            $paths[] = '/storage/photos/1/Products/' . $filename;
        }

        return implode(',', $paths);
    }

    // Saves each colour row in form order and returns the colour ids in that same order
    private function saveColors(Product $product, Request $request): array
    {
        $colorIds = [];

        foreach ($request->input('color_name', []) as $i => $name) {
            $existingId = $request->input("color_id.$i");

            $color = $existingId
                ? Color::where('product_id', $product->id)->find($existingId)
                : null;

            if (!$color) {
                $color = new Color();
                $color->product_id = $product->id;
            }

            $color->color_name = $name;
            $color->color_code = $request->input("color_code.$i");
            $color->save();

            if ($request->hasFile("color_images.$i")) {
                foreach ($request->file("color_images.$i") as $file) {
                    $filename = time() . $file->getClientOriginalName();
                    $file->move(public_path('storage/products'), $filename);

                    $image = new ProductImage();
                    $image->color_id = $color->id;
                    $image->image = $filename;
                    $image->save();
                }
            }

            $colorIds[] = $color->id;
        }

        return $colorIds;
    }

    private function deleteRemovedColors(Product $product, ?string $deleted): void
    {
        if (!$deleted) {
            return;
        }

        $ids = array_filter(explode(',', $deleted));
        $colors = Color::where('product_id', $product->id)->whereIn('id', $ids)->get();

        foreach ($colors as $color) {
            foreach ($color->images as $image) {
                $image->delete();
            }
            $color->delete();
        }
    }

    // Rebuilds product_variants from the grid: one row per size and colour
    private function syncVariants(Product $product, Request $request, array $colorIds): void
    {
        // Products without colours use one column with colour_id null
        $colorIds = $colorIds ?: [null];
        $keepIds = [];

        foreach ($request->input('size', []) as $si => $size) {
            foreach ($colorIds as $ci => $colorId) {
                $variant = ProductVariant::updateOrCreate(
                    ['product_id' => $product->id, 'size' => $size, 'color_id' => $colorId],
                    ['quantity' => (int) $request->input("variant_qty.$si.$ci", 0)]
                );
                $keepIds[] = $variant->id;
            }
        }

        // Remove rows for sizes or colours that no longer exist
        ProductVariant::where('product_id', $product->id)->whereNotIn('id', $keepIds)->delete();
    }
}
