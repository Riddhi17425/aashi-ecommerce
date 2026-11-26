<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BulkOrder;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkOrderController extends Controller
{
    // Paste the Apps Script web app URL here once the Google Sheet is ready
    private const GOOGLE_SHEET_URL = '';

    public function index()
    {
        $categories = Category::orderBy('title')->get(['id', 'title']);

        return view('frontend.pages.bulk_order', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'   => 'required|string|max:191',
            'mobile'      => 'required|digits:10',
            'email'       => 'required|email|max:191',
            'address'     => 'required|string|max:500',
            'shop_name'   => 'required|string|max:191',
            'category_id' => 'required|exists:categories,id',
            'quantity'    => 'required|integer|min:1',
            'message'     => 'required|string|max:2000',
        ], [
            'mobile.digits' => 'Mobile number must be 10 digits.',
        ]);

        $category = Category::find($validated['category_id']);

        // 1. Save to the database (the main record)
        try {
            $bulkOrder = BulkOrder::create([
                'full_name'      => $validated['full_name'],
                'mobile'         => $validated['mobile'],
                'email'          => $validated['email'],
                'address'        => $validated['address'],
                'shop_name'      => $validated['shop_name'],
                'category_id'    => $category->id,
                'category_title' => $category->title,
                'quantity'       => $validated['quantity'],
                'message'        => $validated['message'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Bulk order save failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Could not submit your request. Please try again.');
        }

        // 2. Also send to Google Sheet. A failure here is logged but doesn't block the customer,
        //    because the entry is already saved in the database.
        $this->sendToSheet($bulkOrder);

        return redirect()->route('bulk-order.thankyou');
    }

    public function thankYou()
    {
        return view('frontend.pages.bulk_order_thankyou');
    }

    private function sendToSheet(BulkOrder $bulkOrder): void
    {
        if (empty(self::GOOGLE_SHEET_URL)) {
            Log::warning('Bulk order saved to database, but GOOGLE_SHEET_URL is not set.');
            return;
        }

        try {
            $response = Http::timeout(20)->post(self::GOOGLE_SHEET_URL, [
                'submitted_at' => $bulkOrder->created_at->format('Y-m-d H:i:s'),
                'full_name'    => $bulkOrder->full_name,
                'mobile'       => $bulkOrder->mobile,
                'email'        => $bulkOrder->email,
                'address'      => $bulkOrder->address,
                'shop_name'    => $bulkOrder->shop_name,
                'category'     => $bulkOrder->category_title,
                'quantity'     => $bulkOrder->quantity,
                'message'      => $bulkOrder->message,
            ]);

            if (!$response->successful()) {
                Log::error('Bulk order sheet error: ' . $response->status() . ' ' . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Bulk order sheet exception: ' . $e->getMessage());
        }
    }
}