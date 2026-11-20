<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\PostSize;
use App\Models\ProductImage;

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
        $autoSlug   = Str::slug($request->product_code);
        $manualSlug = $request->filled('slug') ? Str::slug($request->slug) : null;
        $slug       = $manualSlug ?: $autoSlug;
    
        $validator = Validator::make(array_merge($request->all(), ['slug' => $manualSlug]), [
            'product_code'=>'string|required',
            'title'=>'string|nullable',
            'description'=>'string|nullable',
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'nullable|in:default,new,hot',
            'discount'=>'nullable|numeric',
            'hsn_code' => 'nullable',
            'gst_percent' => 'required|numeric|min:0|max:100',
            'stock' => 'required|numeric',
            'size'         => 'required|array|min:1',
            'size.*'       => 'required|string|max:50',
            'price'        => 'required|array|min:1',
            'price.*'      => 'required|numeric|min:0',
            'slug' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'slug')->whereNull('deleted_at'),
            ],
        ],[
            'size.required'    => 'Please add at least one size.',
            'size.array'       => 'Size must be in a valid format.',
            'size.min'         => 'Please add at least one size.',
            'size.*.required'  => 'Size field is required.',
            'size.*.string'    => 'Size must be valid text.',
            'size.*.max'       => 'Size may not be greater than 50 characters.',
            'price.required'   => 'Please add at least one price.',
            'price.array'      => 'Price must be in a valid format.',
            'price.min'        => 'Please add at least one price.',
            'price.*.required' => 'Price field is required.',
            'price.*.numeric'  => 'Price must be a number.',
            'price.*.min'      => 'Price must be zero or greater.',
            'slug.unique'      => 'This slug is already in use. Please choose another.',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $count = Product::where('slug', $slug)->count();
    
        $size = $request->input('size');
        $size_price = $request->input('price');
        $setSizePrice = [
            'size' => $size,
            'price' => $size_price,
        ];
        $jsonData = json_encode($setSizePrice);
    
        if ($request->hasFile('photo')) {
            $sheets = $request->file('photo');
            $upload_files = [];
            foreach ($sheets as $sheet) {
                $extension = $sheet->getClientOriginalExtension();
                $filename = mt_rand(10000000000,99999999999) . '.' . $extension;
                $path = public_path('storage/photos/1/Products');
                $sheet->move($path, $filename);
                $upload_files[]= '/storage/photos/1/Products/'.$filename;
            }
            $product = [
                'product_code' => $request->product_code,
                'title' => $request->title,
                'description' => $request->description,
                'product_features' => $request->product_features ?? null,
                'size_chart' => $request->size_chart ?? null,
                'cat_id' => $request->cat_id,
                'child_cat_id' => $request->child_cat_id,
                'discount' => $request->discount,
                'hsn_code' => $request->hsn_code,
                'gst_percent' => $request->gst_percent,
                'brand_id' => $request->brand_id,
                'condition' => $request->condition,
                'stock' => $request->stock,
                'status' => $request->status,
                'size' => $jsonData,
                'slug' => $slug,
                'is_featured' => $request->input('is_featured',0),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'photo' => implode(',', $upload_files),
            ];
        } else {
            $product = [
                'product_code' => $request->product_code,
                'title' => $request->title,
                'description' => $request->description,
                'product_features' => $request->product_features ?? null,
                'size_chart' => $request->size_chart ?? null,
                'cat_id' => $request->cat_id,
                'child_cat_id' => $request->child_cat_id,
                'discount' => $request->discount,
                'hsn_code' => $request->hsn_code,
                'gst_percent' => $request->gst_percent,
                'brand_id' => $request->brand_id,
                'condition' => $request->condition,
                'stock' => $request->stock,
                'status' => $request->status,
                'size' => $jsonData,
                'slug' => $slug,
                'is_featured' => $request->input('is_featured',0),
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ];
        }
    
        $product = Product::create($product);
    
        // Only auto-append the ID when the slug was auto-generated
        if (!$manualSlug && $count > 0) {
            $slug = $slug.'-'.$product->id;
            $product->slug = $slug;
            $product->save();
        }
    
        foreach ($request->color_name as $index => $colorName) {
            $color = new Color();
            $color->product_id = $product->id;
            $color->color_name = $colorName;
            $color->color_code = $request->color_code[$index];
            $color->save();
    
            if ($request->hasFile("color_images.$index")) {
                foreach ($request->file("color_images.$index") as $file) {
                    $filename = time() . $file->getClientOriginalName();
                    $imagePath = public_path('storage/products');
                    $file->move($imagePath, $filename);
                    $image = new ProductImage();
                    $image->color_id = $color->id;
                    $image->image = $filename;
                    $image->save();
                }
            }
        }
    
        if($product){
            request()->session()->flash('success','Product Successfully added');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
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
        $product    = Product::findOrFail($id);
        $autoSlug   = Str::slug($request->product_code);
        $manualSlug = $request->filled('slug') ? Str::slug($request->slug) : null;
        $slug       = $manualSlug ?: $autoSlug;
    
        $validator = Validator::make(array_merge($request->all(), ['slug' => $manualSlug]), [
            'product_code'=>'string|required',
            'title'=>'string|nullable',
            'description'=>'string|nullable',
            'cat_id'=>'required|exists:categories,id',
            'child_cat_id'=>'nullable|exists:categories,id',
            'status'=>'required|in:active,inactive',
            'condition'=>'nullable|in:default,new,hot',
            'discount'=>'nullable|numeric',
            'hsn_code' => 'nullable',
            'gst_percent' => 'required|numeric|min:0|max:100',
            'stock' => 'required|numeric',
            'size'         => 'required|array|min:1',
            'size.*'       => 'required|string|max:50',
            'price'        => 'required|array|min:1',
            'price.*'      => 'required|numeric|min:0',
            'slug' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'slug')->ignore($product->id)->whereNull('deleted_at'),
            ],
        ],[
            'size.required'    => 'Please add at least one size.',
            'size.array'       => 'Size must be in a valid format.',
            'size.min'         => 'Please add at least one size.',
            'size.*.required'  => 'Size field is required.',
            'size.*.string'    => 'Size must be valid text.',
            'size.*.max'       => 'Size may not be greater than 50 characters.',
            'price.required'   => 'Please add at least one price.',
            'price.array'      => 'Price must be in a valid format.',
            'price.min'        => 'Please add at least one price.',
            'price.*.required' => 'Price field is required.',
            'price.*.numeric'  => 'Price must be a number.',
            'price.*.min'      => 'Price must be zero or greater.',
            'slug.unique'      => 'This slug is already in use. Please choose another.',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $size = $request->input('size');
        $size_price = $request->input('price');
        $setSizePrice = [
            'size' => $size,
            'price' => $size_price,
        ];
        $jsonData = json_encode($setSizePrice);
    
        // Only auto-append the ID when the slug was auto-generated
        $count = Product::where('slug', $slug)->where('id', '!=', $product->id)->count();
        if (!$manualSlug && $count > 0) {
            $slug = $slug.'-'.$product->id;
        }
    
        if ($request->hasFile('photo')) {
            $sheets = $request->file('photo');
            $upload_files = [];
            foreach ($sheets as $sheet) {
                $extension = $sheet->getClientOriginalExtension();
                $filename = mt_rand(10000000000,99999999999) . '.' . $extension;
                $path = public_path('storage/photos/1/Products');
                $sheet->move($path, $filename);
                $upload_files[]= '/storage/photos/1/Products/'.$filename;
            }
            $product = Product::find($id);
            $product->product_code = $request->product_code;
            $product->title = $request->title;
            $product->slug = $slug;
            $product->description = $request->description;
            $product->product_features = $request->product_features ?? null;
            $product->size_chart = $request->size_chart ?? null;
            $product->cat_id = $request->cat_id;
            $product->child_cat_id = $request->child_cat_id;
            $product->discount = $request->discount;
            $product->hsn_code = $request->hsn_code;
            $product->gst_percent = $request->gst_percent;
            $product->brand_id = $request->brand_id;
            $product->condition = $request->condition;
            $product->stock = $request->stock;
            $product->status = $request->status;
            $product->size = $jsonData;
            $product->is_featured = $request->input('is_featured',0);
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->photo = implode(',', $upload_files);
            $product->save();
        } else {
            $product = Product::find($id);
            $product->product_code = $request->product_code;
            $product->title = $request->title;
            $product->slug = $slug;
            $product->description = $request->description;
            $product->product_features = $request->product_features ?? null;
            $product->size_chart = $request->size_chart ?? null;
            $product->cat_id = $request->cat_id;
            $product->child_cat_id = $request->child_cat_id;
            $product->discount = $request->discount;
            $product->hsn_code = $request->hsn_code;
            $product->gst_percent = $request->gst_percent;
            $product->brand_id = $request->brand_id;
            $product->condition = $request->condition;
            $product->stock = $request->stock;
            $product->status = $request->status;
            $product->size = $jsonData;
            $product->is_featured = $request->input('is_featured',0);
            $product->meta_title = $request->meta_title;
            $product->meta_description = $request->meta_description;
            $product->save();
        }
    
        if(isset($request->color_name)){
            foreach ($request->color_name as $index => $colorName) {
                if (isset($request->color_id[$index])) {
                    $color = Color::find($request->color_id[$index]);
                    $color->color_name = $colorName;
                    $color->color_code = $request->color_code[$index];
                    $color->save();
                } else {
                    $color = new Color();
                    $color->product_id = $id;
                    $color->color_name = $colorName;
                    $color->color_code = $request->color_code[$index];
                    $color->save();
                }
                if ($request->hasFile("color_images.$index")) {
                    foreach ($request->file("color_images.$index") as $file) {
                        $filename = time() . $file->getClientOriginalName();
                        $imagePath = public_path('storage/products');
                        $file->move($imagePath, $filename);
                        $image = new ProductImage();
                        $image->color_id = $color->id;
                        $image->image = $filename;
                        $image->save();
                    }
                }
            }
        }
    
        if ($request->deleted_colors) {
            $deletedColorIds = explode(',', $request->deleted_colors);
            foreach ($deletedColorIds as $colorId) {
                $color = Color::find($colorId);
                if ($color) {
                    foreach ($color->images as $image) {
                        $image->delete();
                    }
                    $color->delete();
                }
            }
        }
    
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image) {
                    $image->delete();
                }
            }
        }
    
        if($product){
            request()->session()->flash('success','Product Successfully updated');
        }
        else{
            request()->session()->flash('error','Please try again!!');
        }
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
}
