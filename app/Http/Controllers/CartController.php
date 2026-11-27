<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Services\FreeGiftService;
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
     */
    public function singleAddToCart(Request $request)
    {
        $maxPerOrder = config('shop.max_qty_per_order', 1000);

        $validator = \Validator::make($request->all(), [
            'slug'    => 'required',
            'quant.1' => 'required|integer|min:1|max:' . $maxPerOrder,
        ], [
            'quant.1.required' => 'Please select a quantity.',
            'quant.1.integer'  => 'Quantity must be a whole number.',
            'quant.1.min'      => 'Quantity must be at least 1.',
            'quant.1.max'      => "You can order a maximum of {$maxPerOrder} units of this product at a time.",
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

        $qty = (int) $request->input('quant.1');

        $selectedSize    = $request->selected_size ?: null;
        $selectedColorId = $request->selected_color ? (int) $request->selected_color : null;

        // The size must be one of this product's sizes
        $sizeData   = json_decode($product->size, true);
        $validSizes = $sizeData['size'] ?? [];
        $index      = $selectedSize !== null ? array_search($selectedSize, $validSizes, true) : false;

        if ($index === false || !isset($sizeData['price'][$index])) {
            return response()->json([
                'status'  => false,
                'message' => 'Please select a valid size.',
            ]);
        }

        if ($selectedColorId && !Color::where('product_id', $product->id)->where('id', $selectedColorId)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Color Selected',
            ]);
        }

        // Price comes from the product, not from the browser (same discount as the product page)
        $basePrice     = (float) $sizeData['price'][$index];
        $finalPrice    = $basePrice - ($basePrice * (float) $product->discount) / 100;
        $selectedPrice = number_format($finalPrice, 2, '.', '');

        // GUEST vs LOGGED-IN
        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        // Stock for this exact size and colour
        $available = $product->variantQuantity((string) $selectedSize, $selectedColorId);

        if ($available <= 0) {
            return response()->json([
                'status'  => false,
                'message' => 'This size and colour is out of stock.',
            ]);
        }

        // Quantity of this size and colour already in the cart, and the most allowed in total
        $maxAllowed = min($maxPerOrder, $available);
        $inCart     = (int) $this->variantCartLines($user_id, $session_id, $product, $selectedSize, $selectedColorId)->sum('quantity');
        $remaining  = $maxAllowed - $inCart;

        if ($qty > $remaining) {
            return response()->json([
                'status'  => false,
                'message' => $remaining > 0
                    ? "You can add only {$remaining} more unit(s) of this size and colour."
                    : 'You already have the maximum quantity of this size and colour in your cart.',
                'data'    => [
                    'available_stock' => $available,
                    'already_in_cart' => $inCart,
                ],
            ]);
        }

        // Existing paid line with the same size, colour and price (free gift lines are never merged)
        $existing = $this->variantCartLines($user_id, $session_id, $product, $selectedSize, $selectedColorId)
                        ->where('is_gift', false)
                        ->whereJsonContains('size_price->price', $selectedPrice)
                        ->first();

        if ($existing) {
            $newQty = $existing->quantity + $qty;

            $existing->quantity = $newQty;
            $existing->amount   = $selectedPrice * $newQty;
            $existing->save();

        } else {
            $data = [
                'size'  => $selectedSize,
                'price' => $selectedPrice,
            ];

            $cart              = new Cart;
            $cart->user_id     = $user_id;
            $cart->session_id  = $session_id;
            $cart->product_id  = $product->id;
            $cart->color_id    = $selectedColorId;
            $cart->price       = $selectedPrice;
            $cart->size_price  = json_encode($data);
            $cart->quantity    = $qty;
            $cart->amount      = $selectedPrice * $qty;
            $cart->save();
        }

        FreeGiftService::sync();

        // Cart count for header badge
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
    public function cartUpdate(Request $request)
    {
        if (!$request->quant) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart Invalid!',
            ]);
        }

        $user_id    = Auth::check() ? Auth::id() : null;
        $session_id = Auth::check() ? null : session()->getId();

        // 1) Check every line first. If one fails, nothing is saved.
        $lines = [];

        foreach ($request->quant as $k => $quant) {
            $quant = (int) $quant;
            $cart  = isset($request->qty_id[$k]) ? Cart::with('product')->find($request->qty_id[$k]) : null;

            if (!$cart || !$cart->product) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Cart item not found!',
                ]);
            }
            if ($cart->is_gift) {
                continue; // the free gift quantity can't be changed
            }

            // Ownership check (guest / user)
            if (($user_id && $cart->user_id != $user_id) || (!$user_id && $cart->session_id != $session_id)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized action.',
                ]);
            }

            if ($quant < 1) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Cart Invalid!',
                ]);
            }

            $stored = json_decode($cart->size_price, true);
            $size   = $stored['size'] ?? null;

            $available = $cart->product->variantQuantity((string) $size, $cart->color_id);

            // Other open cart lines for the same size and colour (not this line)
            $others    = (int) $this->variantCartLines($user_id, $session_id, $cart->product, $size, $cart->color_id)
                                    ->where('id', '!=', $cart->id)
                                    ->sum('quantity');
            $remaining = $available - $others;

            if ($quant > $remaining) {
                return response()->json([
                    'status'  => false,
                    'message' => $remaining > 0
                        ? "Only {$remaining} available for this size and colour."
                        : 'This size and colour is out of stock.',
                ]);
            }

            $lines[] = [$cart, $quant, $stored['price'] ?? 0];
        }

        // 2) Save
        foreach ($lines as [$cart, $quant, $price]) {
            $cart->quantity = $quant;
            $cart->amount   = $price * $quant;
            $cart->save();
        }

        $giftChanged = FreeGiftService::sync();

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
            $coupon     = Coupon::where('id', $couponData['id'])->first();

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
            'status'               => true,
            'message'              => 'Cart updated successfully',
            'subtotal'             => Helper::totalCartPrice(),
            'gst_total'            => Helper::totalGstPrice(),
            'first_order_discount' => Helper::firstOrderDiscount(),
            'grand_total'          => Helper::totalCartPrice() + Helper::totalGstPrice()
                - (session()->has('coupon') ? session('coupon')['value'] : 0)
                - Helper::firstOrderDiscount(),
            'gift_changed'         => $giftChanged,
        ]);
    }

    /**
     * Delete cart item via AJAX (Guest + Logged-in both supported)
     */
    public function cartDelete(Request $request, $id = null)
    {
        $cartId = $id ?? $request->cart_id;
        $cart   = Cart::find($cartId);

        if (!$cart) {
            return response()->json([
                'status'  => false,
                'message' => 'Cart item not found.',
            ]);
        }

        // Ownership check (guest / user)
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

        if ($cart->is_gift) {
            return response()->json([
                'status'  => false,
                'message' => 'The free gift cannot be removed from the cart.',
            ]);
        }

        $cart->delete();

        $couponRemoved = false;
        if (session()->has('coupon')) {
            $totalQuery = Cart::where('order_id', null);
            if ($user_id) {
                $totalQuery->where('user_id', $user_id);
            } else {
                $totalQuery->where('session_id', $session_id);
            }
            $total_price = $totalQuery->sum('amount');

            $coupon         = Coupon::find(session('coupon')['id']);
            $discountAmount = $coupon ? $coupon->discount($total_price) : 0;

            if (!$coupon || $discountAmount >= $total_price || ($total_price - $discountAmount) < 100) {
                session()->forget('coupon');
                $couponRemoved = true;
            } else {
                session()->put('coupon.value', $discountAmount);
            }
        }

        FreeGiftService::sync();

        return response()->json([
            'status'               => true,
            'message'              => $couponRemoved
                ? 'Item removed. Coupon removed because cart total no longer meets minimum amount.'
                : 'Item removed from cart.',
            'coupon_removed'       => $couponRemoved,
            'subtotal'             => Helper::totalCartPrice(),
            'gst_total'            => Helper::totalGstPrice(),
            'first_order_discount' => Helper::firstOrderDiscount(),
            'grand_total'          => Helper::totalCartPrice() + Helper::totalGstPrice()
                - (session()->has('coupon') ? session('coupon')['value'] : 0)
                - Helper::firstOrderDiscount(),
        ]);
    }

    /**
     * Checkout page (Auth protected - middleware('user') lagaya hai route me)
     */
    public function checkout(Request $request){
        FreeGiftService::sync();
        return view('frontend.pages.checkout');
    }

    /**
     * Open cart lines (not yet ordered) for one product, size and colour,
     * for the current guest or logged-in user.
     */
    private function variantCartLines(?int $userId, ?string $sessionId, Product $product, ?string $size, ?int $colorId)
    {
        $query = Cart::whereNull('order_id')->where('product_id', $product->id);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        if ($colorId) {
            $query->where('color_id', $colorId);
        } else {
            $query->whereNull('color_id');
        }

        if ($size !== null && $size !== '') {
            $query->whereJsonContains('size_price->size', $size);
        }

        return $query;
    }
}