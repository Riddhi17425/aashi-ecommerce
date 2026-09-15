@extends('frontend.layouts.master')
@section('title', 'Aashi-Ecommerce || PRODUCT PAGE')
<style>
    .child-category li {
        list-style-type: disc;
        /* Adds default bullet points */
        margin-left: 18px;
        /* Adds spacing on the left */
    }
</style>
@section('main-content')

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Shop List</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Product Style 1 -->
    <!-- Off-Canvas Filter Drawer -->
    <div id="filter-offcanvas" class="filter-offcanvas">
        <div class="offcanvas-header">
            <h5><i class="ti-filter" style="color: #5db845;"></i> <span class="mx-3">Filter Products</span> </h5>
            <button type="button" class="close-offcanvas" id="close-filter-btn">&times;</button>
        </div>
        <div class="offcanvas-body">
            <div class="shop-sidebar p-0">
                <!-- Categories Widget -->
                <div class="single-widget category mb-4">
                    <h3 class="title">Categories</h3>
                    <ul class="categor-list">
                        @php
                            $menu = App\Models\Category::getAllParentWithChild();
                        @endphp
                        @if ($menu)
                            @foreach ($menu as $cat_info)
                                @if ($cat_info->child_cat->count() > 0)
                                    <li><a
                                            href="{{ route('product-cat', $cat_info->slug) }}"><b>{{ $cat_info->title }}</b></a>
                                        <ul class="child-category">
                                            @foreach ($cat_info->child_cat as $sub_menu)
                                                <li><a
                                                        href="{{ route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]) }}">{{ $sub_menu->title }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li><a
                                            href="{{ route('product-cat', $cat_info->slug) }}"><b>{{ $cat_info->title }}</b></a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Price Filter Widget -->
                <form action="{{ url()->current() }}" method="GET">
                    @if (request()->has('brand') && request()->brand)
                        <input type="hidden" name="brand" value="{{ request()->brand }}" />
                    @endif
                    @if (request()->has('search') && request()->search)
                        <input type="hidden" name="search" value="{{ request()->search }}" />
                    @endif
                    @if (request()->has('show') && request()->show)
                        <input type="hidden" name="show" value="{{ request()->show }}" />
                    @endif
                    @if (request()->has('sortBy') && request()->sortBy)
                        <input type="hidden" name="sortBy" value="{{ request()->sortBy }}" />
                    @endif
                    @if (request()->has('category') && request()->category)
                        <input type="hidden" name="category" value="{{ request()->category }}" />
                    @endif
                    <input type="hidden" name="price" id="price"
                        value="@if (!empty($_GET['price'])) {{ $_GET['price'] }} @endif" />

                    <div class="single-widget range mb-4">
                        <h3 class="title">Shop by Price</h3>
                        <div class="price-filter">
                            <div class="price-filter-inner">
                                @php
                                    $max_price = \App\Models\Product::where('status', 'Active')
                                        ->get()
                                        ->flatMap(function ($p) {
                                            $data = json_decode($p->size, true);
                                            if (!is_array($data) || empty($data['price'])) {
                                                return [];
                                            }
                                            return $data['price'];
                                        })
                                        ->map(function ($price) {
                                            $clean = preg_replace('/[^\d\.]/', '', (string) $price);
                                            return (int) $clean;
                                        })
                                        ->max();

                                    $max = $max_price ?? 0;
                                    $step = ceil($max / 5);
                                    $ranges = [];
                                    $start = 100;
                                    while ($start < $max) {
                                        $end = $start + $step;
                                        $ranges[] = [$start, $end];
                                        $start = $end;
                                    }
                                @endphp

                                <ul id="price-range-list" class="price-range-list">
                                    @foreach ($ranges as $range)
                                        <li>
                                            <label>
                                                <input type="checkbox" class="price-checkbox"
                                                    value="{{ $range[0] }}-{{ $range[1] }}"
                                                    @if (!empty($_GET['price']) && in_array($range[0] . '-' . $range[1], explode(',', $_GET['price']))) checked @endif>
                                                ₹{{ $range[0] }} - ₹{{ $range[1] }}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Brands Widget -->
                <div class="single-widget category">
                    <h3 class="title">Brands</h3>
                    <ul class="categor-list">
                        @php
                            $brands = DB::table('brands')->orderBy('title', 'ASC')->where('status', 'active')->get();
                        @endphp
                        @foreach ($brands as $brand)
                            <li><a href="{{ route('product-brand', $brand->slug) }}">{{ $brand->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="filter-overlay" class="filter-overlay"></div>
    <!-- Main Shop Section -->
    <section class="product-area shop-sidebar shop-list shop section pt-4 product_page_padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <!-- Shop Top Bar -->
                    <div class="shop-top-bar-custom">
                        <div class="shop-top-left">
                            <button type="button" class="btn-filter-toggle" id="open-filter-btn">
                                <i class="ti-filter"></i> Filter
                            </button>
                            @if (method_exists($products, 'firstItem') && $products->firstItem())
                                <span class="product-count-badge">
                                    Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of
                                    {{ $products->total() }} Products
                                </span>
                            @endif
                        </div>

                        <form action="{{ url()->current() }}" method="GET" class="shop-top-right-form m-0">
                            @if (request()->has('brand') && request()->brand)
                                <input type="hidden" name="brand" value="{{ request()->brand }}" />
                            @endif
                            @if (request()->has('price') && request()->price)
                                <input type="hidden" name="price" value="{{ request()->price }}" />
                            @endif
                            @if (request()->has('search') && request()->search)
                                <input type="hidden" name="search" value="{{ request()->search }}" />
                            @endif
                            @if (request()->has('category') && request()->category)
                                <input type="hidden" name="category" value="{{ request()->category }}" />
                            @endif
                            <div class="shop-shorter-custom">
                                <div class="single-shorter-custom">
                                    <label>Show:</label>
                                    <select class="show select-custom" name="show" onchange="this.form.submit();">
                                        <option value="">Default</option>
                                        <option value="9" @if (!empty($_GET['show']) && $_GET['show'] == '9') selected @endif>09
                                        </option>
                                        <option value="15" @if (!empty($_GET['show']) && $_GET['show'] == '15') selected @endif>15
                                        </option>
                                        <option value="21" @if (!empty($_GET['show']) && $_GET['show'] == '21') selected @endif>21
                                        </option>
                                        <option value="30" @if (!empty($_GET['show']) && $_GET['show'] == '30') selected @endif>30
                                        </option>
                                    </select>
                                </div>
                                <div class="single-shorter-custom">
                                    <label>Sort By:</label>
                                    <select class="sortBy select-custom" name="sortBy" onchange="this.form.submit();">
                                        <option value="">Default</option>
                                        <option value="title" @if (!empty($_GET['sortBy']) && $_GET['sortBy'] == 'title') selected @endif>Name
                                        </option>
                                        <option value="price" @if (!empty($_GET['sortBy']) && $_GET['sortBy'] == 'price') selected @endif>Price
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--/ End Shop Top Bar -->

                    <!-- 4 Cards Per Row Grid -->
                    <div class="row">
                        @if ($products)
                            @foreach ($products as $product)
                                @php
                                    $productPrice = 0;
                                    $sizeData = [];
                                    $decoded = json_decode($product->size, true);
                                    if (
                                        json_last_error() === JSON_ERROR_NONE &&
                                        is_array($decoded) &&
                                        isset($decoded['price'])
                                    ) {
                                        $sizeData = $decoded;
                                        $priceArr = $decoded['price'] ?? [];
                                        $productPrice = $priceArr[0] ?? 0;
                                    } else {
                                        $productPrice = $product->price ?? 0;
                                        $sizeData = [
                                            'size' => [],
                                            'price' => [$productPrice],
                                        ];
                                    }
                                    $after_discount = $productPrice - ($productPrice * ($product->discount ?? 0)) / 100;

                                    $defaultColorId = $product->color->first()->id ?? null;
                                    $whishlist_check = App\Models\Wishlist::where('user_id', Auth::id() ?? 0)->where(
                                        'product_id',
                                        $product->id,
                                    );
                                    if ($defaultColorId != null) {
                                        $whishlist_check = $whishlist_check->where('color_id', $defaultColorId);
                                    }
                                    $whishlist_check = $whishlist_check->first();
                                    $wishlisted = $whishlist_check ? 'active' : '';
                                @endphp

                                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                                    <div class="custom-product-card list-content">
                                        <div class="card-media-wrap">
                                            <a href="{{ route('product-detail', $product->slug) }}"
                                                class="card-img-link">
                                                @php $photo=explode(',',$product->photo); @endphp
                                                <img class="default-img card-default-img"
                                                    src="{{ asset('public/' . $photo[0]) }}"
                                                    alt="{{ $product->title }}">
                                                @if (isset($photo[1]))
                                                    <img class="hover-img card-hover-img"
                                                        src="{{ asset('public/' . $photo[1]) }}"
                                                        alt="{{ $product->title }}">
                                                @endif
                                            </a>
                                            @if ($product->stock <= 0)
                                                <div class="card-left-badges">
                                                    <span class="card-badge-tag badge-hot">Out of Stock</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="card-content-wrap">
                                            <div>
                                                <div
                                                    class="card-meta-line d-flex justify-content-between align-items-center">
                                                    <span class="card-cat-name">
                                                        <a
                                                            href="{{ route('product-detail', $product->slug) }}">{{ $product->product_code }}</a>
                                                    </span>
                                                    <div class="product-price"
                                                        data-discount="{{ $product->discount ?? 0 }}">
                                                        <small
                                                            class="original-price @if (empty($product->discount)) d-none @endif"
                                                            style="font-size: 11px;">
                                                            <del
                                                                class="text-muted">₹{{ number_format($sizeData['price'][0] ?? $productPrice, 2) }}</del>
                                                        </small>
                                                        <span class="final-price font-weight-bold"
                                                            style="color: #0f172a; font-size: 14px;">
                                                            @if (!empty($product->discount))
                                                                ₹{{ number_format($after_discount, 2) }}
                                                            @else
                                                                ₹{{ number_format($sizeData['price'][0] ?? $productPrice, 2) }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                <h4 class="card-item-title">
                                                    <a href="{{ route('product-detail', $product->slug) }}"
                                                        title="{{ $product->title }}">
                                                        {!! html_entity_decode($product->title) !!}
                                                    </a>
                                                </h4>

                                                {{-- Dynamic Product Rating --}}
                                                @php
                                                    $reviewCount = $product->getReview->count();
                                                    $rate =
                                                        $reviewCount > 0
                                                            ? round($product->getReview->avg('rate'), 1)
                                                            : 0;
                                                @endphp

                                                @if ($reviewCount > 0)
                                                    <div class="card-rating my-2"
                                                        style="
            display: inline-flex;
            align-items: center;
            gap: 3px;
            background: #F7941D;
            padding: 4px 8px;
            border-radius: 4px;
        ">



                                                        <span style="font-size: 12px; color: #fff; margin-left: 2px;">
                                                            {{ number_format($rate, 1) }}
                                                        </span>

                                                        <i class="fa fa-star" style="color: #fff; font-size: 12px;"></i>

                                                        <span style="font-size: 12px; color: #fff; margin-left: 4px;">
                                                            |
                                                        </span>

                                                        <span style="font-size: 12px; color: #fff; margin-left: 2px;">
                                                            {{ $reviewCount }}
                                                        </span>

                                                    </div>
                                                @endif


                                                {{-- <div class="card-rating my-2"
                                                    style="display: flex; align-items: center; gap: 3px;">
                                                    <i class="fa fa-star" style="color: #F7941D; font-size: 12px;"></i>
                                                    <i class="fa fa-star" style="color: #F7941D; font-size: 12px;"></i>
                                                    <i class="fa fa-star" style="color: #F7941D; font-size: 12px;"></i>
                                                    <i class="fa fa-star" style="color: #F7941D; font-size: 12px;"></i>
                                                    <i class="fa fa-star" style="color: #F7941D; font-size: 12px;"></i>
                                                </div> --}}

                                                @if (!empty($sizeData['size']))
                                                    <div class="product-sizes my-2">
                                                        @foreach ($sizeData['size'] as $key => $size)
                                                            <label class="size-box">
                                                                <input type="radio"
                                                                    name="product_size_{{ $product->id }}"
                                                                    value="{{ $size }}"
                                                                    data-price="{{ $sizeData['price'][$key] ?? $productPrice }}"
                                                                    data-discount="{{ $product->discount ?? 0 }}"
                                                                    data-discount-val="{{ $after_discount ?? 0 }}"
                                                                    data-price-id="{{ $sizeData['price'][$key] ?? $productPrice }}"
                                                                    data-size-id="{{ $sizeData['size'][$key] ?? '' }}"
                                                                    class="one {{ $key == 0 ? 'set_active' : '' }}"
                                                                    onclick="setPriceId(this)">
                                                                <span
                                                                    class="{{ $key }}">{{ $size }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="card-cta-container">
                                                <a href="{{ route('product-detail', $product->slug) }}"
                                                    class="btn-card-details">
                                                    <span>View Details</span>
                                                    <i class="ti-arrow-right"></i>
                                                </a>
                                            </div>



                                            {{-- <div class="add-to-cart mt-auto pt-2">
                                                <div class="d-flex align-items-center m-0"
                                                    data-slug="{{ $product->slug }}">
                                                    @if ($product->stock > 0)
                                                        <a href="javascript:void(0);"
                                                            data-product-slug="{{ $product->slug }}"
                                                            class="add-to-wishlist btn min p-2 mr-2 {{ $wishlisted }}"
                                                            title="Wishlist"
                                                            style="border-radius: 6px; border: 1px solid #cbd5e1; width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;"><i
                                                                class="ti-heart"></i></a>

                                                        <button type="button"
                                                            class="btn-card-details flex-grow-1 border-0 quick-add-to-cart-btn"
                                                            data-slug="{{ $product->slug }}"
                                                            style="cursor: pointer; height: 36px;">Add to Cart <i
                                                                class="ti-shopping-cart ml-1"></i></button>
                                                    @else
                                                        <span
                                                            class="text-danger font-weight-bold w-100 text-center py-1">Out
                                                            of Stock</span>
                                                    @endif
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h4 class="text-warning col-12 text-center" style="margin:100px auto;">There are no products.
                            </h4>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12 justify-content-center d-flex">
                            @if (method_exists($products, 'lastPage') && $products->lastPage() > 1)
                                <nav class="custom-pagination-wrap" aria-label="Page navigation">
                                    <ul class="custom-pagination">
                                        <li class="prev @if (!$products->previousPageUrl()) disabled @endif">
                                            <a
                                                href="{{ $products->previousPageUrl() ?: 'javascript:void(0);' }}">&laquo;</a>
                                        </li>
                                        @for ($i = 1; $i <= $products->lastPage(); $i++)
                                            <li class="page-item @if ($products->currentPage() == $i) active @endif">
                                                <a class="page-link"
                                                    href="{{ $products->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor
                                        <li class="next @if (!$products->nextPageUrl()) disabled @endif">
                                            <a href="{{ $products->nextPageUrl() ?: 'javascript:void(0);' }}">&raquo;</a>
                                        </li>
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ End Product Style 1  -->
    {{-- </form> --}}
    <!-- Modal -->
    @if ($products)
        @foreach ($products as $key => $product)
            <div class="modal fade" id="{{ $product->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    class="ti-close" aria-hidden="true"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="row no-gutters">
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <!-- Product Slider -->
                                    <div class="product-gallery">
                                        <div class="quickview-slider-active">
                                            @php
                                                $photo = explode(',', $product->photo);
                                                // dd($photo);
                                            @endphp
                                            @foreach ($photo as $data)
                                                <div class="single-slider">
                                                    <img src="{{ asset('public/' . $data) }}"
                                                        alt="{{ asset('public/' . $data) }}">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <!-- End Product slider -->
                                </div>
                                <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                    <div class="quickview-content">
                                        <h2>{{ $product->title }}</h2>
                                        <div class="quickview-ratting-review">
                                            <div class="quickview-ratting-wrap">
                                                <div class="quickview-ratting">

                                                    @php
                                                        $rate = DB::table('product_reviews')
                                                            ->where('product_id', $product->id)
                                                            ->avg('rate');
                                                        $rate_count = DB::table('product_reviews')
                                                            ->where('product_id', $product->id)
                                                            ->count();
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($rate >= $i)
                                                            <i class="yellow fa fa-star"></i>
                                                        @else
                                                            <i class="fa fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <a href="#"> ({{ $rate_count }} customer review)</a>
                                            </div>
                                            <div class="quickview-stock">
                                                @if ($product->stock > 0)
                                                    <span><i class="fa fa-check-circle-o"></i> {{ $product->stock }} in
                                                        stock</span>
                                                @else
                                                    <span><i class="fa fa-times-circle-o text-danger"></i>
                                                        {{ $product->stock }} out stock</span>
                                                @endif
                                            </div>
                                        </div>

                                        //$after_discount=($product->price-($product->price*$product->discount)/100);
                                        @php

                                            $productPrice = 0;

                                            // Try decode JSON
                                            $sizes = json_decode($product->size);

                                            // Check valid JSON + price exists
                                            if (
                                                json_last_error() === JSON_ERROR_NONE &&
                                                is_object($sizes) &&
                                                isset($sizes->price)
                                            ) {
                                                $priceArr = $sizes->price ?? [];
                                                $productPrice = $priceArr[0] ?? 0;
                                            } else {
                                                // OLD DATA fallback
                                                $productPrice = $product->price ?? 0;

                                                // ✅ make $sizes safe so it won't break later
    $sizes = (object) [
        'size' => [],
        'price' => [$productPrice],
                                                ];
                                            }

                                            // Discount
                                            $after_discount =
                                                $productPrice - ($productPrice * $product->discount) / 100;

                                        @endphp

                                        <h3><small>
                                                <del class="text-muted">₹{{ number_format($productPrice, 2) }}</del>
                                            </small>
                                            ₹{{ number_format($after_discount, 2) }}
                                        </h3>
                                        <div class="quickview-peragraph">
                                        </div>
                                        <form action="{{ route('single-add-to-cart') }}" method="POST">
                                            @csrf
                                            <div class="quantity">
                                                <!-- Input Order -->
                                                <div class="input-group">
                                                    <div class="button minus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                            disabled="disabled" data-type="minus" data-field="quant[1]">
                                                            <i class="ti-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="hidden" name="slug" value="{{ $product->slug }}">
                                                    <input type="text" name="quant[1]" class="input-number"
                                                        data-min="1" data-max="1000" value="1">
                                                    <div class="button plus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                            data-type="plus" data-field="quant[1]">
                                                            <i class="ti-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <!--/ End Input Order -->
                                            </div>
                                            <div class="add-to-cart">
                                                <button type="submit" class="btn">Add to cart</button>
                                                <a href="{{ route('add-to-wishlist', $product->slug) }}"
                                                    class="btn min"><i class="ti-heart"></i></a>
                                            </div>
                                        </form>
                                        <div class="default-social">
                                            <!-- ShareThis BEGIN -->
                                            <div class="sharethis-inline-share-buttons"></div><!-- ShareThis END -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    <!-- Modal end -->
@endsection
@push('styles')
    <style>
        /* Blinking text cursor (caret) ko page me kahin bhi na dikhaye */
* {
    caret-color: transparent;
}

/* Sirf typing wale fields me caret wapas normal */
input,
textarea,
select,
[contenteditable="true"],
.form-control,
.input-number,
.select-custom {
    caret-color: auto !important;
}
        .btn-card-details {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            background: #111827;
            color: #ffffff !important;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.25s ease;
        }

        .btn-card-details:hover {
            background: #5db845;
            box-shadow: 0 4px 12px rgba(93, 184, 69, 0.35);
        }

        .btn-card-details i {
            font-size: 11px;
            transition: transform 0.2s ease;
        }

        .btn-card-details:hover i {
            transform: translateX(4px);
        }

        /* SHOP TOP BAR STYLING */
        .shop-top-bar-custom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .shop-top-left {
            display: flex;
            align-items: center;
        }

        .product-count-badge {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            margin-left: 15px;
        }

        .shop-shorter-custom {
            display: flex;
            align-items: center;
        }

        .single-shorter-custom {
            display: flex;
            align-items: center;
            margin-left: 16px;
        }

        .single-shorter-custom label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin: 0 6px 0 0;
        }

        .select-custom {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 24px 5px 10px;
            font-size: 13px;
            color: #1e293b;
            font-weight: 600;
            background: #ffffff;
            outline: none;
            cursor: pointer;
        }

        /* MOBILE RESPONSIVE SHOP TOP BAR STYLING */
        @media (max-width: 767px) {
            .shop-top-bar-custom {
                padding: 12px !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }

            .shop-top-left {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
            }

            .product-count-badge {
                font-size: 12px !important;
                color: #64748b !important;
                margin-left: 0 !important;
                font-weight: 600 !important;
            }

            .shop-top-right-form {
                width: 100% !important;
            }

            .shop-shorter-custom {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 8px !important;
            }

            .single-shorter-custom {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                margin: 0 !important;
                width: 100% !important;
            }

            .single-shorter-custom label {
                font-size: 13px !important;
                font-weight: 700 !important;
                color: #334155 !important;
                margin: 0 10px 0 0 !important;
                white-space: nowrap !important;
                min-width: 65px !important;
            }

            .select-custom {
                flex: 1 !important;
                width: 100% !important;
                min-width: 0 !important;
                padding: 6px 28px 6px 12px !important;
                font-size: 13px !important;
                height: 38px !important;
                border-radius: 6px !important;
            }

            .btn-filter-toggle {
                height: 36px !important;
                padding: 6px 14px !important;
                font-size: 13px !important;
            }
        }

        /* MODERN CUSTOM PRODUCT CARD STYLING */
        .custom-product-card {
            background: #ffffff;
            border: 1px solid #e8edf2;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            height: auto;
        }

        .custom-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.09);
            border-color: #cbd5e1;
        }

        .card-media-wrap {
            position: relative;
            background: #f8fafc;
            /* height: 240px; */
            width: 100%;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
            flex-shrink: 0;
        }

        .card-img-link {
            display: block;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
            position: relative;
        }

        .card-default-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: transform 0.4s ease, opacity 0.3s ease;
        }

        .card-hover-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            opacity: 0;
            display: block;
            transition: opacity 0.35s ease, transform 0.4s ease;
        }

        .custom-product-card:hover .card-default-img {
            transform: scale(1.05);
        }

        .custom-product-card:hover .card-hover-img {
            opacity: 1;
            transform: scale(1.05);
        }

        .card-left-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 3;
        }

        .card-badge-tag.badge-hot {
            background: #e11d48;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .card-content-wrap {
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            flex-grow: 0;
        }

        .card-meta-line {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .card-cat-name a {
            color: #5db845;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-item-title {
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.35;
            margin: 4px 0 6px 0;
            min-height: auto;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-item-title a {
            color: #1e293b;
            transition: color 0.2s ease;
            text-decoration: none !important;
        }

        .card-item-title a:hover {
            color: #5db845;
        }

        .btn-card-details {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            color: #ffffff !important;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.25s ease;
        }

        .btn-card-details:hover {
            background: #5db845;
            box-shadow: 0 4px 12px rgba(93, 184, 69, 0.35);
        }

        .product-sizes {
            display: flex;
            flex-wrap: wrap;
            margin-top: 4px;
        }

        .size-box {
            cursor: pointer;
            display: inline-block;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        .size-box input {
            display: none;
        }

        .size-box input+span {
            border: 1px solid #cbd5e1;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .size-box input:checked+span,
        .size-box input:hover+span {
            border-color: #5db845;
            background: #f0fdf4;
            color: #15803d;
        }

        a.btn.min.active {
            background: #5db845 !important;
            color: #fff !important;
            border-color: transparent !important;
        }

        /* OFF-CANVAS FILTER DRAWER STYLING */
        .filter-offcanvas {
            position: fixed;
            top: 0;
            left: -360px;
            width: 340px;
            height: 100vh;
            background: #ffffff;
            z-index: 999999;
            box-shadow: 6px 0 30px rgba(0, 0, 0, 0.15);
            transition: left 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow-y: auto;
            padding: 0;
        }

        .filter-offcanvas.active {
            left: 0;
        }

        .filter-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            z-index: 999998;
            display: none;
            backdrop-filter: blur(2px);
        }

        .filter-overlay.active {
            display: block;
        }

        .offcanvas-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            background: #0f172a;
            color: #ffffff;
            border-bottom: 1px solid #1e293b;
        }

        .offcanvas-header h5 {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .close-offcanvas {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #94a3b8;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .close-offcanvas:hover {
            color: #ffffff;
        }

        .btn-filter-toggle {
            display: inline-flex;
            align-items: center;
            background: #5db845;
            color: #ffffff !important;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            border: none;
            transition: background 0.2s ease;
            height: 38px;
        }

        .btn-filter-toggle i {
            margin-right: 6px;
        }

        .btn-filter-toggle:hover {
            background: #4ca336;
        }

        /* Custom compact pagination */
        .custom-pagination-wrap {
            margin: 16px 0;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .custom-pagination {
            display: inline-flex;
            gap: 6px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .custom-pagination .page-item a,
        .custom-pagination .prev a,
        .custom-pagination .next a {
            display: inline-block;
            padding: 6px 10px;
            border: 1px solid #e6e6e6;
            color: #666;
            min-width: 34px;
            text-align: center;
            background: #fff;
            border-radius: 3px;
            font-size: 13px;
        }

        .custom-pagination .page-item.active a {
            background: #F7941D;
            color: #fff;
            border-color: #F7941D;
        }

        .custom-pagination .prev a,
        .custom-pagination .next a {
            font-weight: 700;
        }

        .custom-pagination .disabled a {
            opacity: 0.45;
            pointer-events: none;
        }
    </style>
@endpush
@push('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> --}}
    <script src="{{ asset('public/frontend/js/sweetalert.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openBtn = document.getElementById('open-filter-btn');
            const closeBtn = document.getElementById('close-filter-btn');
            const offcanvas = document.getElementById('filter-offcanvas');
            const overlay = document.getElementById('filter-overlay');

            function openFilter() {
                if (offcanvas) offcanvas.classList.add('active');
                if (overlay) overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeFilter() {
                if (offcanvas) offcanvas.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openFilter);
            if (closeBtn) closeBtn.addEventListener('click', closeFilter);
            if (overlay) overlay.addEventListener('click', closeFilter);
        });

        $(document).ready(function() {
            $('.price-checkbox').on('change', function() {
                $('.price-checkbox').not(this).prop('checked', false);
                let selected = $(this).is(':checked') ? $(this).val() : null;
                $('#price').val(selected ? selected : '');
                $(this).closest('form').submit();
            });

            $('.add-to-wishlist').on('click', function(e) {
                e.preventDefault();
                var productSlug = $(this).attr('data-product-slug');
                var colorId = $('#selected_color').val();
                var baseUrl = "{{ url('wishlist') }}/" + productSlug;
                window.location.href = baseUrl + '?color_id=' + colorId;
            });
        });
    </script>

    {{-- size wise price change script --}}
    <script>
        document.querySelectorAll('input[type="radio"][name^="product_size"]').forEach(radio => {
            radio.addEventListener('change', function() {
                let productBox = this.closest('.list-content');
                if (!productBox) return;

                let priceWrapper = productBox.querySelector('.product-price');
                let originalEl = productBox.querySelector('.original-price del');
                let originalWrap = productBox.querySelector('.original-price');
                let finalEl = productBox.querySelector('.final-price');

                let sizeInput = productBox.querySelector('.selected_size');
                let priceInput = productBox.querySelector('.selected_price');

                let basePrice = parseFloat(this.dataset.price);
                let discount = parseFloat(this.dataset.discount || 0);

                let finalPrice = basePrice;

                if (discount > 0) {
                    finalPrice = basePrice - (basePrice * discount / 100);
                    if (originalWrap) originalWrap.classList.remove('d-none');
                    if (originalEl) originalEl.innerText = "₹" + basePrice.toFixed(2);
                } else {
                    if (originalWrap) originalWrap.classList.add('d-none');
                }

                if (finalEl) finalEl.innerText = "₹" + finalPrice.toFixed(2);

                if (sizeInput) sizeInput.value = this.value;
                if (priceInput) priceInput.value = finalPrice.toFixed(2);

                productBox.querySelectorAll('.size-box span')
                    .forEach(span => span.classList.remove('selected'));

                this.nextElementSibling.classList.add('selected');
            });
        });

        document.querySelectorAll('.product-sizes').forEach(box => {
            let first = box.querySelector('input[type="radio"]');
            if (first) {
                first.checked = true;
                first.dispatchEvent(new Event('change'));
            }
        });
    </script>

    <script>
        $(document).on('click', '.quick-add-to-cart-btn', function() {
            let btn = $(this);
            let slug = btn.data('slug');
            let productBox = btn.closest('.list-content');

            let selectedSize = productBox.find('.selected_size').val() || '';
            let selectedPrice = productBox.find('.selected_price').val() || '';
            let selectedColor = productBox.find('.selected_color, [id^="selected_color"]').val() || '';

            $.ajax({
                url: "{{ route('single-add-to-cart') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    slug: slug,
                    quant: {
                        1: 1
                    },
                    selected_size: selectedSize,
                    selected_price: selectedPrice,
                    selected_color: selectedColor
                },
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            confirmButtonColor: '#F7941D',
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: response.message,
                            confirmButtonColor: '#F7941D',
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!',
                    });
                }
            });
        });
    </script>
@endpush
