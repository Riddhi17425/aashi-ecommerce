<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\Color;
use App\Models\Coupon;
use Illuminate\Support\Str;
use Helper;
class CartController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    /**
     * Add product to cart via AJAX (Guest + Logged-in both supported)
     * : session_id for guest, user_id for logged-in
     */
    public function singleAddToCart(Request $request){
        $validator = \Validator::make($request->all(), [
            'slug'  => 'required',
            'quant' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $product = Product::where('slug', $request->slug)->first();

        if (empty($product)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Product',
            ]);
        }

        $selectedSize      = $request->selected_size ?? null;
        $selectedPrice     = $request->selected_price ?? null;
        $selectedColor     = $request->selected_color ?? null;
        $qty               = isset($request->quant[1]) ? (int) $request->quant[1] : 1;

        if ($qty < 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid quantity',
            ]);
        }

        if ($product->stock < $qty || $product->stock <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Out of stock, You can add other products.',
            ]);
        }

        // GUEST vs LOGGED-IN 
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        $already_cart = Cart::where('order_id', null)->where('product_id', $product->id);

        if ($user_id) {
            $already_cart = $already_cart->where('user_id', $user_id);
        } else {
            $already_cart = $already_cart->where('session_id', $session_id);
        }

        if ($selectedColor != null) {
            $productColor = Color::where('product_id', $product->id)->where('id', $selectedColor)->exists();
            if (!$productColor) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid Color Selected',
                ]);
            }
            $already_cart = $already_cart->where('color_id', $selectedColor);
        }

        $already_cart = $already_cart->whereJsonContains('size_price->size', $selectedSize)
                        ->whereJsonContains('size_price->price', $selectedPrice)
                        ->first();

        if ($already_cart) {
            $newQty = $already_cart->quantity + $qty;

            if ($product->stock < $newQty || $product->stock <= 0) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Stock not sufficient!',
                    'data'    => [
                        'available_stock' => $product->stock,
                        'already_in_cart' => $already_cart->quantity,
                    ],
                ]);
            }

            $already_cart->quantity = $newQty;
            $already_cart->amount   = $selectedPrice * $newQty;
            $already_cart->save();

        } else {
            $data = [
                'size'  => $selectedSize,
                'price' => $selectedPrice,
            ];

            $cart              = new Cart;
            $cart->user_id     = $user_id;
            $cart->session_id  = $session_id;
            $cart->product_id  = $product->id;
            $cart->color_id    = $selectedColor;
            $cart->price       = $selectedPrice;
            $cart->size_price  = json_encode($data);
            $cart->quantity    = $qty;
            $cart->amount      = $selectedPrice * $qty;
            $cart->save();
        }

        // Calculate cart count for header badge
        $cartCountQuery = Cart::where('order_id', null);
        if ($user_id) {
            $cartCountQuery->where('user_id', $user_id);
        } else {
            $cartCountQuery->where('session_id', $session_id);
        }
        $cartCount = $cartCountQuery->sum('quantity');

        return response()->json([
            'status'     => true,
            'message'    => 'Product successfully added to cart.',
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Update cart quantity via AJAX (Guest + Logged-in both supported)
     */
    public function cartUpdate(Request $request){
        if (!$request->quant) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart Invalid!',
            ]);
        }

        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        $error   = [];
        $success = '';

        foreach ($request->quant as $k => $quant) {
            $id   = $request->qty_id[$k];
            $cart = Cart::find($id);

            // Ownership check (guest / user dono ke liye)
            if (!$cart) {
                $error[] = 'Cart item not found!';
                continue;
            }
            if ($user_id && $cart->user_id != $user_id) {
                continue; // skip - not this user's cart item
            }
            if (!$user_id && $cart->session_id != $session_id) {
                continue; // skip - not this session's cart item
            }

            if ($quant > 0 && $cart) {
                if ($cart->product->stock < $quant) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Out of stock',
                    ]);
                }

                $cart->quantity = ($cart->product->stock > $quant) ? $quant : $cart->product->stock;

                if ($cart->product->stock <= 0) continue;

                $cart_stored_price = json_decode($cart->size_price, true);
                $stored_price       = $cart_stored_price['price'] ?? 0;

                $cart->amount = $stored_price * $quant;
                $cart->save();
                $success = 'Cart successfully updated!';
            } else {
                $error[] = 'Cart Invalid!';
            }
        }

        /* ===============================
            RE-VALIDATE COUPON HERE
        =============================== */
        if (session()->has('coupon')) {
            $totalQuery = Cart::where('order_id', null);
            if ($user_id) {
                $totalQuery->where('user_id', $user_id);
            } else {
                $totalQuery->where('session_id', $session_id);
            }
            $total_price = $totalQuery->sum('amount');

            $couponData = session('coupon');
            $coupon = Coupon::where('id', $couponData['id'])->first();

            if (!$coupon) {
                session()->forget('coupon');
                return response()->json([
                    'status'  => true,
                    'message' => 'Coupon removed (invalid)',
                ]);
            }

            $discountAmount = $coupon->discount($total_price);
            if ($discountAmount >= $total_price || ($total_price - $discountAmount) < 100) {
                session()->forget('coupon');
                return response()->json([
                    'status'  => true,
                    'message' => 'Coupon removed because cart total no longer meets minimum amount',
                ]);
            }
            session()->put('coupon.value', $discountAmount);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart updated successfully',
            'subtotal' => Helper::totalCartPrice(),
            'gst_total' => Helper::totalGstPrice(),
            'grand_total' => Helper::totalCartPrice() + Helper::totalGstPrice() - (session()->has('coupon') ? session('coupon')['value'] : 0),        ]);
    }

    /**
     * Delete cart item via AJAX (Guest + Logged-in both supported)
     */
    public function cartDelete(Request $request, $id = null){
        $cartId = $id ?? $request->cart_id;
        $cart   = Cart::find($cartId);

        if (!$cart) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart item not found.',
            ]);
        }

        // Ownership check (guest / user dono ke liye)
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        if ($user_id && $cart->user_id != $user_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action.',
            ]);
        }
        if (!$user_id && $cart->session_id != $session_id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action.',
            ]);
        }

        $cart->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Item removed from cart.',
        ]);
    }

    /**
     * Checkout page (Auth protected - middleware('user') lagaya hai route me)
     */
    public function checkout(Request $request){
        return view('frontend.pages.checkout');
    }
}