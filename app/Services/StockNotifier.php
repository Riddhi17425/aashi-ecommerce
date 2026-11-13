<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Sends the "back in stock" email to visitors who used the "Notify Me" form.
 * It only reads the product. It never changes stock, prices or orders.
 */
class StockNotifier
{
    // Called after a product is saved. Does nothing unless stock went from 0 (or less) to more than 0.
    public static function sendIfBackInStock(Product $product)
    {
        if (!$product->wasChanged('stock')) {
            return;
        }

        $oldStock = (int) $product->getOriginal('stock');
        if ($oldStock > 0 || (int) $product->stock <= 0 || $product->status !== 'active') {
            return;
        }

        $waiting = ProductStockNotification::where('product_id', $product->id)
            ->whereNull('notified_at')
            ->get();

        foreach ($waiting as $notification) {
            try {
                Mail::send('emails.back-in-stock', ['product' => $product], function ($message) use ($notification, $product) {
                    $message->to($notification->email)
                        ->subject($product->title . ' is back in stock');

                    // If MAIL_FROM_ADDRESS is not set, use the SMTP login email as the sender
                    $smtpUser = config('mail.mailers.smtp.username');
                    if (!config('mail.from.address') && filter_var($smtpUser, FILTER_VALIDATE_EMAIL)) {
                        $message->from($smtpUser, config('mail.from.name'));
                    }
                });

                $notification->update(['notified_at' => now()]);
            } catch (\Throwable $e) {
                // A mail problem must never stop the admin from saving the product.
                // The request stays "not notified", so it is tried again the next time stock is restored.
                Log::error('Back in stock email failed for ' . $notification->email . ': ' . $e->getMessage());
            }
        }
    }
}
