{{-- SEO values: a page can set them with @section('meta_title') / @section('meta_description'),
     or the controller can pass $meta_title, $meta_description, $meta_image, $canonical_url, $og_type. --}}
@php
    $seoTitle = html_entity_decode(trim($__env->yieldContent('meta_title')), ENT_QUOTES) ?: ($meta_title ?? 'Aashi - Ecommerce');
    $seoDescription = html_entity_decode(trim($__env->yieldContent('meta_description')), ENT_QUOTES) ?: ($meta_description ?? 'Aashi Retail');
    $seoImage = $meta_image ?? asset('public/frontend/img/logo.png');
    $seoUrl = $canonical_url ?? url()->current();
@endphp
<!-- Meta Tag -->
@yield('meta')
<!-- Title Tag  -->
<title>{{ $seoTitle }}</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!--<link rel="icon" type="image/x-icon" href="">-->
<link rel="icon" href="{{ asset('public/frontend/img/aashi_retail_favicon.ico') }}" sizes="any">

<meta name="description" content="{{ $seoDescription }}">
{{-- Default is index, follow. Private pages set the robots section to noindex, follow. --}}
<meta name="robots" content="@yield('robots', 'index, follow')">
<link rel="canonical" href="{{ $seoUrl }}" />

<!--OG Tags-->
<meta property="og:site_name" content="Aashi Retail">
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:url" content="{{ $seoUrl }}">
<meta property="og:type" content="{{ $og_type ?? 'website' }}">

<!--Twitter X Card Tags-->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<!-- Web Font -->
<link href="https://fonts.googleapis.com/css?family=Poppins:200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">

<!-- StyleSheet -->
<link rel="manifest" href="{{ asset('public/manifest.json') }}">
<!-- Bootstrap -->
<link rel="stylesheet" href="{{asset('public/frontend/css/bootstrap.css')}}">
<!-- Magnific Popup -->
<link rel="stylesheet" href="{{asset('public/frontend/css/magnific-popup.min.css')}}">
<!-- Font Awesome -->
<link rel="stylesheet" href="{{asset('public/frontend/css/font-awesome.css')}}">
<!-- Fancybox -->
<link rel="stylesheet" href="{{asset('public/frontend/css/jquery.fancybox.min.css')}}">
<!-- Themify Icons -->
<link rel="stylesheet" href="{{asset('public/frontend/css/themify-icons.css')}}">
<!-- Nice Select CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/niceselect.css')}}">
<!-- Animate CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/animate.css')}}">
<!-- Flex Slider CSS -->
<link rel="stylesheet" href="{{asset('public/frontend/css/flex-slider.min.css')}}">
<!-- Owl Carousel -->
<link rel="stylesheet" href="{{asset('public/frontend/css/owl-carousel.css')}}">
<!-- Slicknav -->
<link rel="stylesheet" href="{{asset('public/frontend/css/slicknav.min.css')}}">
<!-- Jquery Ui -->
<link rel="stylesheet" href="{{asset('public/frontend/css/jquery-ui.css')}}">

<!-- Eshop StyleSheet -->
<link rel="stylesheet" href="{{asset('public/frontend/css/reset.css')}}">
<link rel="stylesheet" href="{{asset('public/frontend/css/style.css')}}">
<link rel="stylesheet" href="{{asset('public/frontend/css/responsive.css')}}">
<link rel="shortcut icon" type="image/x-icon" href="{{asset('public/frontend/img/favicon.png')}}">
<style>
    /* Multilevel dropdown */
    .dropdown-submenu {
    position: relative;
    }

    .dropdown-submenu>a:after {
    content: "\f0da";
    float: right;
    border: none;
    font-family: 'FontAwesome';
    }

    .dropdown-submenu>.dropdown-menu {
    top: 0;
    left: 100%;
    margin-top: 0px;
    margin-left: 0px;
    }
    
    /* Chrome, Safari, Edge, Opera */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    /* Firefox */
    input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
    body {
        animation: smoothFadeIn 0.5s ease-in-out;
    }
    @keyframes smoothFadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
</style>
@stack('styles')

{{-- Structured data (JSON-LD): pages pass a $schemas array from the controller --}}
@foreach ($schemas ?? [] as $schema)
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endforeach
