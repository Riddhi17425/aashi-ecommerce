<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStockNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * "Notify me when back in stock" requests.
 * Only stores the visitor's email. It does not read or change stock, orders or prices.
 */
class StockNotificationController extends Controller
{
    // Visitor submits the form on an out-of-stock product page.
    public function store(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), ['email' => 'required|email|max:191']);
        if ($validator->fails()) {
            session()->flash('error', 'Please enter a valid email address.');
            return redirect()->back();
        }

        // Same email twice for the same product is saved only once.
        ProductStockNotification::firstOrCreate([
            'product_id' => $product->id,
            'email' => strtolower(trim($request->email)),
        ]);

        session()->flash('success', 'Thank you! We will email you when this product is back in stock.');
        return redirect()->back();
    }

    // Admin: list of requests.
    public function index()
    {
        $notifications = ProductStockNotification::with('product')->orderBy('id', 'DESC')->paginate(50);

        return view('backend.stock-notification.index', compact('notifications'));
    }
}
