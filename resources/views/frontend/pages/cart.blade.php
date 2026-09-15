@extends('frontend.layouts.master')
@section('title','Aashi-Ecommerce || Cart Page')
@section('main-content')
	<!-- Breadcrumbs -->
	<div class="breadcrumbs">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="bread-inner">
						<ul class="bread-list">
							<li><a href="{{('home')}}">Home<i class="ti-arrow-right"></i></a></li>
							<li class="active"><a href="">Cart</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- End Breadcrumbs -->

	<!-- Shopping Cart -->
	<div class="shopping-cart section">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<!-- Shopping Summery -->
					<table class="table shopping-summery">
						<thead>
							<tr class="main-hading">
								<th>PRODUCT</th>
								<th>NAME</th>
								<th>SIZE</th>
								<th class="text-center">UNIT PRICE</th>
								<th class="text-center">QUANTITY</th>
								<th class="text-center">TOTAL</th>
								<th class="text-center"><i class="ti-trash remove-icon"></i></th>
							</tr>
						</thead>
						<tbody id="cart_item_list">
							@if(Helper::getAllProductFromCart()->count() > 0)
								@foreach(Helper::getAllProductFromCart() as $key=>$cart)
									@php
										$price = json_decode($cart->size_price,true) ?? [];
									@endphp
									
									<tr id="cart-row-{{$cart->id}}" class="cart-item-row">
										@php 
										$photo=explode(',',$cart->product['photo']);
										@endphp
										<td class="image" data-title="No">
											@if(isset($cart->color_img) && $cart->color_img != null) 
												<img src="{{$cart->color_img}}" alt="{{ $cart->color_img }}">
											@else
												<img src="{{asset('public/'.$photo[0])}}" alt="{{asset('public/'.$photo[0])}}">
											@endif
										</td>
										<td class="product-des" data-title="Description">
											<p class="product-name"><a href="{{route('product-detail',$cart->product['slug'])}}" target="_blank">{{$cart->product['product_code']}}@if(isset($cart->color_id) && $cart->color_id != NULL) ({{optional($cart->color)->color_name}}) @endif</a></p>
											<p class="product-des">{!!($cart['summary']) !!}</p>
										</td>
										<td class="price" data-title="Price"><span>{{ $price['size']}}</span></td>

										<td class="price" data-title="Price"><span class="unit-price" data-price="{{ $cart['price'] }}">
										₹{{number_format($cart['price'],2)}}</span>
										</td>
										<td class="qty" data-title="Qty">
											<!-- Input Order -->
											<div class="input-group increment_decrement" data-cart-id="{{$cart->id}}" data-stock="{{$cart->product->stock}}">
												<div class="button minus"> 
													<button type="button" class="btn btn-primary btn-number dec_btn">
														<i class="ti-minus"></i>
													</button>
												</div>

												<input type="text" class="input-number qty_input" data-min="1" data-max="100" value="{{$cart->quantity}}" readonly>

												<div class="button plus">
													<button type="button" class="btn btn-primary btn-number inc_btn">
														<i class="ti-plus"></i>
													</button>
												</div>
											</div>
											<!--/ End Input Order -->
										</td>
										<td class="total-amount cart_single_price" data-title="Total"><span class="money row-total">₹{{$cart['amount']}}</span></td>

										<td class="action" data-title="Remove">
											<a href="javascript:void(0);" class="delete-cart-item" data-id="{{$cart->id}}">
												<i class="ti-trash remove-icon"></i>
											</a>
										</td>
									</tr>
								@endforeach
							@else
									<tr class="empty-cart-row">
										<td class="text-center" colspan="7">
											There are no any carts available. <a href="{{route('product-lists')}}" style="color:blue;">Continue shopping</a>
										</td>
									</tr>
							@endif
						</tbody>
					</table>
					<!--/ End Shopping Summery -->
				</div>
			</div>
			@if(Helper::getAllProductFromCart()->count() > 0)
			<div class="row" id="calculation-section">
				<div class="col-12">
					<!-- Total Amount -->
					<div class="total-amount">
						<div class="row">
							<div class="col-lg-8 col-md-5 col-12">
								<div class="left">
									<div class="coupon">
										{{-- coupon form remains same --}}
									</div> 
								</div>
							</div>
							<div class="col-lg-4 col-md-7 col-12">
								<div class="right">
									<ul>
										<li class="order_subtotal" data-price="{{Helper::totalCartPrice()}}">Cart Subtotal<span id="cart-subtotal">₹{{number_format(Helper::totalCartPrice(),2)}}</span></li>

										@if(session()->has('coupon'))
										<li class="coupon_price" data-price="{{Session::get('coupon')['value']}}">You Save<span>₹{{number_format(Session::get('coupon')['value'],2)}}</span></li>
										@endif
										@php
											$total_amount=Helper::totalCartPrice();
											if(session()->has('coupon')){
												$total_amount=$total_amount-Session::get('coupon')['value'];
											}
											$gstTotal = Helper::totalGstPrice();
										@endphp
										@if($gstTotal > 0)
											@php $total_amount += $gstTotal; @endphp
											<li class="last" id="gst_amount">GST Amount<span>₹{{number_format($gstTotal,2)}}</span></li>
										@endif
										<li class="last" id="order_total_price">You Pay<span id="you-pay">₹{{number_format($total_amount,2)}}</span></li>
									</ul>
									<div class="button5">
										{{-- <a class="btn" @auth href="{{route('checkout')}}" @else data-bs-toggle="modal" data-bs-target="#checkoutAuthModal" href="javascript:void(0);" @endauth>Checkout</a> --}}
										<a class="btn" @auth href="{{route('checkout')}}" @else data-toggle="modal" data-target="#checkoutAuthModal" href="javascript:void(0);" @endauth>Checkout</a>
										<a href="{{route('product-lists')}}" class="btn">Continue shopping</a>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--/ End Total Amount -->
				</div>
			</div>
			@endif
		</div>
	</div>
	<!--/ End Shopping Cart -->

	<!-- Checkout Authentication Modal (HNOWW pattern) -->
	<div class="modal fade" id="checkoutAuthModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 text-center" id="checkoutAuthTitle">Login to Checkout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
				<div class="modal-body text-center">
					<form id="checkout-auth-form">
						@csrf

						{{-- <div id="checkout-auth-alert" class="alert alert-danger d-none py-2 px-3 mb-3 text-start"></div> --}}

						<div id="step-email" class="auth-step">
							<p>Please enter your email address to continue.</p>
							<div class="form-group text-start">
								<label>Email address</label>
								<input type="email" name="email" id="checkout_email" class="form-control" required>
								<span id="alert-step-email" class="field-error d-none"></span>
							</div>
							<div class="d-flex flex-column align-items-center gap-2 mt-4">
								<button type="button" id="btn-email-next" class="btn w-100">Continue</button>
								<button type="button" class="btn-auth-secondary" data-dismiss="modal">Cancel</button>
								 <p class="mb-0 mt-2" style="font-size:13px; color:#1e293b;">
										Don't have an account?
										<a href="javascript:void(0);" id="btn-goto-signup" style="color:#5db845; font-weight:700; text-decoration:underline;">Sign Up</a>
									</p>
							</div>
						</div>

						<div id="step-login" class="auth-step d-none">
							<p>This email is already registered. Please enter your password to continue.</p>
							<div class="form-group text-start">
								<label>Password</label>
								<input type="password" name="password" id="checkout_password" class="form-control">
								<span id="alert-step-login" class="field-error d-none"></span>
							</div>
							<div class="text-end" style="margin-top:-8px; margin-bottom:10px;">
								<a href="{{ route('password.request') }}" target="_blank" style="font-size:12.5px; color:#5db845; font-weight:700; text-decoration:underline;">Forgot Password?</a>
							</div>
							<div class="d-flex flex-column align-items-center gap-2 mt-4">
								<button type="submit" id="btn-login-submit" class="btn w-100">Login & Checkout</button>
								<button type="button" id="btn-login-back" class="btn btn-link">&larr; Back</button>
							</div>
						</div>

						<div id="step-register" class="auth-step d-none">
							<p>New here? Create an account to continue checkout.</p>
							<div class="form-group text-start">
								<label>Full Name</label>
								<input type="text" name="name" id="checkout_name" class="form-control">
								<span id="alert-step-register" class="field-error d-none"></span>
							</div>
							<div class="form-group text-start">
								<label>Email Address</label>
								<input type="email" name="register_email" id="checkout_register_email" class="form-control" readonly>
							</div>
							<div class="form-group text-start">
								<label>Password (min 6 characters)</label>
								<input type="password" name="reg_password" id="checkout_reg_password" class="form-control">
							</div>
							<div class="form-group text-start">
								<label>Confirm Password</label>
								<input type="password" name="reg_password_confirmation" id="checkout_reg_password_confirmation" class="form-control">
							</div>
							<div class="d-flex flex-column align-items-center gap-2 mt-4">
								<button type="submit" id="btn-register-submit" class="btn w-100">Register & Checkout</button>
								<button type="button" id="btn-register-back" class="btn btn-link">&larr; Back</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<!-- End Checkout Authentication Modal -->

	<!-- Start Shop Services Area  -->
	<section class="shop-services section mb-4">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-12">
					<div class="single-service">
						<i class="ti-lock"></i>
						<h4>Sucure Payment</h4>
						<p>100% secure payment</p>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-12">
					<div class="single-service">
						<i class="ti-tag"></i>
						<h4>Best Peice</h4>
						<p>Guaranteed price</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Shop Newsletter -->

	<!-- Start Shop Newsletter  -->
	{{-- @include('frontend.layouts.newsletter') --}}
	<!-- End Shop Newsletter -->

@endsection
@push('styles')
	<style>
		li.shipping{
			display: inline-flex;
			width: 100%;
			font-size: 14px;
		}
		li.shipping .input-group-icon {
			width: 100%;
			margin-left: 10px;
		}
		.input-group-icon .icon {
			position: absolute;
			left: 20px;
			top: 0;
			line-height: 40px;
			z-index: 3;
		}
		.form-select {
			height: 30px;
			width: 100%;
		}
		.form-select .nice-select {
			border: none;
			border-radius: 0px;
			height: 40px;
			background: #f6f6f6 !important;
			padding-left: 45px;
			padding-right: 40px;
			width: 100%;
		}
		.list li{
			margin-bottom:0 !important;
		}
		.list li:hover{
			background:#F7941D !important;
			color:white !important;
		}
		.form-select .nice-select::after {
			top: 14px;
		}

		/* ===== CHECKOUT AUTH MODAL - CUSTOM STYLING (Bootstrap independent) ===== */
		#checkoutAuthModal.modal {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.6);
			z-index: 99999;
			display: none;
			align-items: center;
			justify-content: center;
		}
		#checkoutAuthModal.modal.show {
			display: flex !important;
		}
		#checkoutAuthModal .modal-dialog {
			max-width: 460px;
			width: 90%;
			margin: 20px auto;
		}
		#checkoutAuthModal .modal-content {
			background: #ffffff;
			border-radius: 8px;
			border: none;
			padding: 20px 25px;
			box-shadow: 0 15px 50px rgba(0,0,0,0.25);
			position: relative;
		}
		#checkoutAuthModal .modal-header {
			border-bottom: none;
			padding: 0 0 15px 0;
			position: relative;
			display: block;
			text-align: center;
		}
		#checkoutAuthModal .modal-title {
			font-size: 24px;
			font-weight: 700;
			color: #1e293b;
			margin: 0;
		}
		#checkoutAuthModal .close {
			position: absolute;
			top: -10px;
			right: -10px;
			background: #f1f5f9;
			border: none;
			border-radius: 50%;
			width: 32px;
			height: 32px;
			font-size: 20px;
			line-height: 1;
			color: #475569;
			opacity: 1;
		}
		#checkoutAuthModal .modal-body {
			padding: 0;
		}
		#checkoutAuthModal .form-group {
			margin-bottom: 12px;
		}
		#checkoutAuthModal .form-group label {
			display: block;
			font-size: 13px;
			font-weight: 600;
			color: #475569;
			margin-bottom: 6px;
			text-align: left;
		}
		#checkoutAuthModal .form-control {
			width: 100%;
			padding: 10px 14px;
			border: 1px solid #cbd5e1;
			border-radius: 6px;
			font-size: 14px;
		}
		#checkoutAuthModal .btn.w-100 {
			display: block;
			width: 100%;
			background: #111827;
			color: #ffffff;
			border: none;
			padding: 12px;
			border-radius: 6px;
			font-weight: 600;
			font-size: 14px;
			cursor: pointer;
			text-transform: uppercase;
		}
		#checkoutAuthModal .btn.w-100:hover {
			background: #5db845;
		}
		#checkoutAuthModal .btn-auth-secondary,
		#checkoutAuthModal .btn-link {
			background: none;
			border: none;
			color: #64748b;
			font-size: 13px;
			text-decoration: underline;
			cursor: pointer;
			padding: 8px;
		}
		#checkoutAuthModal p {
			text-align: center;
			color: #64748b;
			font-size: 13.5px;
			margin-bottom: 20px;
		}
		#checkoutAuthModal .d-none {
			display: none !important;
		}
		#checkoutAuthModal .field-error {
			display: block;
    color: #dc2626;
    font-size: 12.5px;
    font-weight: 500;
    margin-top: 6px;
    text-align: left;
		}
	</style>
@endpush
@push('scripts')
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> --}}
<script src="{{ asset('public/frontend/js/sweetalert.min.js') }}"></script>
	<script src="{{asset('public/frontend/js/nice-select/js/jquery.nice-select.min.js')}}"></script>
	<script src="{{ asset('public/frontend/js/select2/js/select2.min.js') }}"></script>
	<script>
		$(document).ready(function() { $("select.select2").select2(); });
  		$('select.nice-select').niceSelect();
	</script>
	<script>
	window.appCsrfToken = "{{ csrf_token() }}";
	window.appRoutes = {
		cartUpdate: "{{ route('cart.update') }}",
		cartDeleteBase: "{{ url('cart-delete') }}",
		checkoutCheckEmail: "{{ route('checkout.check-email') }}",
		checkoutLogin: "{{ route('checkout.login') }}",
		checkoutRegister: "{{ route('checkout.register') }}"
	};
	window.appData = {
		emptyCartImage: "{{ asset('public/frontend/img/empty-cart.png') }}",
		homeUrl: "{{ route('home') }}",
		productListUrl: "{{ route('product-lists') }}"
	};
	</script>
	<script src="{{ asset('public/frontend/js/cart-ajax.js') }}"></script>

@endpush