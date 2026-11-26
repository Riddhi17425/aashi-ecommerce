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
     */
    public function singleAddToCart(Request $request)
    {
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

        $qty = isset($request->quant[1]) ? (int) $request->quant[1] : 1;

        if ($qty < 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid quantity',
            ]);
        }

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

        // Price comes from the product, not from the browser.
        // Same discount calculation as the product page.
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

        // Quantity of this size and colour already in the cart
        $inCart    = (int) $this->variantCartLines($user_id, $session_id, $product, $selectedSize, $selectedColorId)->sum('quantity');
        $remaining = $available - $inCart;

        if ($qty > $remaining) {
            return response()->json([
                'status'  => false,
                'message' => $remaining > 0
                    ? "Only {$remaining} more can be added for this size and colour."
                    : 'You already have all the available stock of this size and colour in your cart.',
            ]);
        }

        $existing = $this->variantCartLines($user_id, $session_id, $product, $selectedSize, $selectedColorId)
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

        // Calculate cart count for header badge
        $cartCount = Cart::whereNull('order_id')
            ->when($user_id, fn ($q) => $q->where('user_id', $user_id), fn ($q) => $q->where('session_id', $session_id))
            ->sum('quantity');

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
            'status'      => true,
            'message'     => 'Cart updated successfully',
            'subtotal'    => Helper::totalCartPrice(),
            'gst_total'   => Helper::totalGstPrice(),
            'grand_total' => Helper::totalCartPrice() + Helper::totalGstPrice() - (session()->has('coupon') ? session('coupon')['value'] : 0),
        ]);
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