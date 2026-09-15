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

        // GUEST vs LOGGED-IN
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        $already_wishlist = Wishlist::where('cart_id', null);
        if ($user_id) {
            $already_wishlist = $already_wishlist->where('user_id', $user_id);
        } else {
            $already_wishlist = $already_wishlist->where('session_id', $session_id);
        }
        if (isset($request->color_id) && $request->color_id != null) {
            $already_wishlist = $already_wishlist->where('color_id', $request->color_id);
        }
        $already_wishlist = $already_wishlist->where('product_id', $product->id)->first();

        if ($already_wishlist) {
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
        } else {
            $sizeData = json_decode($product->size, true);
            $price = $product->price;
            if (!isset($product->price) && $product->price == null) {
                $price = $sizeData['price'][0];
            }
            $wishlist = new Wishlist;
            $wishlist->user_id     = $user_id;
            $wishlist->session_id  = $session_id;
            $wishlist->product_id  = $product->id;
            $wishlist->price       = ($price-($price*$product->discount)/100);
            $wishlist->quantity    = 1;
            $wishlist->amount      = $wishlist->price*$wishlist->quantity;
            $wishlist->color_id    = $request->color_id ?? null;

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

        if (!$wishlist) {
            request()->session()->flash('error','Error please try again');
            return back();
        }

        // Ownership check (guest / user dono ke liye)
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        if ($user_id && $wishlist->user_id != $user_id) {
            request()->session()->flash('error','Unauthorized action.');
            return back();
        }
        if (!$user_id && $wishlist->session_id != $session_id) {
            request()->session()->flash('error','Unauthorized action.');
            return back();
        }

        $wishlist->delete();
        request()->session()->flash('success','Wishlist successfully removed');
        return back();
    }

    public function check(Request $request)
    {
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        $query = Wishlist::where('product_id', $request->product_id)
                    ->where('color_id', $request->color_id);

        if ($user_id) {
            $query->where('user_id', $user_id);
        } else {
            $query->where('session_id', $session_id);
        }

        return response()->json([
            'wishlisted' => $query->exists()
        ]);
    }
}