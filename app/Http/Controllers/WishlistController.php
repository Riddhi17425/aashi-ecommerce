<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use Helper;

class WishlistController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function wishlist(Request $request){
        $isAjax = $request->ajax() || $request->wantsJson();

        if (empty($request->slug)) {
            if ($isAjax) return response()->json(['status'=>false,'message'=>'Invalid Products']);
            request()->session()->flash('error','Invalid Products');
            return back();
        }        
        $product = Product::where('slug', $request->slug)->first();
        if (empty($product)) {
            if ($isAjax) return response()->json(['status'=>false,'message'=>'Invalid Products']);
            request()->session()->flash('error','Invalid Products');
            return back();
        }

        $already_wishlist = Wishlist::where('user_id', auth()->user()->id)->where('cart_id',null);
        if(isset($request->color_id) && $request->color_id != null){
            $already_wishlist = $already_wishlist->where('color_id', $request->color_id);
        }
        $already_wishlist = $already_wishlist->where('product_id', $product->id)->first();

        if($already_wishlist) {
            $already_wishlist->delete();
            if ($isAjax) {
                return response()->json([
                    'status'=>true,
                    'message'=>'Product removed from wishlist',
                    'wishlisted'=>false,
                    'wishlist_count'=>Helper::wishlistCount()
                ]);
            }
            request()->session()->flash('success','Product Removed to wishlist');
            return back();
        }else{
            $sizeData = json_decode($product->size, true);
            $price = $product->price;
            if(!isset($product->price) && $product->price == null){
                $price = $sizeData['price'][0];
            }
            $wishlist = new Wishlist;
            $wishlist->user_id = auth()->user()->id;
            $wishlist->product_id = $product->id;
            $wishlist->price = ($price-($price*$product->discount)/100);
            $wishlist->quantity = 1;
            $wishlist->amount=$wishlist->price*$wishlist->quantity;
            $wishlist->color_id=$request->color_id ?? null;

            if ($wishlist->product->stock < $wishlist->quantity || $wishlist->product->stock <= 0) {
                if ($isAjax) return response()->json(['status'=>false,'message'=>'Stock not sufficient!']);
                return back()->with('error','Stock not sufficient!.');
            }
            $wishlist->save();

            if ($isAjax) {
                return response()->json([
                    'status'=>true,
                    'message'=>'Product successfully added to wishlist',
                    'wishlisted'=>true,
                    'wishlist_count'=>Helper::wishlistCount()
                ]);
            }
        }
        request()->session()->flash('success','Product successfully added to wishlist');
        return back();       
    }
    
    public function wishlistDelete(Request $request){
        $wishlist = Wishlist::find($request->id);
        if ($wishlist) {
            $wishlist->delete();
            request()->session()->flash('success','Wishlist successfully removed');
            return back();  
        }
        request()->session()->flash('error','Error please try again');
        return back();       
    }   
    
    public function check(Request $request)
    {
        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->exists();

        return response()->json([
            'wishlisted' => $exists
        ]);
    }
}