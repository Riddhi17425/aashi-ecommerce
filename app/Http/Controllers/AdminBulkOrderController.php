<?php

namespace App\Http\Controllers;

use App\Models\BulkOrder;

class AdminBulkOrderController extends Controller
{
    public function index()
    {
        $bulkOrders = BulkOrder::latest()->paginate(20);
        return view('backend.bulkorder.index', compact('bulkOrders'));
    }
}