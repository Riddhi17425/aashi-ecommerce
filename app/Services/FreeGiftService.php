<?php

namespace App\Services;

use Helper;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FreeGiftService
{
    /**
     * Adds or removes the free gift based on the cart after discounts and coupon.
     * Returns true only when the gift was added or removed.
     */
    public static function sync(): bool
    {
        $threshold = (float) config('shop.free_gift.threshold', 2000);
        $slug      = config('shop.free_gift.product_slug');

        // Paid items (product discounts are already in "amount")
        $paidTotal = (float) self::ownerQuery()->where('is_gift', false)->sum('amount');

        // Eligibility uses the amount after the coupon
        $netTotal = $paidTotal - self::couponDiscount($paidTotal) - \Helper::firstOrderDiscount();

        $existingGift = self::ownerQuery()->where('is_gift', true)->first();
        $product      = Product::where('slug', $slug)->first();

        $qualifies = $netTotal >= $threshold && $product && $product->stock > 0;

        if (!$qualifies) {
            if ($existingGift) {
                $existingGift->delete();
                return true;
            }
            return false;
        }

        if ($existingGift) {
            return false;
        }

        $sizes = json_decode($product->size, true) ?: [];

        // Original price after product discount, shown as ₹199 on checkout and email
        $base          = (float) ($sizes['price'][0] ?? 0);
        $discount      = (float) ($product->discount ?? 0);
        $originalPrice = number_format($base - ($base * $discount / 100), 2, '.', '');

        $gift = new Cart();
        $gift->user_id    = Auth::check() ? Auth::id() : null;
        $gift->session_id = Auth::check() ? null : session()->getId();
        $gift->product_id = $product->id;
        $gift->color_id   = null;
        $gift->price      = $originalPrice;
        $gift->size_price = json_encode([
            'size'  => $sizes['size'][0] ?? null,
            'price' => $originalPrice,
        ]);
        $gift->quantity   = 1;
        $gift->amount     = 0; // free, so cart totals are unaffected
        $gift->is_gift    = true;
        $gift->save();

        return true;
    }

    /**
     * Coupon discount for this total, using the same rule as the cart.
     * Returns 0 if there is no coupon or the coupon would be removed.
     */
    private static function couponDiscount(float $total): float
    {
        $couponData = session('coupon');
        if (!$couponData) {
            return 0;
        }

        $coupon = Coupon::where('id', $couponData['id'] ?? 0)->first();
        if (!$coupon) {
            return 0;
        }

        $discount = (float) $coupon->discount($total);

        if ($discount >= $total || ($total - $discount) < 100) {
            return 0;
        }

        return $discount;
    }

    private static function ownerQuery()
    {
        $query = Cart::query()->where('order_id', null);

        if (Auth::check()) {
            return $query->where('user_id', Auth::id());
        }

        return $query->where('session_id', session()->getId());
    }
}