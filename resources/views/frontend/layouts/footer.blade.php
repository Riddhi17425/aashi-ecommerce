<!-- Start Footer Area --><!-- Footer layout styles -->
<style>
	/* Less empty space in the footer */
	.footer .footer-top { padding: 30px 0 15px; }
	.footer .single-footer h4 { margin-bottom: 18px; }
	.footer .about .logo { margin-bottom: 0; }
	.footer .about .call { margin-top: 12px; }
	.footer .copyright .inner { padding: 14px 0; }
	.footer .footer-contact { margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.15); }
	.footer .footer-contact h4 { margin-bottom: 12px; }
	.footer .footer-contact .contact ul { display: flex; flex-wrap: wrap; gap: 8px 40px; margin-top: 0; }
	.footer .footer-contact .contact ul li { margin-bottom: 0; }
	.footer .footer-contact .contact ul li:first-child { flex: 1 1 100%; }
	.footer .footer-contact .contact ul li, .footer .footer-contact .contact ul li a { font-size: 14px; }
	.footer .footer-contact .social-media { margin-top: 18px; }
	.footer .footer-contact .contact ul li i { color: #5db845; width: 18px; margin-right: 8px; text-align: center; font-size: 15px; }
	.footer .footer-contact .contact ul li a { display: inline; }
	@media only screen and (max-width: 991px) {
		.footer .single-footer { padding: 0; }
		.footer .about { padding-right: 0; margin-bottom: 20px; }
	}
	@media only screen and (max-width: 767px) {
		.footer .single-footer.links { margin-bottom: 18px; }
		.footer .single-footer h4 { margin-bottom: 14px; }
	}
</style>
	<footer class="footer">
		<!-- Footer Top -->
		<div class="footer-top section">
			<div class="container">
				<div class="row">
					<div class="col-lg-4 col-md-12 col-12">
						<!-- Single Widget -->
						@php
							$settings=DB::table('settings')->get();
						@endphp
						<div class="single-footer about">
							<div class="logo">
								<a href="{{route('home')}}"><img src="@foreach($settings as $data) {{asset('public'.$data->logo)}} @endforeach" alt="#" width="250px"></a>
							</div>
							
							<p class="text">@foreach($settings as $data) {!! $data->short_des !!} @endforeach</p>
							<p class="call">Got Question? Call us 24/7<span>@foreach($settings as $data)
    <a href="tel:{{ $data->phone }}">{{ $data->phone }}</a>
@endforeach</span></p>
						</div>
						<!-- End Single Widget -->
					</div>
					<div class="col-lg-8 col-md-12 col-12">
						<div class="row">
						<div class="col-lg-3 col-md-3 col-6">
							<!-- Single Widget: Categories -->
							<div class="single-footer links">
								<h4>Categories</h4>
								<ul>
									@php
	                                    $menuCategory=App\Models\Category::where('status','active')->where('is_parent', 1)->get();
	                                @endphp
									@if($menuCategory)
	                                    @foreach($menuCategory as $cat)
	                                        <li><a href="{{route('productlist',$cat->slug)}}">{{ $cat->title }}</a>
	                                        </li>									
	                                    @endforeach
	                                @endif
								</ul>
							</div>
							<!-- End Single Widget -->
						</div>
						<div class="col-lg-3 col-md-3 col-6">
							<!-- Single Widget: Brands -->
							<div class="single-footer links">
								<h4>Brands</h4>
								<ul>
									@foreach(App\Models\Brand::where('status','active')->orderBy('title')->get() as $brand)
										<li><a href="{{ route('product-brand', $brand->slug) }}">{{ $brand->title }}</a></li>
									@endforeach
								</ul>
							</div>
							<!-- End Single Widget -->
						</div>
						<div class="col-lg-3 col-md-3 col-6">
							<!-- Single Widget -->
							<div class="single-footer links">
								<h4>Links</h4>
								<ul>
								    <!--<li><a href="{{route('about-us')}}">Terms & Conditions</a></li>-->
									<li><a href="{{route('blog')}}">Blogs</a></li>
									{{-- <li><a href="#">Faq</a></li> --}}
									<li><a href="{{route('contact')}}">Contact Us</a></li>
									<li><a href="{{route('privacy-policy')}}">Privacy Policy</a></li>
									<li><a href="{{route('terms-conditions')}}">Terms & Conditions</a></li>
									<!--<li><a href="{{route('return-refund-policy')}}">Return & Refund Policy</a></li>-->
									<!--<li><a href="{{route('exchange-policy')}}">Exchange Policy</a></li>-->
									{{-- <li><a href="#">Help</a></li> --}}
								</ul>
							</div>
							<!-- End Single Widget -->
						</div>
						<div class="col-lg-3 col-md-3 col-6">
							<!-- Single Widget -->
							<div class="single-footer links">
								<h4>Customer Service</h4>
								<ul>
									{{-- <li><a href="#">Payment Methods</a></li>
									<li><a href="#">Money-back</a></li>
									<li><a href="#">Returns</a></li>
									<li><a href="#">Shipping</a></li>
									<li><a href="#">Privacy Policy</a></li> --}}
									@if(auth()->check())
										<li><a href="{{ route('order.track') }}">Track Orders</a></li>
										<li><a href="{{route('myorders')}}">My Orders</a></li>
										<li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
										<li><a href="{{ route('cart') }}">My Cart Products</a></li>
									@else
										<li><a href="{{ route('order.track') }}">Track Orders</a></li>
										<li><a href="{{ route('login.form') }}">My Orders</a></li>
										<li><a href="{{ route('wishlist') }}">My Wishlist</a></li>
										<li><a href="{{ route('cart') }}">My Cart Products</a></li>
									@endif
								</ul>
							</div>
							<!-- End Single Widget -->
						</div>
						</div>
						<!-- Contact details -->
						<div class="footer-contact">
							<!-- Single Widget -->
							<div class="single-footer social">
								<h4>Get In Touch</h4>
								<!-- Single Widget -->
								<div class="contact">
									<ul>
										<li><i class="fa fa-map-marker"></i>@foreach($settings as $data) {{$data->address}} @endforeach</li>
										<li><i class="fa fa-envelope"></i>@foreach($settings as $data) <a href="mailto:{{ $data->email }}">
	                                        {{ $data->email }}
	                                    </a> @endforeach</li>
										<li><i class="fa fa-phone"></i>@foreach($settings as $data) <a href="tel:{{ preg_replace('/\s+/', '', $data->phone) }}">
	                                        {{ $data->phone }}
	                                    </a>  @endforeach</li>
									</ul>
								</div>
								<!-- End Single Widget -->
								<div class="social-media">
									<a target="_blank" href="https://www.facebook.com/aashigroupofcompanies"><i class="ti-facebook"> </i></a>
									<a target="_blank" href="https://www.instagram.com/aashi_rainwear_official/"><i class="ti-instagram"> </i></a>
									<a target="_blank" href="https://www.youtube.com/@newaashirainwear2779"><i class="ti-youtube"> </i></a>
									<a target="_blank" href="https://in.linkedin.com/company/aashigroup/"><i class="ti-linkedin"> </i></a>
								</div>
							</div>
							<!-- End Single Widget -->
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- End Footer Top -->
		<div class="copyright">
			<div class="container">
				<div class="inner">
					<div class="row">
						<div class="col-12 text-center">
							<div class="center">
								<p>Copyright © {{date('Y')}} Aashi Group  -  All Rights Reserved.</p>
							</div>
						</div>
						{{-- <div class="col-lg-6 col-12">
							<div class="right">
								<img src="{{asset('public/backend/img/payments.png')}}" alt="#">
							</div>
						</div> --}}
					</div>
				</div>
			</div>
		</div>

		{{-- WhatsApp floating button --}}
		<style>
			.wa-float-btn {
				position: fixed;
				right: 20px;
				bottom: 20px;
				width: 56px;
				height: 56px;
				border-radius: 50%;
				border: 0;
				background: #25D366;
				color: #fff;
				font-size: 28px;
				display: flex;
				align-items: center;
				justify-content: center;
				box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
				cursor: pointer;
				z-index: 9998;
				transition: transform 0.2s ease;
			}
			.wa-float-btn:hover { transform: scale(1.05); }

			.wa-popup {
				position: fixed;
				right: 20px;
				bottom: 90px;
				width: 320px;
				max-width: calc(100vw - 40px);
				background: #fff;
				border-radius: 12px;
				box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
				padding: 18px;
				z-index: 9999;
				display: none;
			}
			.wa-popup.open { display: block; }
			.wa-popup-head {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 12px;
			}
			.wa-popup-head h4 { margin: 0; font-size: 16px; font-weight: 600; color: #1e293b; }
			.wa-close {
				background: none;
				border: 0;
				font-size: 22px;
				line-height: 1;
				cursor: pointer;
				color: #475569;
			}
			.wa-popup label {
				display: block;
				font-size: 13px;
				font-weight: 600;
				color: #334155;
				margin-bottom: 4px;
			}
			.wa-popup label span { color: #dc2626; }
			.wa-popup input,
			.wa-popup textarea {
				display: block;
				width: 100%;
				box-sizing: border-box;
				padding: 9px 10px;
				margin-bottom: 12px;
				border: 1px solid #cbd0d8;
				border-radius: 8px;
				background: #f8f9fb;
				font-family: inherit;
				font-size: 14px;
				color: #334155;
			}
			.wa-popup input { height: 42px; }
			.wa-popup .wa-error {
				display: block;
				margin: -8px 0 10px;
				color: #dc2626;
				font-size: 12px;
			}
			.wa-popup .wa-error:empty { display: none; }
			.wa-popup input.wa-invalid,
			.wa-popup textarea.wa-invalid {
				border-color: #dc2626;
				background: #fff8f8;
			}
			.wa-submit {
				width: 100%;
				padding: 11px;
				border: 0;
				border-radius: 8px;
				background: #25D366;
				color: #fff;
				font-size: 14px;
				font-weight: 600;
				cursor: pointer;
			}
			.wa-submit:hover { background: #1ebe57; }
		</style>

		<button type="button" class="wa-float-btn" id="waFloatBtn" aria-label="Chat on WhatsApp">
			<i class="fa fa-whatsapp"></i>
		</button>

		<div class="wa-popup {{ $errors->hasAny(['wa_number', 'wa_message']) ? 'open' : '' }}" id="waPopup" role="dialog" aria-label="WhatsApp enquiry">
			<div class="wa-popup-head">
				<h4>Chat with us on WhatsApp</h4>
				<button type="button" class="wa-close" id="waClose" aria-label="Close">&times;</button>
			</div>

			<form action="{{ route('whatsapp-enquiry.store') }}" method="POST" novalidate>
				@csrf

				<label for="wa_message">Message </label>
				<textarea id="wa_message" name="wa_message" rows="4"
						placeholder="Type your message">{{ old('wa_message') }}</textarea>
				<span class="wa-error" data-error-for="wa_message">@error('wa_message'){{ $message }}@enderror</span>

				<label for="wa_number">Your mobile number <span>*</span></label>
				<input type="text" id="wa_number" name="wa_number" value="{{ old('wa_number') }}"
					placeholder="10-digit mobile" maxlength="10" inputmode="numeric">
				<span class="wa-error" data-error-for="wa_number">@error('wa_number'){{ $message }}@enderror</span>

				<button type="submit" class="wa-submit">Submit &amp; Open WhatsApp</button>
			</form>
		</div>

		<script>
			(function () {
				var btn = document.getElementById('waFloatBtn');
				var popup = document.getElementById('waPopup');
				var closeBtn = document.getElementById('waClose');
				if (!btn || !popup) return;

				var form = popup.querySelector('form');

				var rules = {
					wa_number: function (value) {
						if (value.trim() === '') return 'Please enter your mobile number.';
						if (!/^\d{10}$/.test(value.trim())) return 'Mobile number must be exactly 10 digits.';
						return '';
					},
					// wa_message: function (value) {
					// 	if (value.trim() === '') return 'Please type your message.';
					// 	if (value.trim().length > 1000) return 'Message must be 1000 characters or fewer.';
					// 	return '';
					// }
				};

				function showError(field, message) {
					var errorBox = popup.querySelector('[data-error-for="' + field.name + '"]');
					field.classList.toggle('wa-invalid', message !== '');
					if (errorBox) errorBox.textContent = message;
				}

				// Check a field while the user types, once it has been touched
				Object.keys(rules).forEach(function (name) {
					var field = form.elements[name];
					field.addEventListener('input', function () {
						showError(field, rules[name](field.value));
					});
				});

				// Check everything on submit and stop if anything is wrong
				form.addEventListener('submit', function (e) {
					var firstInvalid = null;

					Object.keys(rules).forEach(function (name) {
						var field = form.elements[name];
						var message = rules[name](field.value);
						showError(field, message);
						if (message && !firstInvalid) firstInvalid = field;
					});

					if (firstInvalid) {
						e.preventDefault();
						firstInvalid.focus();
					}
				});

				btn.addEventListener('click', function () {
					popup.classList.toggle('open');
				});
				closeBtn.addEventListener('click', function () {
					popup.classList.remove('open');
				});
			})();
		</script>
	</footer>
	<!-- /End Footer Area -->
 
	<!-- Jquery -->
    <script src="{{asset('public/frontend/js/jquery.min.js')}}"></script>
    <script src="{{asset('public/frontend/js/jquery-migrate-3.0.0.js')}}"></script>
	<script src="{{asset('public/frontend/js/jquery-ui.min.js')}}"></script>
	<!-- Popper JS -->
	<script src="{{asset('public/frontend/js/popper.min.js')}}"></script>
	<!-- Bootstrap JS -->
	<script src="{{asset('public/frontend/js/bootstrap.min.js')}}"></script>
	<!-- Color JS -->
	<!--<script src="{{asset('public/frontend/js/colors.js')}}"></script>-->
	<!-- Slicknav JS -->
	<script src="{{asset('public/frontend/js/slicknav.min.js')}}"></script>
	<!-- Owl Carousel JS -->
	<script src="{{asset('public/frontend/js/owl-carousel.js')}}"></script>
	<!-- Magnific Popup JS -->
	<script src="{{asset('public/frontend/js/magnific-popup.js')}}"></script>
	<!-- Waypoints JS -->
	<script src="{{asset('public/frontend/js/waypoints.min.js')}}"></script>
	<!-- Countdown JS -->
	<script src="{{asset('public/frontend/js/finalcountdown.min.js')}}"></script>
	<!-- Nice Select JS -->
	<script src="{{asset('public/frontend/js/nicesellect.js')}}"></script>
	<!-- Flex Slider JS -->
	<script src="{{asset('public/frontend/js/flex-slider.js')}}"></script>
	<!-- ScrollUp JS -->
	<script src="{{asset('public/frontend/js/scrollup.js')}}"></script>
	<!-- Onepage Nav JS -->
	<script src="{{asset('public/frontend/js/onepage-nav.min.js')}}"></script>
	{{-- Isotope --}}
	<script src="{{asset('public/frontend/js/isotope/isotope.pkgd.min.js')}}"></script>
	<!-- Easing JS -->
	<script src="{{asset('public/frontend/js/easing.js')}}"></script>

	<!-- Active JS -->
	<script src="{{asset('public/frontend/js/active.js')}}"></script>

	
	@stack('scripts')
	<script>
    	setTimeout(function(){
		  $('.alert').slideUp();
		},5000);
		$(function() {
		// ------------------------------------------------------- //
		// Multi Level dropdowns
		// ------------------------------------------------------ //
			$("ul.dropdown-menu [data-toggle='dropdown']").on("click", function(event) {
				event.preventDefault();
				event.stopPropagation();

				$(this).siblings().toggleClass("show");


				if (!$(this).next().hasClass('show')) {
				$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
				}
				$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
				$('.dropdown-submenu .show').removeClass("show");
				});

			});
		});
	  </script>
