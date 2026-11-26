@extends('backend.layouts.master')
@section('title', 'Bulk Orders')

@section('main-content')
<div class="container-fluid">
    <h3 class="mb-3">Bulk Orders</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Full name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Shop name</th>
                    <th>Address</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bulkOrders as $order)
                    <tr>
                        <td>{{ $bulkOrders->firstItem() + $loop->index }}</td>
                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->mobile }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->shop_name }}</td>
                        <td>{{ $order->address }}</td>
                        <td>{{ $order->category_title }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->message }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No bulk orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $bulkOrders->links() }}
</div>
@endsection