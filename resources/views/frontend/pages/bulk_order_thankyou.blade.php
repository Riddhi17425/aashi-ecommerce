@extends('frontend.layouts.master')
@section('title', 'Thank You')

@section('main-content')
<section class="section">
    <div class="container" style="text-align:center; padding: 80px 0;">
        <h2>Thank you for your enquiry</h2>
        <p>Our team will contact you shortly with the details of your bulk order.</p>
        <a href="{{ url('/') }}" class="btn">Back to home</a>
    </div>
</section>
@endsection