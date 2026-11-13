@extends('backend.layouts.master')
@section('main-content')
<div class="card">
  <div class="row">
    <div class="col-md-12">
       @include('backend.layouts.notification')
    </div>
  </div>
  <h5 class="card-header">Stock Alerts (Notify Me requests)</h5>
  <div class="card-body">
    @if(count($notifications) > 0)
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Email</th>
            <th>Current Stock</th>
            <th>Requested On</th>
          </tr>
        </thead>
        <tbody>
          @foreach($notifications as $notification)
          <tr>
            <td>{{ $notification->id }}</td>
            <td>{{ $notification->product->title ?? '-' }} ({{ $notification->product->product_code ?? '-' }})</td>
            <td>{{ $notification->email }}</td>
            <td>{{ $notification->product->stock ?? '-' }}</td>
            <td>{{ $notification->created_at->format('F d, Y h:i A') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $notifications->links() }}
    @else
      <h6 class="text-center">No requests yet.</h6>
    @endif
  </div>
</div>
@endsection
