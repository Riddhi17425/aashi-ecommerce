@extends('frontend.layouts.master')

@section('title','Aashi-Ecommerce || Forgot Password')

@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Forgot Password</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Shop Login -->
    <section class="shop login section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3 col-12">
                    <div class="login-form">
    <h2>Forgot Password</h2>

    <p>Please enter your email address to request a password reset link.</p>

    {{-- Success Message --}}
    @if (session('status'))
        <div class="alert alert-success text-center forgot-password-success" role="alert">
            <strong>Reset link sent successfully!</strong>
            <br>
            Please check your email inbox and follow the link to reset your password.
        </div>
    @endif

    {{-- Error Message --}}
    @if ($errors->has('email'))
        <div class="alert alert-danger text-center" role="alert">
            {{ $errors->first('email') }}
        </div>
    @endif

    {{-- Forgot Password Form --}}
    <form class="form" method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="row">

            <div class="col-12">
                <div class="form-group">

                    <label>Your Email<span>*</span></label>

                    <input
                        type="email"
                        name="email"
                        id="reset-email"
                        placeholder="Enter your email address"
                        value="{{ old('email') }}"
                        autofocus
                    >

                    <span
                        id="email-front-error"
                        style="display: none; color: #dc3545; font-size: 14px; margin-top: 5px;"
                    ></span>

                </div>
            </div>

            <div class="col-12">

                <div class="form-group login-btn mb-3">

                    <button
                        class="btn"
                        type="submit"
                        id="send-reset-link-btn"
                        style="width: 100%; border-radius: 0;"
                    >
                        <span id="reset-btn-text">SEND RESET LINK</span>

                        <span id="reset-btn-loader" style="display: none;">
                           SENDING...
                        </span>
                    </button>

                </div>

                <div class="text-center mt-3">

                    <span style="color: #666;">
                        Remember your password?
                    </span>

                    <a
                        href="{{ route('login.form') }}"
                        style="color: #5db845; font-weight: 600; text-decoration: none; margin-left: 5px;"
                    >
                        Login here
                    </a>

                </div>

            </div>

        </div>
    </form>
</div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Login -->

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form = document.querySelector('form[action="{{ route('password.email') }}"]');
            const email = document.getElementById('reset-email');
            const errorMessage = document.getElementById('email-front-error');

            const button = document.getElementById('send-reset-link-btn');
            const buttonText = document.getElementById('reset-btn-text');
            const loader = document.getElementById('reset-btn-loader');


            // Email validation function
            function validateEmail() {

                const emailValue = email.value.trim();

                // Empty email
                if (emailValue === '') {

                    errorMessage.textContent = 'Please enter your email address.';
                    errorMessage.style.display = 'block';

                    return false;
                }


                // Email format validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailPattern.test(emailValue)) {

                    errorMessage.textContent = 'Please enter a valid email address.';
                    errorMessage.style.display = 'block';

                    return false;
                }


                // Valid email
                errorMessage.textContent = '';
                errorMessage.style.display = 'none';

                return true;
            }


            // Validate while user is typing
            email.addEventListener('input', function () {

                validateEmail();

            });


            // Form submit
            form.addEventListener('submit', function (event) {

                // Check email before submitting
                if (!validateEmail()) {

                    event.preventDefault();

                    return;
                }


                // Disable button immediately
                button.disabled = true;

                // Change button text
                buttonText.style.display = 'none';
                loader.style.display = 'inline';

            });

        });
    </script>
@endsection
