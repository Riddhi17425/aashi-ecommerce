@extends('frontend.layouts.master')
@section('meta')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name='copyright' content=''>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick-theme.css" />
@endsection
@section('title', 'E-SHOP || PRODUCT DETAIL')
@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            @if ($product_detail->cat_info)
                                <li><a href="{{ route('productlist', $product_detail->cat_info->slug) }}">{{ $product_detail->cat_info->title }}<i class="ti-arrow-right"></i></a></li>
                                @if ($product_detail->sub_cat_info)
                                    <li><a href="{{ route('productlist', $product_detail->sub_cat_info->slug) }}">{{ $product_detail->sub_cat_info->title }}<i class="ti-arrow-right"></i></a></li>
                                @endif
                            @endif
                            <li class="active"><span>{{ $product_detail->title }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->
    <!-- Shop Single -->
    <section class="shop single section pb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <!-- Product Slider -->
                            @if ($product_detail->color->first())
                                <div class="product-gallery">
                                    <div class="main-banner">
                                        {{-- Show video first if exists --}}
                                        @if (!empty($product_detail->video))
                                            <div>
                                                <video width="100%" height="400" controls>
                                                    <source
                                                        src="{{ asset('public/product_videos/' . $product_detail->video) }}"
                                                        type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        @endif
                                        @foreach ($product_detail->color->first()->images as $image)
                                            <img src="{{ asset('public/storage/products/' . $image->image) }}"
                                                alt="{{ $product_detail->color->first()->color_name }}">
                                        @endforeach
                                    </div>
                                    <div class="small-banner">
                                        {{-- Video thumbnail --}}
                                        @if (!empty($product_detail->video))
                                            <div>
                                                <video width="100" height="80">
                                                    <source
                                                        src="{{ asset('public/product_videos/' . $product_detail->video) }}"
                                                        type="video/mp4">
                                                </video>
                                            </div>
                                        @endif
                                        @foreach ($product_detail->color->first()->images as $image)
                                            <img src="{{ asset('public/storage/products/' . $image->image) }}"
                                                alt="{{ $product_detail->color->first()->color_name }}">
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="flexslider-thumbnails">
                                    <ul class="slides">
                                        @php
                                            $photo = explode(',', $product_detail->photo);
                                        @endphp
                                        @foreach ($photo as $data)
                                            <li data-thumb="{{ asset('public' . $data) }}" rel="adjustX:10, adjustY:">
                                                <img src="{{ asset('public' . $data) }}"
                                                    alt="{{ $product_detail->title }}">
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <!-- End Product slider -->
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="product-des">
                                <!-- Description -->
                                <div class="short">
                                    <h1>{{ $product_detail->title }}</h1>
                                    <h5 class="description">{{ $product_detail->product_code }}</h5>
                                    <div class="rating-main">
                                        <ul class="rating">
                                            @php
                                                $reviewCount = $product_detail->getReview->count();
                                                $rate =
                                                    $reviewCount > 0
                                                        ? round($product_detail->getReview->avg('rate'), 1)
                                                        : 0;
                                            @endphp

                                            @if ($reviewCount > 0)
                                                <div class="rating-main"
                                                    style="display: flex; align-items: center; gap: 6px;">

                                                    {{-- Stars --}}
                                                    <ul class="rating"
                                                        style="display: flex; align-items: center; margin: 0; gap: 2px;">

                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @php
                                                                $fillPercentage = max(
                                                                    0,
                                                                    min(100, ($rate - ($i - 1)) * 100),
                                                                );
                                                            @endphp

                                                            <li style="line-height: 1;">
                                                                <i class="fa fa-star"
                                                                    style="
                            font-size: 14px;
                            background: linear-gradient(
                                to right,
                                #F7941D {{ $fillPercentage }}%,
                                #d9d9d9 {{ $fillPercentage }}%
                            );
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                        ">
                                                                </i>
                                                            </li>
                                                        @endfor

                                                    </ul>

                                                    {{-- Rating Info Box --}}
                                                    <div
                                                        style="
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #F7941D;
            padding: 4px 8px;
            border-radius: 4px;
        ">

                                                        <span style="font-size: 13px; color: #fff;">
                                                            {{ number_format($rate, 1) }}
                                                        </span>

                                                        <span style="font-size: 13px; color: #fff;">
                                                            |
                                                        </span>

                                                        <span style="font-size: 13px; color: #fff;">
                                                            {{ $reviewCount }} Rating
                                                        </span>

                                                    </div>

                                                </div>
                                            @endif @php
                                                $sizes = json_decode($product_detail->size);
                                                $priceArr = $sizes->price;
                                                $productPrice = 0;
                                                $productPrice = $priceArr[0];
                                                $after_discount =
                                                    $productPrice - ($productPrice * $product_detail->discount) / 100;
                                                $sizeData = json_decode($product_detail->size, true);
                                            @endphp
                                            <p class="price" data-discount="{{ $product_detail->discount }}">
                                                @if ($product_detail->discount > 0)
                                                    <small class="original-price">
                                                        <del class="text-muted">
                                                            ₹{{ number_format($sizeData['price'][0], 2) }}
                                                        </del>
                                                    </small>
                                                    <span class="discounted-price" id="targetDivId">
                                                        ₹{{ number_format($after_discount, 2) }}
                                                    </span>
                                                @else
                                                    <span class="discounted-price" id="targetDivId">
                                                        ₹{{ number_format($sizeData['price'][0], 2) }}
                                                    </span>
                                                @endif
                                            </p>
                                    </div>
                                    <!--/ End Description -->
                                    <!-- Color -->
                                    @php
                                        $variantStock = $product_detail->variants
                                            ->mapWithKeys(fn ($v) => [$v->size . '|' . ($v->color_id ?? 'none') => (int) $v->quantity])
                                            ->all();
                                    @endphp
                                    <script type="application/json" id="variant-stock">@json($variantStock)</script>

                                    @if ($product_detail->color->first())
                                        <div class="color mt-4">
                                            <h4>Available Options <span>Color</span></h4>
                                            <ul id="color-options">
                                                @foreach ($product_detail->color as $key => $color)
                                                    @php
                                                        $isWhite =
                                                            strtolower($color->color_code) == '#ffffff' ||
                                                            strtolower($color->color_code) == '#fff' ||
                                                            strtolower($color->color_name) == 'white';
                                                    @endphp
                                                    <li>
                                                        <a style="background-color: {{ $color->color_code }};
                                                                  border: {{ $isWhite ? '1px solid #eee' : '1px solid transparent' }};"
                                                            href="javascript:void(0);"
                                                            class="one color-selector {{ $key == 0 ? 'set_active' : '' }}"
                                                            data-color-id="{{ $color->id }}"
                                                            data-color-name="{{ $color->color_name }}">
                                                            <i class="ti-check {{ $loop->first ? 'selected' : '' }}"
                                                                style="color: {{ $isWhite ? '#000' : '#fff' }};">
                                                            </i>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <!--/ End Color -->
                                    <!-- Size -->
                                    @if ($product_detail->size)
                                        @php
                                            $sizes = json_decode($product_detail->size, true);
                                        @endphp
                                        <div class="size mt-4 product-sizeoptions">
                                            <h4>Size</h4>
                                            <ul id="size-options">
                                                @foreach ($sizes['size'] as $key => $size)
                                                    <li><a href="javascript:void(0);"
                                                            data-price-id="{{ $sizes['price'][$key] }}"
                                                            data-size-id="{{ $size }}"
                                                            class="one size-selector {{ $key == 0 ? 'set_active' : '' }}">{{ $size }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <!--/ End Size -->
                                    <!-- Product Buy -->
                                    <div class="">
                                        <div id="add-to-cart-wrapper">
                                            @if ($product_detail->stock > 0)
                                                <div class="quantity mt-3">
                                                    <h6>Quantity :</h6>
                                                    <!-- Input Order -->
                                                    <div class="input-group">
                                                        <div class="button minus">
                                                            <button type="button" class="btn btn-primary btn-number"
                                                                disabled="disabled" data-type="minus"
                                                                data-field="quant[1]">
                                                                <i class="ti-minus"></i>
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="slug" id="product_slug"
                                                            value="{{ $product_detail->slug }}">
                                                        <input type="text" name="quant[1]" class="input-number"
                                                            data-min="1" data-max="1000" value="1"
                                                            id="quantity">
                                                        <div class="button plus">
                                                            <button type="button" class="btn btn-primary btn-number"
                                                                data-type="plus" data-field="quant[1]">
                                                                <i class="ti-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!--/ End Input Order -->
                                                </div>
                                                <p id="variant-stock-msg" style="display:none;"></p>
                                            @endif
                                            <?php
                                            $defaultColorId = $product_detail->color->first()->id ?? null;
                                            $whishlist_check = App\Models\Wishlist::where('user_id', Auth::id() ?? 0)->where('product_id', $product_detail->id);
                                            if ($defaultColorId != null) {
                                                $whishlist_check = $whishlist_check->where('color_id', $defaultColorId);
                                            }
                                            $whishlist_check = $whishlist_check->first();
                                            $wishlisted = '';
                                            if ($whishlist_check) {
                                                $wishlisted = 'active';
                                            } else {
                                                $wishlisted = '';
                                            }
                                            ?>
                                            <div class="add-to-cart mt-4">
                                                <input type="hidden" name="selected_size" id="selected_size"
                                                    value="{{ $sizes['size'][0] ?? '' }}">
                                                <input type="hidden" name="selected_price" id="selected_price"
                                                    value="{{ $sizeData['price'][0] }}">
                                                <input type="hidden" name="selected_color" id="selected_color"
                                                    value="{{ $product_detail->color->first()->id ?? '' }}">
                                                <input type="hidden" name="selected_color_name" id="selected_color_name"
                                                    value="{{ $product_detail->color->first()->color_name ?? '' }}">
                                                @if ($product_detail->stock > 0)
                                                    <button type="button" class="btn add_to_cart_btn"
                                                        data-product-id="{{ $product_detail->id }}">Add to cart</button>
                                                    <a href="javascript:void(0);"
                                                        class="add-to-wishlist btn min {{ $wishlisted }}"><i
                                                            class="ti-heart"></i></a>
                                                    <button type="button" class="btn cart buy-now-btn"
                                                        data-id="{{ $product_detail->id }}">
                                                        Buy Now
                                                    </button>
                                                @else
                                                    <button type="button" class="btn out-of-stock-btn" disabled><i class="fa fa-ban" aria-hidden="true"></i> Out of Stock</button>
                                                    {{-- Out of stock: visitor can leave an email to be notified --}}
                                                    <form action="{{ route('product.notify-me', $product_detail->slug) }}" method="POST" class="mt-3 notify-form">
                                                        @csrf
                                                        <label for="notify-email"><i class="fa fa-bell-o" aria-hidden="true"></i> Get an email when this product is back in stock</label>
                                                        <div class="d-flex">
                                                            <input type="email" id="notify-email" name="email" class="form-control mr-2" placeholder="Enter your email" required maxlength="191">
                                                            <button type="submit" class="btn">Notify Me</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>
                                            <div class="product-share mt-3">
                                                <h6>Share Product :</h6>
                                                @php
                                                    $shareUrl = route('product-detail', $product_detail->slug);
                                                    $shareText = urlencode(
                                                        $product_detail->title . ' - Check this product',
                                                    );
                                                @endphp
                                                <a href="https://wa.me/?text={{ $shareText }}%20{{ urlencode($shareUrl) }}"
                                                    target="_blank" class="share-btn whatsapp">
                                                    <i class="fa fa-whatsapp"></i>
                                                </a>
                                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                                                    target="_blank" class="share-btn facebook">
                                                    <i class="fa fa-facebook"></i>
                                                </a>
                                                <a href="https://www.instagram.com/" target="_blank"
                                                    class="share-btn instagram">
                                                    <i class="fa fa-instagram"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <p class="cat">Category :
                                            <a
                                                href="{{ route('productlist', $product_detail->cat_info['slug']) }}">{{ $product_detail->cat_info['title'] }}</a>
                                        </p>
                                        @if ($product_detail->sub_cat_info)
                                            <p class="cat mt-1">Sub Category :
                                                <a
                                                    href="{{ route('productlist', $product_detail->sub_cat_info['slug']) }}">{{ $product_detail->sub_cat_info['title'] }}</a>
                                            </p>
                                        @endif
                                        @if ($product_detail->size_chart)
                                            <p class="mt-3"> <a class="View-Size-Chart" href="javascript:void(0);"
                                                    data-toggle="modal" data-target="#sizeChartModal">
                                                    <b>View Size Chart</b>
                                                </a></p>
                                        @endif
                                        <div class="product-accordion">
                                            <div id="accordion" class="custom-accordion">
                                                    @if ($product_detail->product_features)
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <button class="btn btn-link" data-toggle="collapse"
                                                                    data-target="#collapseFeatures">
                                                                    <i class="fa fa-list-ul accordion-left-icon"></i>
                                                                    Features
                                                                    <span class="plus-minus">
                                                                        <i class="fa fa-plus"></i>
                                                                        <i class="fa fa-minus"></i>
                                                                    </span>
                                                                </button>
                                                            </div>
                                                            <div id="collapseFeatures" class="collapse show"
                                                                data-parent="#accordion">
                                                                <div class="card-body product-features">
                                                                    {!! $product_detail->product_features !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @php $productName = strtolower($product_detail->product_code); @endphp
                                                    @if (!in_array($productName, ['apt poncho', 'apn poncho', 'jlpc-101', 'rlpc-104']))
                                                        @if ($product_detail->cat_info->slug == 'rainwear')
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                        data-target="#collapseOne">
                                                                        <i
                                                                            class="fa fa-arrows-alt accordion-left-icon"></i>
                                                                        Size Guide
                                                                        <span class="plus-minus">
                                                                            <span class="fa fa-plus"></span>
                                                                            <span class="fa fa-minus"></span>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                                <div id="collapseOne" class="collapse"
                                                                    data-parent="#accordion">
                                                                    <div class="card-body">
                                                                        <ul>
                                                                            <li>Choose a raincoat that is one size larger
                                                                                than your regular shirt size for a
                                                                                comfortable fit.</li>
                                                                            <li>If you plan to wear a backpack inside the
                                                                                raincoat, we recommend choosing two sizes
                                                                                larger. (Do not Put this on APT, APN, JLPC,
                                                                                RLPC code)</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseTwo">
                                                                <i class="fa fa-truck accordion-left-icon"></i>
                                                                Shipping Instructions
                                                                <span class="plus-minus">
                                                                    <i class=" fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Orders are usually processed within 24–48 hours
                                                                        after confirmation.</li>
                                                                    <li>Delivery timelines may vary depending on your
                                                                        location and courier availability</li>
                                                                    <li>Please provide a complete and accurate shipping
                                                                        address with a valid contact number.</li>
                                                                    <li>Delivery timelines may be affected due to bad
                                                                        weather conditions or unforeseen courier delays.
                                                                    </li>
                                                                    <li>Once your order is shipped, tracking details
                                                                        will be shared via SMS or email.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseThree">
                                                                <i class="fa fa-exchange accordion-left-icon"></i>
                                                                7-Day Easy Exchange
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseThree" class="collapse"
                                                            data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Free Exchange Available.</li>
                                                                    <li>Request an exchange within 7 days of delivery.
                                                                    </li>
                                                                    <li>Exchange is not applicable on free products or
                                                                        promotional items.</li>
                                                                    <li>Only one-time exchange is allowed per order.
                                                                    </li>
                                                                    <li>If you receive a wrong product, please share an
                                                                        images of the Product for verification.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseFour">
                                                                <i class="fa fa-refresh accordion-left-icon"></i>
                                                                7-Day Easy Return & Refund
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseFour" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>We offer a 7-day return policy for eligible
                                                                        products.</li>
                                                                    <li>To be eligible for a return, the product must be
                                                                        unused and in its original condition and
                                                                        packaging.</li>
                                                                    <li>Returns cannot be initiated before the product
                                                                        is delivered.</li>
                                                                    <li>Refunds will be processed to the original
                                                                        payment method after the returned item is
                                                                        received and verified.</li>
                                                                    <li>For Cash on Delivery (COD) orders, customers
                                                                        will be required to provide bank account details
                                                                        for refund processing.</li>
                                                                    <li>Refunds are usually initiated within 2–4 working
                                                                        days after receiving the returned product.</li>
                                                                    <li>If you receive a wrong product, please share an
                                                                        images of the Product for verification purposes.
                                                                    </li>
                                                                    <li>In case your pin code is not serviceable for
                                                                        reverse pickup, you may be required to self-ship
                                                                        the product.</li>
                                                                    <li>Please do not accept the package if it appears
                                                                        tampered with or damaged.</li>
                                                                    <li>Do not share the OTP (One-Time Password) with
                                                                        the delivery partner unless you have received
                                                                        the package</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseFive">
                                                                <i class="fa fa-tint accordion-left-icon"></i>
                                                                Wash & Care Instructions
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseFive" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Clean the product using running water.</li>
                                                                    <li>Do not iron the garment.</li>
                                                                    <li>Do not tumble dry.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseSix">
                                                                <i class="fa fa-globe accordion-left-icon"></i>
                                                                Country Origin
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseSix" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>India</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseSeven">
                                                                <i class="fa fa-industry accordion-left-icon"></i>
                                                                Manufactured and Marketed By
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseSeven" class="collapse"
                                                            data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>NEW AASHI RAINWEAR, 843/2, NIDHI IND ESTATE,
                                                                        RAKANPUR, Gandhinagar, Gujarat, 382721</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    {{-- <section class="shop single pt-0 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="product-info mt-0">
                        <div class="nav-main">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#description"
                                        role="tab">Description</a></li>
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#reviews"
                                        role="tab">Reviews</a></li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel">
                                < class="tab-single">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="single-des">
                                                <p>{!! $product_detail->description !!}</p>
                                                <div id="accordion" class="custom-accordion mt-4">
                                                    @php $productName = strtolower($product_detail->product_code); @endphp
                                                    @if (!in_array($productName, ['apt poncho', 'apn poncho', 'jlpc-101', 'rlpc-104']))
                                                        @if ($product_detail->cat_info->slug == 'rainwear')
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <button class="btn btn-link" data-toggle="collapse"
                                                                        data-target="#collapseOne">
                                                                        <i
                                                                            class="fa fa-arrows-alt accordion-left-icon"></i>
                                                                        Size Guide
                                                                        <span class="plus-minus">
                                                                            <span class="fa fa-plus"></span>
                                                                            <span class="fa fa-minus"></span>
                                                                        </span>
                                                                    </button>
                                                                </div>
                                                                <div id="collapseOne" class="collapse show"
                                                                    data-parent="#accordion">
                                                                    <div class="card-body">
                                                                        <ul>
                                                                            <li>Choose a raincoat that is one size larger
                                                                                than your regular shirt size for a
                                                                                comfortable fit.</li>
                                                                            <li>If you plan to wear a backpack inside the
                                                                                raincoat, we recommend choosing two sizes
                                                                                larger. (Do not Put this on APT, APN, JLPC,
                                                                                RLPC code)</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseTwo">
                                                                <i class="fa fa-truck accordion-left-icon"></i>
                                                                Shipping Instructions
                                                                <span class="plus-minus">
                                                                    <i class=" fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Orders are usually processed within 24–48 hours
                                                                        after confirmation.</li>
                                                                    <li>Delivery timelines may vary depending on your
                                                                        location and courier availability</li>
                                                                    <li>Please provide a complete and accurate shipping
                                                                        address with a valid contact number.</li>
                                                                    <li>Delivery timelines may be affected due to bad
                                                                        weather conditions or unforeseen courier delays.
                                                                    </li>
                                                                    <li>Once your order is shipped, tracking details
                                                                        will be shared via SMS or email.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseThree">
                                                                <i class="fa fa-exchange accordion-left-icon"></i>
                                                                7-Day Easy Exchange
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseThree" class="collapse"
                                                            data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Free Exchange Available.</li>
                                                                    <li>Request an exchange within 7 days of delivery.
                                                                    </li>
                                                                    <li>Exchange is not applicable on free products or
                                                                        promotional items.</li>
                                                                    <li>Only one-time exchange is allowed per order.
                                                                    </li>
                                                                    <li>If you receive a wrong product, please share an
                                                                        images of the Product for verification.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseFour">
                                                                <i class="fa fa-refresh accordion-left-icon"></i>
                                                                7-Day Easy Return & Refund
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseFour" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>We offer a 7-day return policy for eligible
                                                                        products.</li>

                                                                    <li>To be eligible for a return, the product must be
                                                                        unused and in its original condition and
                                                                        packaging.</li>
                                                                    <li>Returns cannot be initiated before the product
                                                                        is delivered.</li>
                                                                    <li>Refunds will be processed to the original
                                                                        payment method after the returned item is
                                                                        received and verified.</li>
                                                                    <li>For Cash on Delivery (COD) orders, customers
                                                                        will be required to provide bank account details
                                                                        for refund processing.</li>
                                                                    <li>Refunds are usually initiated within 2–4 working
                                                                        days after receiving the returned product.</li>
                                                                    <li>If you receive a wrong product, please share an
                                                                        images of the Product for verification purposes.
                                                                    </li>
                                                                    <li>In case your pin code is not serviceable for
                                                                        reverse pickup, you may be required to self-ship
                                                                        the product.</li>
                                                                    <li>Please do not accept the package if it appears
                                                                        tampered with or damaged.</li>
                                                                    <li>Do not share the OTP (One-Time Password) with
                                                                        the delivery partner unless you have received
                                                                        the package</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseFive">
                                                                <i class="fa fa-tint accordion-left-icon"></i>
                                                                Wash & Care Instructions
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseFive" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>Clean the product using running water.</li>
                                                                    <li>Do not iron the garment.</li>
                                                                    <li>Do not tumble dry.</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseSix">
                                                                <i class="fa fa-globe accordion-left-icon"></i>
                                                                Country Origin
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseSix" class="collapse" data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>India</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <button class="btn btn-link collapsed" data-toggle="collapse"
                                                                data-target="#collapseSeven">
                                                                <i class="fa fa-industry accordion-left-icon"></i>
                                                                Manufactured and Marketed By
                                                                <span class="plus-minus">
                                                                    <i class="fa fa-plus"></i>
                                                                    <i class="fa fa-minus"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                        <div id="collapseSeven" class="collapse"
                                                            data-parent="#accordion">
                                                            <div class="card-body">
                                                                <ul>
                                                                    <li>NEW AASHI RAINWEAR, 843/2, NIDHI IND ESTATE,
                                                                        RAKANPUR, Gandhinagar, Gujarat, 382721</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                                        <div class="tab-single review-panel">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="ratting-main">
                                                        <div class="avg-ratting">
                                                            <h4>{{ ceil($product_detail->getReview->avg('rate')) }}
                                                                <span>(Overall)</span>
                                                            </h4>
                                                            <span>Based on {{ $product_detail->getReview->count() }}
                                                                Comments</span>
                                                        </div>
                                                        @foreach ($product_detail['getReview'] as $data)
                                                            <div class="single-rating">
                                                                <div class="rating-author">
                                                                    @if (isset($data->user_info['photo']) && $data->user_info['photo'])
                                                                        <img src="{{ asset('public/' . $data->user_info['photo']) }}"
                                                                            alt="{{ $data->user_info['photo'] }}">
                                                                    @else
                                                                        <img src="{{ asset('public/backend/img/avatar.png') }}"
                                                                            alt="Profile.jpg">
                                                                    @endif
                                                                </div>
                                                                <div class="rating-des">
                                                                    <h6>{{ $data->user_info['name'] ?? '' }}</h6>
                                                                    <div class="ratings">
                                                                        <ul class="rating">
                                                                            @for ($i = 1; $i <= 5; $i++)
                                                                                @if ($data->rate >= $i)
                                                                                    <li><i class="fa fa-star"></i></li>
                                                                                @else
                                                                                    <li><i class="fa fa-star-o"></i></li>
                                                                                @endif
                                                                            @endfor
                                                                        </ul>
                                                                        <div class="rate-count">
                                                                            (<span>{{ $data->rate }}</span>)
                                                                        </div>
                                                                    </div>
                                                                    <p>{{ $data->review }}</p>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section> --}}

    <section class="shop single pt-0 pb-5 product-lower">
        <div class="container">
            @if (trim(strip_tags($product_detail->description)) !== '')
                <div class="product-description">
                    <h3 class="pr-title">Product Description</h3>
                    <div class="pr-description">{!! $product_detail->description !!}</div>
                </div>
            @endif

            {{-- Customer reviews: summary (average + bars) on the left, review cards on the right --}}
            @php
                $reviews = $product_detail->getReview;
                $reviewTotal = $reviews->count();
                $reviewAvg = $reviewTotal > 0 ? round($reviews->avg('rate'), 1) : 0;
            @endphp
            <div class="product-reviews" id="reviews">
                <h3 class="pr-title">Customer Reviews</h3>
                @if ($reviewTotal > 0)
                    <div class="pr-grid">
                        <div class="pr-summary">
                            <div class="pr-average">{{ number_format($reviewAvg, 1) }}</div>
                            <div class="pr-stars" aria-label="{{ $reviewAvg }} out of 5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $reviewAvg >= $i ? 'fa-star' : ($reviewAvg >= $i - 0.5 ? 'fa-star-half-o' : 'fa-star-o') }}"></i>
                                @endfor
                            </div>
                            <div class="pr-based">Based on {{ $reviewTotal }} {{ $reviewTotal == 1 ? 'review' : 'reviews' }}</div>
                            <div class="pr-bars">
                                @for ($star = 5; $star >= 1; $star--)
                                    @php
                                        $starCount = $reviews->filter(fn($r) => (int) round($r->rate) === $star)->count();
                                        $starPercent = round(($starCount / $reviewTotal) * 100);
                                    @endphp
                                    <div class="pr-bar-row">
                                        <span class="pr-bar-label">{{ $star }} <i class="fa fa-star"></i></span>
                                        <span class="pr-bar"><span style="width: {{ $starPercent }}%"></span></span>
                                        <span class="pr-bar-count">{{ $starCount }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        <div class="pr-list">
                            @foreach ($reviews as $data)
                                <div class="pr-card">
                                    <div class="pr-card-head">
                                        @if ($data->user_info['photo'])
                                            <img src="{{ asset('public/' . $data->user_info['photo']) }}" alt="{{ $data->user_info['name'] ?? 'Customer' }}">
                                        @else
                                            {{-- no photo: first letters of the first and last name --}}
                                            @php
                                                $nameParts = preg_split('/\s+/', trim($data->user_info['name'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
                                                $initials = $nameParts
                                                    ? mb_strtoupper(mb_substr($nameParts[0], 0, 1) . (count($nameParts) > 1 ? mb_substr(end($nameParts), 0, 1) : ''))
                                                    : '?';
                                            @endphp
                                            <span class="pr-avatar" aria-hidden="true">{{ $initials }}</span>
                                        @endif
                                        <div class="pr-card-who">
                                            <strong>{{ $data->user_info['name'] ?? '' }}</strong>
                                            @if ($data->created_at)
                                                <small>{{ $data->created_at->format('d M Y') }}</small>
                                            @endif
                                        </div>
                                        <div class="pr-stars pr-stars-sm" aria-label="{{ $data->rate }} out of 5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fa {{ $data->rate >= $i ? 'fa-star' : 'fa-star-o' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="pr-card-text">{{ $data->review }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="pr-empty">No reviews yet for this product.</p>
                @endif
            </div>
        </div>
    </section>
    {{-- REVIEW SECTION --}}
    @auth
        @if ($canReview == true && $hasReviewed == false)
        @endif
    @endauth
    @if (!empty($product_detail->rel_prods) && $product_detail->rel_prods->count() > 1)
        <div class="product-area most-popular related-product section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-title">
                            <h2>Related Products</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="owl-carousel popular-slider">
                            @php
                                // Related products already in this visitor's wishlist (guests are tracked by their session)
                                $relatedWishlistRows = App\Models\Wishlist::whereNull('cart_id')
                                    ->whereIn('product_id', $product_detail->rel_prods->pluck('id'))
                                    ->where(Auth::check() ? 'user_id' : 'session_id', Auth::check() ? Auth::id() : session()->getId())
                                    ->get(['product_id', 'color_id']);
                            @endphp
                            @foreach ($product_detail->rel_prods as $data)
                                @if ($data->id !== $product_detail->id)
                                    @php
                                        $decoded = json_decode($data->size, true);
                                        $relPrice = is_array($decoded) && isset($decoded['price']) ? ($decoded['price'][0] ?? 0) : ($data->price ?? 0);
                                        $relFinal = $relPrice - ($relPrice * ($data->discount ?? 0)) / 100;
                                        $relPhoto = explode(',', $data->photo);
                                        $relColorId = $data->color->first()->id ?? null;
                                        $relInWishlist = $relatedWishlistRows->where('product_id', $data->id)->where('color_id', $relColorId)->isNotEmpty();
                                        $relReviewCount = $data->getReview->count();
                                        $relRate = $relReviewCount > 0 ? round($data->getReview->avg('rate'), 1) : 0;
                                    @endphp
                                    <!-- Start Single Product (same card as the product list page) -->
                                    <div class="related-card">
                                        <div class="custom-product-card">
                                            <div class="card-media-wrap">
                                                <a href="{{ route('product-detail', $data->slug) }}" class="card-img-link">
                                                    <img class="card-default-img" src="{{ asset('public' . $relPhoto[0]) }}"
                                                        alt="{{ $data->title }}" loading="lazy">
                                                    @if (isset($relPhoto[1]))
                                                        <img class="card-hover-img" src="{{ asset('public' . $relPhoto[1]) }}"
                                                            alt="{{ $data->title }}" loading="lazy">
                                                    @endif
                                                </a>
                                                @if ($data->stock <= 0)
                                                    <div class="card-left-badges">
                                                        <span class="card-badge-tag badge-hot">Out of Stock</span>
                                                    </div>
                                                @else
                                                    <button type="button" class="card-wishlist-btn {{ $relInWishlist ? 'active' : '' }}"
                                                        data-slug="{{ $data->slug }}" data-color-id="{{ $relColorId }}"
                                                        title="{{ $relInWishlist ? 'Remove from wishlist' : 'Add to wishlist' }}"
                                                        aria-label="Wishlist" aria-pressed="{{ $relInWishlist ? 'true' : 'false' }}">
                                                        <i class="fa {{ $relInWishlist ? 'fa-heart' : 'fa-heart-o' }}" aria-hidden="true"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="card-content-wrap">
                                                <div class="card-meta-line d-flex justify-content-between align-items-center">
                                                    <span class="card-cat-name">
                                                        <a href="{{ route('product-detail', $data->slug) }}">{{ $data->product_code }}</a>
                                                    </span>
                                                    @if ($relReviewCount > 0)
                                                        <div class="card-rating-inline">
                                                            <span>{{ number_format($relRate, 1) }}</span>
                                                            <i class="fa fa-star"></i>
                                                            <span>| {{ $relReviewCount }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <h4 class="card-item-title">
                                                    <a href="{{ route('product-detail', $data->slug) }}" title="{{ $data->title }}">
                                                        {!! html_entity_decode($data->title) !!}
                                                    </a>
                                                </h4>
                                                <div class="product-price my-2">
                                                    @if (!empty($data->discount))
                                                        <small class="original-price" style="font-size: 11px;">
                                                            <del class="text-muted">₹{{ number_format($relPrice, 2) }}</del>
                                                        </small>
                                                    @endif
                                                    <span class="final-price font-weight-bold" style="color: #0f172a; font-size: 15px;">
                                                        ₹{{ number_format(!empty($data->discount) ? $relFinal : $relPrice, 2) }}
                                                    </span>
                                                </div>
                                                <div class="card-cta-container">
                                                    <a href="{{ route('product-detail', $data->slug) }}" class="btn-card-details">
                                                        <span>View Details</span>
                                                        <i class="ti-arrow-right"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Single Product -->
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Checkout Authentication Modal (guest Buy Now flow: email -> login/register -> checkout) -->
    <div class="modal fade" id="checkoutAuthModal" tabindex="-1" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="checkoutAuthTitle">Login to Checkout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body text-center">
                    <form id="checkout-auth-form">
                        @csrf

                        <div id="step-email" class="auth-step">
                            <p>Please enter your email address to continue.</p>
                            <div class="form-group text-start">
                                <label>Email address</label>
                                <input type="email" name="email" id="checkout_email" class="form-control" required>
                                <span id="error_checkout_email" class="field-error d-none"></span>
                            </div>
                            <div class="d-flex flex-column align-items-center gap-2 mt-4">
                                <button type="button" id="btn-email-next" class="btn w-100">Continue</button>
                                <button type="button" class="btn-auth-secondary" data-dismiss="modal">Cancel</button>
                                <p class="mb-0 mt-2" style="font-size:13px; color:#1e293b;">
                                    Don't have an account?
                                    <a href="javascript:void(0);" id="btn-goto-signup"
                                        style="color:#5db845; font-weight:700; text-decoration:underline;">Sign Up</a>
                                </p>
                            </div>
                        </div>

                        <div id="step-login" class="auth-step d-none">
                            <p>This email is already registered. Please enter your password to continue.</p>
                            <div class="form-group text-start">
                                <label>Password</label>
                                <input type="password" name="password" id="checkout_password" class="form-control">
                                <span id="error_checkout_password" class="field-error d-none"></span>
                            </div>
                            <div class="d-flex flex-column align-items-center gap-2 mt-4">
                                <button type="submit" id="btn-login-submit" class="btn w-100">Login & Checkout</button>
                                <div class="d-flex justify-content-center align-items-center w-100 mt-3">
                                    <button type="button" id="btn-login-back" class="btn btn-link"
                                        style="padding:0;">&larr; Back</button>
                                    <button type="button" id="btn-forgot-password"
                                        style="background:none;border:none;font-size:12.5px; color:#5db845; font-weight:700; text-decoration:underline;  padding:0; cursor:pointer; margin-left:20px;">Forgot
                                        Password?</button>
                                </div>
                            </div>
                        </div>

                        <div id="step-forgot-password" class="auth-step d-none">
                            <p>We have sent a verification OTP to your <strong id="forgot-password-email"></strong> email
                                address.</p>
                            <div class="form-group text-start">
                                <label>Enter OTP</label>
                                <input type="text" name="forgot_otp" id="checkout_forgot_otp" class="form-control"
                                    maxlength="6" inputmode="numeric">
                                <span id="error_checkout_forgot_otp" class="field-error d-none"></span>
                            </div>
                            <p class="small text-muted mt-2 mb-0" style="text-align:left;">OTP is valid for 10 minutes.
                            </p>
                            <div class="d-flex flex-column align-items-center gap-2 mt-4">
                                <button type="button" id="btn-verify-forgot-otp" class="btn w-100">Verify OTP</button>
                                <button type="button" id="btn-resend-forgot-otp" class="btn-auth-secondary"
                                    disabled>Resend OTP <span id="forgot-otp-timer">(60s)</span></button>
                                <button type="button" id="btn-forgot-back" class="btn btn-link">&larr; Back</button>
                            </div>
                        </div>

                        <div id="step-reset-password" class="auth-step d-none">
                            <p>OTP verified successfully. Please create your new password.</p>
                            <div class="form-group text-start">
                                <label>New Password</label>
                                <input type="password" name="forgot_password" id="checkout_forgot_password"
                                    class="form-control">
                                <span id="error_checkout_forgot_password" class="field-error d-none"></span>
                            </div>
                            <div class="form-group text-start">
                                <label>Confirm New Password</label>
                                <input type="password" name="forgot_password_confirmation"
                                    id="checkout_forgot_password_confirmation" class="form-control">
                                <span id="error_checkout_forgot_password_confirmation" class="field-error d-none"></span>
                            </div>
                            <div class="d-flex flex-column align-items-center gap-2 mt-4">
                                <button type="button" id="btn-reset-forgot-password" class="btn w-100">Update
                                    Password</button>
                                <button type="button" id="btn-reset-password-back" class="btn btn-link">&larr;
                                    Back</button>
                            </div>
                        </div>

                        <div id="step-register" class="auth-step d-none">
                            <p>New here? Create an account to continue checkout.</p>
                            <div class="form-group text-start">
                                <label>Full Name</label>
                                <input type="text" name="name" id="checkout_name" class="form-control">
                                <span id="error_checkout_name" class="field-error d-none"></span>
                            </div>
                            <div class="form-group text-start">
                                <label>Email Address</label>
                                <input type="email" name="register_email" id="checkout_register_email"
                                    class="form-control" readonly>
                                <span id="error_checkout_register_email" class="field-error d-none"></span>
                            </div>
                            <div class="form-group text-start">
                                <label>Password (min 6 characters)</label>
                                <input type="password" name="reg_password" id="checkout_reg_password"
                                    class="form-control">
                                <span id="error_checkout_reg_password" class="field-error d-none"></span>
                            </div>
                            <div class="form-group text-start">
                                <label>Confirm Password</label>
                                <input type="password" name="reg_password_confirmation"
                                    id="checkout_reg_password_confirmation" class="form-control">
                                <span id="error_checkout_reg_password_confirmation" class="field-error d-none"></span>
                            </div>
                            <div class="d-flex flex-column align-items-center gap-2 mt-4">
                                <button type="submit" id="btn-register-submit" class="btn w-100">Register &
                                    Checkout</button>
                                <button type="button" id="btn-register-back" class="btn btn-link">&larr; Back</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Checkout Authentication Modal -->


    @if ($product_detail->size_chart)
        {{-- Size chart: a panel that slides in from the right (same id as before, so the "View Size Chart" link still opens it) --}}
        <div class="modal fade size-chart-modal" id="sizeChartModal" tabindex="-1" role="dialog"
            aria-label="Size Chart" aria-hidden="true">

            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="size-chart-section">
                            <button type="button" class="size-chart-toggle" data-toggle="collapse"
                                data-target="#sizeChartCollapse" aria-expanded="true" aria-controls="sizeChartCollapse">
                                <span class="size-chart-label"><i class="fa fa-arrows-alt" aria-hidden="true"></i>Size Chart</span>
                                <span class="size-chart-icon" aria-hidden="true"></span>
                            </button>
                            <div id="sizeChartCollapse" class="collapse show">
                                <div class="size-chart-content">
                                    {!! $product_detail->size_chart !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

@endsection
@push('styles')
    <style>
        /* ===== Variant stock: unavailable size / colour ===== */
        .color-selector.is-unavailable,
        .product-sizeoptions .size-selector.is-unavailable {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .product-sizeoptions .size-selector.is-unavailable {
            text-decoration: line-through;
        }

        #variant-stock-msg {
            margin: 8px 0 0;
            font-size: 13px;
            color: #c0392b;
        }

        /* Zoom on the main image: the photo stays inside its frame while it zooms */
        .main-banner .slick-slide {
            overflow: hidden;
        }

        .main-banner .slick-slide img {
            transition: transform 0.25s ease;
        }

        @media (hover: hover) {
            .main-banner .slick-slide img {
                cursor: zoom-in;
            }
        }

        /* ===== Gallery: thumbnails in a column on the left of the main image (all screen sizes) ===== */
        .product-gallery {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .product-gallery .small-banner {
            order: -1;
            flex: 0 0 64px;
            width: 64px;
            padding: 28px 0;
        }

        .product-gallery .main-banner {
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-gallery .small-banner .slick-slide {
            margin: 5px 0;
        }

        /* thumbnails keep the 3:4 shape of the photos, so the whole photo shows (no cropping) */
        .product-gallery .small-banner .slick-slide img,
        .product-gallery .small-banner .slick-slide video {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 3 / 4;
            object-fit: contain;
            background: #f3f3f3;
            border: 2px solid transparent;
            border-radius: 4px;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .product-gallery .small-banner .slick-current img,
        .product-gallery .small-banner .slick-slide img:hover {
            border-color: #5db845;
        }

        /* up / down arrows above and below the thumbnails (centred, so nothing sticks out at the sides) */
        .product-gallery .small-banner .slick-prev,
        .product-gallery .small-banner .slick-next {
            left: 50%;
            right: auto;
            transform: translateX(-50%) rotate(90deg);
        }

        .product-gallery .small-banner .slick-prev {
            top: 4px;
        }

        .product-gallery .small-banner .slick-next {
            top: auto;
            bottom: 4px;
        }

        /* tablets: a little wider thumbnail column */
        @media only screen and (min-width: 576px) {
            .product-gallery {
                gap: 10px;
            }

            .product-gallery .small-banner {
                flex-basis: 80px;
                width: 80px;
            }
        }

        /* desktops: widest column, and the gallery stays in view while the product details scroll past
           (top offset = the menu bar that sticks to the top of the screen) */
        @media only screen and (min-width: 992px) {
            .product-gallery {
                gap: 12px;
                position: sticky;
                top: 70px;
            }

            .product-gallery .small-banner {
                flex-basis: 96px;
                width: 96px;
                padding: 30px 0;
            }

            .product-gallery .small-banner .slick-slide {
                margin: 6px 0;
            }
        }
        /* ===== Gallery look: framed main photo, rounded thumbnails, less empty space above and below ===== */
        .product-gallery .main-banner {
            border-radius: 14px;
            overflow: hidden;
            background: #f3f3f3;
            box-shadow: 0 8px 26px rgba(0, 0, 0, 0.09);
        }

        .product-gallery .small-banner .slick-slide img,
        .product-gallery .small-banner .slick-slide video {
            border-radius: 8px;
        }

        .product-gallery .small-banner .slick-current img {
            box-shadow: 0 4px 12px rgba(93, 184, 69, 0.35);
        }

        @media only screen and (min-width: 768px) {
            /* the 30px theme margin above the gallery and details only added empty space under the breadcrumb */
            .shop.single .product-gallery,
            .shop.single .product-des {
                margin-top: 0;
            }

            .shop.single .product-des {
                padding-bottom: 0;
            }
        }

        /* ===== Size buttons: hover tooltip ===== */
        .shop.single .product-des .product-sizeoptions ul {
            margin-top: 14px;
        }

        .shop.single .product-des .product-sizeoptions ul li {
            margin-right: 8px;
        }

        .shop.single .product-des .product-sizeoptions ul li a {
            position: relative;
            display: block;
            width: auto;
            min-width: 44px;
            height: 40px;
            padding: 0 12px;
            line-height: 38px;
            font-weight: 500;
            color: #555;
            background: #f6f6f6;
            border: 1px solid #e6e6e6;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }

        /* hovered size: white box, dark border, soft shadow underneath */
        .shop.single .product-des .product-sizeoptions ul li a:hover {
            color: #222;
            background: #fff;
            border-color: #333;
            box-shadow: 0 8px 10px -6px rgba(0, 0, 0, 0.45);
        }

        /* selected size: theme green */
        .shop.single .product-des .product-sizeoptions ul li a.set_active,
        .shop.single .product-des .product-sizeoptions ul li a.set_active:hover {
            color: #fff !important;
            background: #5db845;
            border-color: #5db845;
        }

        /* tooltip above the size: dark label with a small arrow (only where a mouse can hover) */
        @media (hover: hover) {
            .shop.single .product-des .product-sizeoptions ul li a::after {
                content: attr(data-size-id);
                position: absolute;
                bottom: calc(100% + 10px);
                left: 50%;
                transform: translate(-50%, 4px);
                padding: 6px 30px;
                font-size: 13px;
                line-height: 1.3;
                white-space: nowrap;
                color: #fff;
                background: #444;
                border-radius: 4px;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
                z-index: 20;
            }

            .shop.single .product-des .product-sizeoptions ul li a::before {
                content: "";
                position: absolute;
                bottom: calc(100% + 4px);
                left: 50%;
                transform: translate(-50%, 4px);
                border: 6px solid transparent;
                border-top-color: #444;
                border-bottom: 0;
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
                z-index: 20;
            }

            .shop.single .product-des .product-sizeoptions ul li a:hover::after,
            .shop.single .product-des .product-sizeoptions ul li a:hover::before {
                opacity: 1;
                visibility: visible;
                transform: translate(-50%, 0);
            }
        }

        /* ===== Less empty space on the product page (tablets and desktops; phones keep their own spacing) ===== */
        @media only screen and (min-width: 768px) {
            .breadcrumbs {
                padding: 14px 0;
            }

            section.shop.single:not(.pt-0) {
                padding-top: 24px;
            }

            section.shop.single.pb-5:not(.pt-0) {
                padding-bottom: 24px !important;
            }

            .shop.single .product-des .short .description {
                margin-top: 10px;
                margin-bottom: 10px;
                padding-bottom: 12px;
            }

            .shop.single .product-des .color.mt-4,
            .shop.single .product-des .size.mt-4 {
                margin-top: 14px !important;
            }

            .shop.single .product-des #add-to-cart-wrapper .quantity.mt-3 {
                margin-top: 10px !important;
            }
        }
        /* ===== Smaller size, add to cart, wishlist and buy now buttons ===== */
        .shop.single .product-des .product-sizeoptions ul li {
            margin-right: 6px;
        }

        .shop.single .product-des .product-sizeoptions ul li a {
            height: 34px;
            line-height: 32px;
            padding: 0 10px;
            font-size: 13px;
            min-width: 38px;
        }

        .shop.single .product-des .add-to-cart {
            margin-top: 14px !important;
        }

        .shop.single .product-des .add-to-cart .btn {
            height: 38px;
            line-height: 36px;
            padding: 0 26px;
            font-size: 13px;
            margin-right: 2px;
        }

        .shop.single .product-des .add-to-cart .btn.min {
            padding: 0 14px;
            font-size: 15px;
        }

        /* ===== Smaller quantity box ===== */
        .shop.single .product-des .quantity .input-group {
            width: 120px;
        }

        .shop.single .product-des .quantity .input-number {
            width: 120px;
            height: 38px;
            padding: 0 30px;
            font-size: 13px;
        }

        .shop.single .product-des .quantity .button .btn {
            width: 30px;
            height: 38px;
            line-height: 38px;
            font-size: 11px;
        }

        /* ===== Purchase options: one tidy column (colour, size), then quantity + buttons on one row, then share ===== */
        .shop.single .product-des .color,
        .shop.single .product-des .size {
            display: block;
            margin-right: 0;
        }

        .shop.single .product-des .size h4 {
            margin-bottom: 0;
            color: #333;
            font-size: 14px !important;
            font-weight: 500 !important;
        }

        .shop.single .product-des .color ul,
        .shop.single .product-des .size ul {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 8px 0 0;
        }

        .shop.single .product-des .color ul li,
        .shop.single .product-des .size ul li {
            margin-right: 0;
        }

        .shop.single .product-des #add-to-cart-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px 16px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #eee;
        }

        .shop.single .product-des #add-to-cart-wrapper .quantity,
        .shop.single .product-des #add-to-cart-wrapper .add-to-cart {
            display: flex;
            align-items: center;
            margin: 0 !important;
        }

        .shop.single .product-des #add-to-cart-wrapper .quantity h6 {
            margin: 0 10px 0 0;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart {
            flex-wrap: wrap;
            gap: 8px;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart .btn {
            margin: 0;
        }

        .shop.single .product-des #add-to-cart-wrapper .product-share {
            flex: 0 0 100%;
            margin-top: 4px !important;
        }

        /* ===== Product info under the buttons: category, sub category and size chart share one tidy row (no big gaps) ===== */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px 22px;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > #add-to-cart-wrapper,
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:has(b) {
            flex: 0 0 100%;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:not(.mb-2),
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) {
            margin: 0 !important;
        }

        /* paragraphs that come out of the features text keep their own full-width lines */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:not(.cat):not(:has(> .View-Size-Chart)) {
            flex: 0 0 100%;
        }

        /* highlighted bar: category | sub category | size chart, with a divider between them */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) {
            column-gap: 0;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > #add-to-cart-wrapper + p,
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:not(:has(b)),
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border: 1px solid #dcefd5;
            background: #f4fbf1;
            margin: 0 -1px 0 0 !important;
            font-size: 14px;
            color: #6b7280;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:not(:has(b)) a,
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) a {
            color: #3f9a2c;
            font-weight: 600;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:not(:has(b)) a:hover,
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) a:hover {
            color: #2e7a1f;
        }

        /* rounded ends of the bar */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > #add-to-cart-wrapper + p {
            border-radius: 10px 0 0 10px;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart),
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:has(+ .product-accordion) {
            border-radius: 0 10px 10px 0;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > #add-to-cart-wrapper + p:has(+ p.cat.mb-2) {
            border-radius: 10px;
        }

        /* size chart link: no extra underline, it already sits in the bar */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) .View-Size-Chart {
            border-bottom: 0;
            margin-top: 0;
            text-decoration: underline;
        }

        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) .View-Size-Chart,
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p:has(> .View-Size-Chart) .View-Size-Chart b {
            font-weight: 400;
        }

        /* a thin line and some air above the product details (features) */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > p.cat:has(b) {
            margin-top: 10px !important;
            padding-top: 12px;
            border-top: 1px solid #eee;
        }

        .shop.single .product-des #add-to-cart-wrapper .product-share {
            margin-bottom: 2px;
        }

        .shop.single .product-des .product-share h6 {
            margin: 0;
        }

        /* ===== Tighter spacing in the top part of the details; colour and size share one row (they wrap on narrow screens) ===== */
        .shop.single .product-des .short p.price {
            margin-top: 8px;
        }

        .shop.single .product-des .color.mt-4,
        .shop.single .product-des .size.mt-4 {
            display: inline-block;
            vertical-align: bottom;
            margin: 8px 40px 0 0 !important;
        }

        .shop.single .product-des .color ul,
        .shop.single .product-des .size ul {
            min-height: 34px;
            align-items: flex-start;
            margin-top: 6px;
        }

        .shop.single .product-des .color h4 span {
            margin-top: 2px;
        }

        .shop.single .product-des #add-to-cart-wrapper {
            margin-top: 12px;
            padding-top: 12px;
        }

        /* ===== Price | rating on one line, smaller "Available Options", quantity lined up with the buttons ===== */
        .shop.single .product-des .short > .rating-main > ul.rating {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px 16px;
            width: auto;
            height: auto;
        }

        .shop.single .product-des .short > .rating-main > ul.rating > p.price {
            order: -1;
            margin: 0;
            padding-right: 16px;
            border-right: 1px solid #d9d9d9;
        }

        .shop.single .product-des .short > .rating-main > ul.rating > p.price:only-child {
            padding-right: 0;
            border-right: 0;
        }

        .shop.single .product-des .color h4 {
            font-size: 15px;
        }

        .shop.single .product-des #add-to-cart-wrapper .quantity.mt-3 {
            margin-top: 0 !important;
        }

        /* ===== Accordion under the buttons (Features, size guide, shipping ...) ===== */
        .shop.single .product-des .short > div:has(> #add-to-cart-wrapper) > .product-accordion {
            flex: 0 0 100%;
            margin-top: 16px;
        }

        .product-accordion .card {
            margin-bottom: 8px;
            border: 1px solid #e8efe5;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: none;
        }

        .product-accordion .card-header {
            padding: 0;
            border: 0;
            background: #f8fbf6;
        }

        .product-accordion .card-header .btn {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            text-decoration: none;
        }

        .product-accordion .card-body {
            padding: 12px 16px;
            font-size: 14px;
            line-height: 1.7;
            color: #555;
        }

        .product-accordion .card-body ul {
            margin: 0;
            padding-left: 18px;
        }

        .product-accordion .product-features p {
            margin: 0 0 6px;
        }

        /* ===== Description + customer reviews (below the product) ===== */
        .product-lower {
            padding-top: 10px;
        }

        .pr-title {
            margin: 0 0 16px;
            font-size: 22px;
            font-weight: 600;
            color: #222;
        }

        .product-description {
            margin-bottom: 30px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
        }

        .pr-description {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
        }

        .pr-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 30px;
            align-items: start;
        }

        .pr-summary {
            padding: 24px;
            border: 1px solid #e8efe5;
            border-radius: 14px;
            background: #f8fbf6;
            text-align: center;
        }

        .pr-average {
            font-size: 52px;
            font-weight: 700;
            line-height: 1;
            color: #222;
        }

        .pr-stars {
            margin: 8px 0 4px;
            color: #f7941d;
            font-size: 18px;
            letter-spacing: 2px;
        }

        .pr-stars-sm {
            margin: 0;
            font-size: 13px;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .pr-based {
            font-size: 14px;
            color: #777;
        }

        .pr-bars {
            margin-top: 18px;
        }

        .pr-bar-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13px;
            color: #555;
        }

        .pr-bar-label {
            flex: 0 0 34px;
            text-align: left;
            white-space: nowrap;
        }

        .pr-bar-label i {
            color: #f7941d;
            font-size: 11px;
        }

        .pr-bar {
            flex: 1;
            height: 8px;
            border-radius: 8px;
            background: #e5e5e5;
            overflow: hidden;
        }

        .pr-bar span {
            display: block;
            height: 100%;
            border-radius: 8px;
            background: #5db845;
        }

        .pr-bar-count {
            flex: 0 0 24px;
            text-align: right;
        }

        .pr-list {
            display: grid;
            gap: 14px;
        }

        .pr-card {
            padding: 18px 20px;
            border: 1px solid #ececec;
            border-radius: 14px;
            background: #fff;
            transition: box-shadow 0.25s ease;
        }

        .pr-card:hover {
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);
        }

        .pr-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .pr-card-head img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }

        .pr-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #5db845, #3f9a2c);
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .pr-card-who {
            flex: 1;
            min-width: 0;
            line-height: 1.3;
        }

        .pr-card-who strong {
            display: block;
            font-size: 15px;
            color: #222;
        }

        .pr-card-who small {
            color: #999;
        }

        .pr-card-text {
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
            color: #555;
        }

        .pr-empty {
            color: #777;
        }

        @media only screen and (max-width: 767px) {
            .pr-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .pr-card-head {
                flex-wrap: wrap;
            }
        }

        /* accordion: lighter, smaller version of the theme look (it only applies to the one under the buttons) */
        .product-accordion .custom-accordion .btn-link {
            padding: 12px 16px;
            gap: 10px;
            font-size: 14px;
        }

        .product-accordion .custom-accordion .accordion-left-icon {
            font-size: 15px;
        }

        .product-accordion .custom-accordion .btn-link:not(.collapsed) {
            background: #eef8ea;
            color: #2f7a1f;
        }

        .product-accordion .custom-accordion .btn-link:not(.collapsed) .accordion-left-icon {
            color: #3f9a2c;
        }

        .product-accordion .custom-accordion .card {
            margin-bottom: 8px;
        }

        .product-accordion .custom-accordion .card-body {
            padding: 12px 16px;
            line-height: 1.7;
        }

        /* breadcrumb: the product you are on stands out from the links before it */
        .breadcrumbs .bread-list li.active span {
            color: #3f9a2c;
            font-size: 14px;
            font-weight: 600;
            text-transform: capitalize;
        }

        /* smaller headings for the description and review blocks */
        .pr-title {
            position: relative;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e3eedf;
            font-size: 18px;
            color: #3f9a2c;
        }

        /* short theme-colour accent over the separator line */
        .pr-title::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -1px;
            width: 60px;
            height: 3px;
            border-radius: 3px;
            background: #5db845;
        }

        /* ===== Out of stock: a tidy card with the status and the "notify me" form ===== */
        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) {
            display: block;
            flex: 0 0 100%;
            padding: 14px 16px;
            border: 1px solid #f0e1e1;
            border-radius: 12px;
            background: #fffafa;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .out-of-stock-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 30px;
            line-height: 30px;
            padding: 0 14px;
            border: 0;
            border-radius: 30px;
            background: #fde8e8;
            color: #c0392b;
            font-size: 13px;
            font-weight: 600;
            opacity: 1;
            cursor: default;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form {
            margin-top: 12px !important;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form label {
            display: block;
            margin: 0 0 8px;
            font-size: 13px;
            font-weight: 500;
            color: #555;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form label i {
            margin-right: 4px;
            color: #3f9a2c;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form .form-control {
            flex: 1;
            height: 40px;
            border-radius: 8px;
            font-size: 14px;
        }

        .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form .btn {
            height: 40px;
            line-height: 38px;
            padding: 0 22px;
            border-radius: 8px;
            background: linear-gradient(135deg, #5db845, #3f9a2c);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        @media only screen and (max-width: 480px) {
            .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form .d-flex {
                flex-wrap: wrap;
                gap: 8px;
            }

            .shop.single .product-des #add-to-cart-wrapper .add-to-cart:has(.out-of-stock-btn) .notify-form .form-control {
                flex: 0 0 100%;
                margin-right: 0 !important;
            }
        }

        /* ===== Related products: same card as the product list page ===== */
        .related-card {
            padding: 4px 4px 10px;
        }

        .related-product .custom-product-card {
            background: #fff;
            border: 1px solid #e8edf2;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            width: 100%;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .related-product .custom-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.09);
            border-color: #cbd5e1;
        }

        .related-product .card-media-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 3 / 4;
            background: #f8fafc;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
        }

        .related-product .card-img-link {
            display: block;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .related-product .card-default-img,
        .related-product .card-hover-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: transform 0.4s ease, opacity 0.35s ease;
        }

        .related-product .card-hover-img {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
        }

        .related-product .custom-product-card:hover .card-default-img {
            transform: scale(1.05);
        }

        .related-product .custom-product-card:hover .card-hover-img {
            opacity: 1;
            transform: scale(1.05);
        }

        .related-product .card-left-badges {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 3;
        }

        .related-product .card-badge-tag.badge-hot {
            background: #e11d48;
            color: #fff;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }

        .related-product .card-content-wrap {
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
        }

        .related-product .card-meta-line {
            margin-bottom: 4px;
            height: 26px;
            overflow: hidden;
        }

        .related-product .card-cat-name a {
            color: #5db845;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .related-product .card-rating-inline {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 3px 7px;
            border-radius: 6px;
            background: #f7941d;
            color: #fff;
            font-size: 11px;
        }

        .related-product .card-rating-inline i {
            font-size: 10px;
        }

        .related-product .card-item-title {
            margin: 4px 0 6px;
            height: 2.7em;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .related-product .card-item-title a {
            color: #1e293b;
            text-decoration: none !important;
            transition: color 0.2s ease;
        }

        .related-product .card-item-title a:hover {
            color: #5db845;
        }

        .related-product .btn-card-details {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 8px 12px;
            background: #111827;
            color: #fff !important;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.25s ease;
        }

        .related-product .btn-card-details:hover {
            background: #5db845;
            box-shadow: 0 4px 12px rgba(93, 184, 69, 0.35);
        }

        .related-product .btn-card-details i {
            font-size: 11px;
            transition: transform 0.2s ease;
        }

        .related-product .btn-card-details:hover i {
            transform: translateX(4px);
        }

        /* wishlist heart on the cards */
        .related-product .card-wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 3;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: #fff;
            color: #555;
            font-size: 17px;
            line-height: 36px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
            cursor: pointer;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .related-product .card-wishlist-btn:hover {
            transform: scale(1.1);
            color: #e53935;
        }

        .related-product .card-wishlist-btn.active {
            color: #e53935;
        }

        .related-product .card-wishlist-btn:disabled {
            opacity: 0.7;
            cursor: wait;
        }
        /* Product title is an H1 (SEO) - same look as the old H4 */
        .shop.single .product-des .short h1 {
            font-size: 22px;
            font-weight: 600;
            margin-top: -5px;
            line-height: 28px;
        }

        @media only screen and (max-width: 767px) {
            .shop.single .product-des .short h1 {
                font-size: 20px;
                font-weight: 500;
                margin-top: 0;
                line-height: 24px;
            }
        }

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

        /* ===== Size chart: panel that slides in from the right ===== */
        #sizeChartModal .modal-dialog {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            max-width: 540px;
            height: 100%;
            margin: 0;
            padding: 0; /* the theme adds 35px top and bottom on phones */
        }

        /* slides in from the right instead of dropping from the top */
        #sizeChartModal.fade .modal-dialog {
            transform: translateX(100%);
            transition: transform 0.35s ease;
        }

        #sizeChartModal.show .modal-dialog {
            transform: translateX(0);
        }

        #sizeChartModal .modal-content {
            height: 100%;
            border: 0;
            border-radius: 0;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.18);
        }

        /* the theme (reset.css) places every modal header in the top-right corner: put it back in the normal flow here */
        #sizeChartModal .modal-header {
            position: relative;
            top: auto;
            right: auto;
            z-index: auto;
            flex: 0 0 auto;
            align-items: center;
            justify-content: flex-end;
            padding: 14px 20px;
            border-top: 4px solid #5db845;
            border-bottom: 1px solid #ececec;
        }

        #sizeChartModal .close {
            flex: 0 0 auto;
            margin: 0;
            padding: 0;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 50%;
            background: #f2f4f6;
            font-size: 26px;
            font-weight: 400;
            line-height: 36px;
            color: #333;
            opacity: 1;
            outline: none;
            box-shadow: none;
            transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
        }

        #sizeChartModal .close:hover {
            color: #fff;
            background: #5db845;
            border-color: transparent;
            transform: rotate(90deg);
        }

        #sizeChartModal .modal-body {
            flex: 1 1 auto;
            min-height: 0;
            max-height: none;
            padding: 0 28px 28px;
            overflow-y: auto;
        }

        /* ===== Size chart look: green theme ===== */

        /* collapsible "Size Chart" heading: a soft green bar (minus when open, plus when closed) */
        #sizeChartModal .size-chart-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin: 22px 0 18px;
            padding: 12px 14px;
            border: 0;
            border-radius: 10px;
            background: #eef8ea;
            font-size: 17px;
            font-weight: 600;
            color: #1f3a17;
            text-align: left;
            cursor: pointer;
            outline: none;
            transition: background 0.25s ease;
        }

        #sizeChartModal .size-chart-toggle:hover {
            background: #e3f3de;
        }

        #sizeChartModal .size-chart-label {
            display: flex;
            align-items: center;
        }

        #sizeChartModal .size-chart-label .fa {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            margin-right: 12px;
            border-radius: 50%;
            background: #5db845;
            color: #fff;
            font-size: 15px;
        }

        #sizeChartModal .size-chart-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #fff;
            color: #3f9a2c;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
        }

        #sizeChartModal .size-chart-icon::before {
            content: "\2212";
            font-size: 20px;
            font-weight: 400;
            line-height: 1;
        }

        #sizeChartModal .size-chart-toggle.collapsed .size-chart-icon::before {
            content: "+";
        }

        /* the chart itself */
        #sizeChartModal .size-chart-content {
            overflow-x: auto;
            padding: 0 2px 6px;
        }

        /* "SIZE GUIDE - All measurements are in inches": a small green label */
        #sizeChartModal .size-chart-content h4 {
            display: inline-block;
            margin: 0 0 16px;
            padding: 7px 14px;
            border-radius: 20px;
            background: #eef8ea;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            letter-spacing: 0.2px;
            color: #2f7d1f;
            text-transform: none;
        }

        #sizeChartModal .size-chart-content table {
            width: 100%;
            margin-bottom: 24px;
            border: 0;
            border-radius: 12px;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            font-size: 14px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        #sizeChartModal .size-chart-content table th,
        #sizeChartModal .size-chart-content table td {
            padding: 12px 14px;
            text-align: center;
            vertical-align: middle;
            border: 0;
            border-bottom: 1px solid #edf1ee;
        }

        #sizeChartModal .size-chart-content table tr:last-child td,
        #sizeChartModal .size-chart-content table tr:last-child th {
            border-bottom: 0;
        }

        /* first header row: the table title (TOP, BOTTOM ... or the column names when there is no title row) */
        #sizeChartModal .size-chart-content table thead tr:first-child th {
            background: #5db845;
            color: #fff;
            font-weight: 600;
            letter-spacing: 0.6px;
        }

        /* second header row: the column names (Particular, sizes ...) */
        #sizeChartModal .size-chart-content table thead tr:nth-child(2) th {
            background: #eef8ea;
            color: #1f3a17;
            font-weight: 600;
        }

        #sizeChartModal .size-chart-content table tbody tr:nth-child(even) td {
            background: #f8fbf7;
        }

        #sizeChartModal .size-chart-content table tbody tr {
            transition: background 0.2s ease;
        }

        #sizeChartModal .size-chart-content table tbody tr:hover td {
            background: #eaf6e6;
        }

        /* first column (the measurement names): left aligned, bold, with a green marker */
        #sizeChartModal .size-chart-content table tbody td:first-child,
        #sizeChartModal .size-chart-content table thead tr:nth-child(2) th:first-child {
            text-align: left;
            font-weight: 600;
            color: #222;
        }

        #sizeChartModal .size-chart-content table tbody td:first-child {
            border-left: 3px solid #5db845;
        }

        /* "Size Selection Tips": a heading with an icon, and the tips as soft cards with a check mark */
        #sizeChartModal .size-chart-content h5 {
            margin: 26px 0 14px;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.5;
            color: #1f3a17;
        }

        #sizeChartModal .size-chart-content h5::before {
            content: "\f0eb";
            font-family: FontAwesome;
            margin-right: 8px;
            color: #f7941d;
        }

        #sizeChartModal .size-chart-content ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sizeChartModal .size-chart-content ul li {
            position: relative;
            margin-bottom: 10px;
            padding: 12px 14px 12px 46px;
            border-radius: 10px;
            background: #f7faf6;
            font-size: 14px;
            line-height: 1.6;
            color: #444;
        }

        #sizeChartModal .size-chart-content ul li:last-child {
            margin-bottom: 0;
        }

        #sizeChartModal .size-chart-content ul li::before {
            content: "\f00c";
            font-family: FontAwesome;
            position: absolute;
            left: 12px;
            top: 12px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #5db845;
            color: #fff;
            font-size: 11px;
            line-height: 22px;
            text-align: center;
        }

        /* a chart that is a picture */
        #sizeChartModal .size-chart-content img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 0 auto 16px;
            border-radius: 10px;
        }

        @media (max-width: 575px) {
            #sizeChartModal .modal-header {
                padding: 12px 14px;
            }

            #sizeChartModal .modal-body {
                padding: 0 18px 22px;
            }

            #sizeChartModal .size-chart-toggle {
                margin: 16px 0 14px;
                font-size: 16px;
            }

            #sizeChartModal .size-chart-content table th,
            #sizeChartModal .size-chart-content table td {
                padding: 10px 8px;
                font-size: 13px;
            }

            #sizeChartModal .size-chart-content ul li {
                padding: 10px 12px 10px 42px;
                font-size: 13px;
            }
        }
        /* while the panel is open, the header cart / user icons (z-index 9999 in the theme) stay below it */
        body.size-chart-open .header .sinlge-bar.shopping,
        body.size-chart-open .header .mobile-user-menu {
            z-index: auto;
        }

        /* people who prefer less motion: the panel just appears */
        @media (prefers-reduced-motion: reduce) {
            #sizeChartModal.fade .modal-dialog {
                transition: none;
            }
        }

        .shop.single .product-des .color ul li a .selected {
            opacity: 1;
            visibility: visible;
        }

        /* Rating */
        .rating_box {
            display: inline-flex;
        }

        .star-rating {
            font-size: 0;
            padding-left: 10px;
            padding-right: 10px;
        }

        .star-rating__wrap {
            display: inline-block;
            font-size: 1rem;
        }

        .star-rating__wrap:after {
            content: "";
            display: table;
            clear: both;
        }

        .star-rating__ico {
            float: right;
            padding-left: 2px;
            cursor: pointer;
            color: #F7941D;
            font-size: 16px;
            margin-top: 5px;
        }

        .star-rating__ico:last-child {
            padding-left: 0;
        }

        .star-rating__input {
            display: none;
        }

        .star-rating__ico:hover:before,
        .star-rating__ico:hover~.star-rating__ico:before,
        .star-rating__input:checked~.star-rating__ico:before {
            content: "\F005";
        }

        .slick-slide {
            margin: 0 5px
        }

        .slick-prev:before,
        .slick-next:before {
            color: #5db845;
        }

        .slick-prev,
        .slick-next {
            z-index: 99;
        }

        .set_active {
            color: #5db845 !important;
        }

        a.btn.min.active {
            color: #fff;
            background: #5db845 !important;
        }

        .product-share {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .product-share h6 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
        }

        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            font-size: 18px;
            transition: 0.3s;
            text-decoration: none;
        }

        .share-btn:hover {
            transform: translateY(-3px);
        }

        .share-btn.whatsapp {
            background: #25D366;
        }

        .share-btn.facebook {
            background: #1877F2;
        }

        .share-btn.instagram {
            background: #E4405F;
        }

        .View-Size-Chart {
            border-bottom: 1px solid rgb(93, 184, 69);
            margin-top: 10px;
            color: #333;
        }
    </style>
@endpush

{{-- Checkout Auth Modal styling (guest Buy Now flow) --}}
@push('styles')
    <style>
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
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.25);
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
    <script src="{{ asset('public/frontend/js/sweetalert.min.js') }}"></script>

    <!-- <script>
        function setPriceId(element) {
            var priceId = element.getAttribute('data-price-id');
            var sizeId = element.getAttribute('data-size-id');
            var colorId = element.getAttribute('data-color-id');
            var colorName = element.getAttribute('data-color-name');

            var selectedPrice = document.getElementById('selected_price').value;
            var selectedSize = document.getElementById('selected_size').value;
            var selectedColor = document.getElementById('selected_color').value;
            var selectedColorName = document.getElementById('selected_color_name').value;

            document.getElementById('selected_size').value = sizeId != null ? sizeId : selectedSize;
            document.getElementById('selected_price').value = priceId != null ? priceId : selectedPrice;
            document.getElementById('selected_color').value = colorId != null ? colorId : selectedColor;
            document.getElementById('selected_color_name').value = colorName != null ? colorName : selectedColorName;

            if (priceId == null) {
                priceId = document.getElementById('selected_price').value;
            }

            const priceWrapper = document.querySelector('.price');
            const discountPercent = parseFloat(priceWrapper.dataset.discount || 0);
            const basePrice = parseFloat(priceId);
            let finalPrice = basePrice;

            if (discountPercent > 0) {
                finalPrice = basePrice - (basePrice * discountPercent / 100);
                const originalPriceEl = priceWrapper.querySelector('.original-price del');
                if (originalPriceEl) {
                    originalPriceEl.innerText = '₹' + basePrice.toFixed(2);
                }
            }
            document.getElementById('targetDivId').innerText = '₹' + finalPrice.toFixed(2);
            document.getElementById('selected_price').value = finalPrice.toFixed(2);

            const links = document.querySelectorAll('.one');
            links.forEach(link => {
                link.classList.remove('set_active');
            });
            element.classList.add('set_active');
            checkWishlistStatus();
        }

        function checkWishlistStatus() {
            let productId = "{{ $product_detail->id }}";
            let colorId = $('#selected_color').val();

            $.ajax({
                url: "{{ route('wishlist.check') }}",
                type: "GET",
                data: {
                    product_id: productId,
                    color_id: colorId
                },
                success: function(res) {
                    if (res.wishlisted) {
                        $('.add-to-wishlist').addClass('active');
                    } else {
                        $('.add-to-wishlist').removeClass('active');
                    }
                }
            });
        }

        function fetchColorImages(colorId) {
            var fetchUrl = '{{ url('get-color-images') }}' + '/' + colorId;
            $.ajax({
                url: fetchUrl,
                type: 'GET',
                success: function(response) {
                    $('.main-banner').slick('unslick');
                    $('.small-banner').slick('unslick');
                    $('.main-banner').empty();
                    $('.small-banner').empty();

                    $.each(response.images, function(index, image) {
                        $('.main-banner').append(
                            '<div><img src="' + image + '" alt="Main Image ' + (index + 1) +
                            '"></div>'
                        );
                    });
                    $.each(response.images, function(index, image) {
                        $('.small-banner').append(
                            '<div><img src="' + image + '" alt="Thumbnail Image ' + (index + 1) +
                            '"></div>'
                        );
                    });

                    $('.main-banner').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        fade: true,
                        asNavFor: '.small-banner'
                    });

                    $('.small-banner').slick({
                        slidesToShow: Math.min(4, $('.small-banner').children().length),
                        slidesToScroll: 1,
                        asNavFor: '.main-banner',
                        focusOnSelect: true,
                        arrows: true,
                        vertical: true,
                        verticalSwiping: true
                    });
                },
                error: function(xhr) {
                    console.error("Error fetching color images: ", xhr);
                }
            });
        }

        $(document).ready(function() {
            checkWishlistStatus();

            // ---- WISHLIST (AJAX, no page reload) ----
            $('.add-to-wishlist').on('click', function(e) {
                e.preventDefault();

                let btn = $(this);
                let baseUrl = "{{ route('add-to-wishlist', $product_detail->slug) }}";
                let colorId = $('#selected_color').val();

                $.ajax({
                    url: baseUrl,
                    method: "GET",
                    data: {
                        color_id: colorId
                    },
                    success: function(response) {
                        if (response.status) {
                            btn.toggleClass('active', response.wishlisted);
                            $('.wishlist-total-count').text(response.wishlist_count);
                            if (typeof refreshMiniWishlist === 'function') {
                                refreshMiniWishlist();
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#F7941D',
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: true,
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

            // ---- ADD TO CART ----
            $(document).on('click', '.add_to_cart_btn', function() {
                let productId = $(this).data('product-id');
                let qty = $('#quantity').val() || 1;

                $.ajax({
                    url: "{{ route('single-add-to-cart') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        slug: $('#product_slug').val(),
                        quant: {
                            1: qty
                        },
                        selected_size: $('#selected_size').val(),
                        selected_price: $('#selected_price').val(),
                        selected_color: $('#selected_color').val(),
                        selected_color_name: $('#selected_color_name').val()
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#F7941D',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let newCount = response.cart_count || 0;
                                    $('.total-count').text(newCount);
                                    if (typeof refreshMiniCart === 'function') {
                                        refreshMiniCart();
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: true,
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

            // ---- BUY NOW ----
            $(document).on('click', '.buy-now-btn', function(e) {
                    e.preventDefault();
                    let qty = $('#quantity').val() || 1;

                    $.ajax({
                            url: "{{ route('single-add-to-cart') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                slug: $('#product_slug').val(),
                                quant: {
                                    1: qty
                                },
                                selected_size: $('#selected_size').val(),
                                selected_price: $('#selected_price').val(),
                                selected_color: $('#selected_color').val(),
                                selected_color_name: $('#selected_color_name').val()
                            },
                            success: function(response) {
                                if (response.status) {
                                    if (typeof refreshMiniCart === 'function') {
                                        refreshMiniCart();
                                    }
                                    @auth
                                    window.location.href = "{{ route('checkout') }}";
                                @else
                                    $('.auth-step').addClass('d-none');
                                    $('#step-email').removeClass('d-none');
                                    $('#checkoutAuthTitle').text('Login to Checkout');
                                    $('#checkout_email').val('');
                                    $('.auth-step .field-error').addClass('d-none').text('');
                                    $('#checkoutAuthModal').addClass('show').css('display', 'flex');
                                    $('body').addClass('modal-open');
                                @endauth
                            } else {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning',
                                    text: response.message,
                                    showConfirmButton: true,
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

        // ---- COLOR SELECTOR ----
        var defaultColorId = $('#color-options .color-selector').first().data('color-id');

        $('.color-selector').on('click', function(e) {
            e.preventDefault();
            $('#color-options i').removeClass('selected');
            $(this).find('i').addClass('selected');
            var colorId = $(this).data('color-id');
            fetchColorImages(colorId);
        });

        $(document).on('click', '.thumbnail', function() {
            var newImageSrc = $(this).data('image');
            $('#main-img').attr('src', newImageSrc);
        });
        });
    </script> -->

        <script>
        // ===== Variant stock map: "size|colorId" => quantity (or "size|none") =====
        var variantStock = {};

        function variantKey(size, colorId) {
            return size + '|' + (colorId ? colorId : 'none');
        }

        function stockFor(size, colorId) {
            return parseInt(variantStock[variantKey(size, colorId)] || 0, 10) || 0;
        }

        function maxQty() {
            return stockFor($('#selected_size').val(), $('#selected_color').val());
        }

        // ===== Price (same discount rule as before) =====
        function applyPrice(basePriceRaw) {
            var $price = $('p.price[data-discount]').first();
            var discount = parseFloat($price.data('discount') || 0);
            var base = parseFloat(basePriceRaw);
            var finalPrice = discount > 0 ? base - (base * discount / 100) : base;

            if (discount > 0) {
                $price.find('.original-price del').text('₹' + base.toFixed(2));
            }
            $('#targetDivId').text('₹' + finalPrice.toFixed(2));
            $('#selected_price').val(finalPrice.toFixed(2));
        }

        function setSize($el) {
            $('#size-options .size-selector').removeClass('set_active');
            $el.addClass('set_active');
            $('#selected_size').val($el.attr('data-size-id'));
            applyPrice($el.attr('data-price-id'));
        }

        function setColor($el) {
            $('#color-options .color-selector').removeClass('set_active');
            $('#color-options i').removeClass('selected');
            $el.addClass('set_active');
            $el.find('i').addClass('selected');
            $('#selected_color').val($el.attr('data-color-id'));
            $('#selected_color_name').val($el.attr('data-color-name'));
        }

        // Greys out unavailable options, switches away from them, and sets the quantity limit and buttons
        function refreshVariantState() {
            var $sizes  = $('#size-options .size-selector');
            var $colors = $('#color-options .color-selector');

            // 1. A size is available when at least one colour has stock in it
            $sizes.each(function () {
                var size = $(this).attr('data-size-id');
                var ok = $colors.length
                    ? $colors.toArray().some(function (c) { return stockFor(size, $(c).attr('data-color-id')) > 0; })
                    : stockFor(size, '') > 0;
                $(this).toggleClass('is-unavailable', !ok);
            });

            if ($sizes.filter('.set_active').hasClass('is-unavailable')) {
                var $firstSize = $sizes.not('.is-unavailable').first();
                if ($firstSize.length) {
                    setSize($firstSize);
                }
            }

            // 2. Colours with no stock in the chosen size are greyed out
            var size = $('#selected_size').val();
            $colors.each(function () {
                var out = stockFor(size, $(this).attr('data-color-id')) <= 0;
                $(this).toggleClass('is-unavailable', out)
                       .attr('title', out ? 'Out of stock in this size' : $(this).attr('data-color-name'));
            });

            if ($colors.filter('.set_active').hasClass('is-unavailable')) {
                var $firstColor = $colors.not('.is-unavailable').first();
                if ($firstColor.length) {
                    setColor($firstColor);
                    fetchColorImages($firstColor.attr('data-color-id'));
                }
            }

            // 3. Quantity limit and buttons for this size and colour
            var max = maxQty();
            var $qty = $('#quantity');
            var val = Math.min(Math.max(parseInt($qty.val(), 10) || 1, 1), Math.max(max, 1));
            $qty.val(val).attr('data-max', max);
            $('.quantity .button.minus button').prop('disabled', val <= 1);
            $('.quantity .button.plus button').prop('disabled', max <= 0 || val >= max);

            var inStock = max > 0;
            $('.add_to_cart_btn, .buy-now-btn').prop('disabled', !inStock);

            var msg = '';
            if (!inStock) {
                msg = 'This size and colour is out of stock.';
            } else if (max <= 5) {
                msg = 'Only ' + max + ' left in this size and colour.';
            }
            $('#variant-stock-msg').text(msg).toggle(msg !== '');
        }

        // Plus / minus buttons: capture phase so the theme's own handler does not change the value too
        document.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('.quantity .btn-number') : null;
            if (!btn || btn.disabled) {
                return;
            }
            e.preventDefault();
            e.stopImmediatePropagation();

            var input = document.getElementById('quantity');
            var max = Math.max(maxQty(), 1);
            var val = parseInt(input.value, 10) || 1;

            val = btn.getAttribute('data-type') === 'plus' ? Math.min(val + 1, max) : Math.max(val - 1, 1);
            input.value = val;
            refreshVariantState();
        }, true);

        function checkWishlistStatus() {
            let productId = "{{ $product_detail->id }}";
            let colorId = $('#selected_color').val();

            $.ajax({
                url: "{{ route('wishlist.check') }}",
                type: "GET",
                data: {
                    product_id: productId,
                    color_id: colorId
                },
                success: function(res) {
                    if (res.wishlisted) {
                        $('.add-to-wishlist').addClass('active');
                    } else {
                        $('.add-to-wishlist').removeClass('active');
                    }
                }
            });
        }

        function fetchColorImages(colorId) {
            var fetchUrl = '{{ url('get-color-images') }}' + '/' + colorId;
            $.ajax({
                url: fetchUrl,
                type: 'GET',
                success: function(response) {
                    $('.main-banner').slick('unslick');
                    $('.small-banner').slick('unslick');
                    $('.main-banner').empty();
                    $('.small-banner').empty();

                    $.each(response.images, function(index, image) {
                        $('.main-banner').append(
                            '<div><img src="' + image + '" alt="Main Image ' + (index + 1) + '"></div>'
                        );
                    });
                    $.each(response.images, function(index, image) {
                        $('.small-banner').append(
                            '<div><img src="' + image + '" alt="Thumbnail Image ' + (index + 1) + '"></div>'
                        );
                    });

                    $('.main-banner').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        fade: true,
                        asNavFor: '.small-banner'
                    });

                    $('.small-banner').slick({
                        slidesToShow: Math.min(4, $('.small-banner').children().length),
                        slidesToScroll: 1,
                        asNavFor: '.main-banner',
                        focusOnSelect: true,
                        arrows: true,
                        vertical: true,
                        verticalSwiping: true
                    });
                },
                error: function(xhr) {
                    console.error("Error fetching color images: ", xhr);
                }
            });
        }

        $(document).ready(function() {
            // Read the stock map
            var stockEl = document.getElementById('variant-stock');
            try {
                variantStock = stockEl ? (JSON.parse(stockEl.textContent) || {}) : {};
            } catch (err) {
                variantStock = {};
            }

            // Start with the default size's price (with discount), then set availability
            var $activeSize = $('#size-options .size-selector.set_active').first();
            if ($activeSize.length) {
                applyPrice($activeSize.attr('data-price-id'));
            }
            refreshVariantState();
            checkWishlistStatus();

            // ---- Size and colour clicks ----
            $(document).on('click', '#size-options .size-selector', function(e) {
                e.preventDefault();
                if ($(this).hasClass('is-unavailable')) return;
                setSize($(this));
                refreshVariantState();
            });

            $(document).on('click', '#color-options .color-selector', function(e) {
                e.preventDefault();
                if ($(this).hasClass('is-unavailable')) return;
                setColor($(this));
                fetchColorImages($(this).attr('data-color-id'));
                checkWishlistStatus();
                refreshVariantState();
            });

            // Typed quantity
            $(document).on('change blur', '#quantity', refreshVariantState);

            // ---- WISHLIST (AJAX, no page reload) ----
            $('.add-to-wishlist').on('click', function(e) {
                e.preventDefault();

                let btn = $(this);
                let baseUrl = "{{ route('add-to-wishlist', $product_detail->slug) }}";
                let colorId = $('#selected_color').val();

                $.ajax({
                    url: baseUrl,
                    method: "GET",
                    data: {
                        color_id: colorId
                    },
                    success: function(response) {
                        if (response.status) {
                            btn.toggleClass('active', response.wishlisted);
                            $('.wishlist-total-count').text(response.wishlist_count);
                            if (typeof refreshMiniWishlist === 'function') {
                                refreshMiniWishlist();
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#F7941D',
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: true,
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

            // ---- ADD TO CART ----
            $(document).on('click', '.add_to_cart_btn', function() {
                let qty = $('#quantity').val() || 1;

                $.ajax({
                    url: "{{ route('single-add-to-cart') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        slug: $('#product_slug').val(),
                        quant: {
                            1: qty
                        },
                        selected_size: $('#selected_size').val(),
                        selected_price: $('#selected_price').val(),
                        selected_color: $('#selected_color').val(),
                        selected_color_name: $('#selected_color_name').val()
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                showConfirmButton: true,
                                confirmButtonColor: '#F7941D',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('.total-count').text(response.cart_count || 0);
                                    if (typeof refreshMiniCart === 'function') {
                                        refreshMiniCart();
                                    }
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: true,
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

            // ---- BUY NOW ----
            $(document).on('click', '.buy-now-btn', function(e) {
                e.preventDefault();
                let qty = $('#quantity').val() || 1;

                $.ajax({
                    url: "{{ route('single-add-to-cart') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        slug: $('#product_slug').val(),
                        quant: {
                            1: qty
                        },
                        selected_size: $('#selected_size').val(),
                        selected_price: $('#selected_price').val(),
                        selected_color: $('#selected_color').val(),
                        selected_color_name: $('#selected_color_name').val()
                    },
                    success: function(response) {
                        if (response.status) {
                            if (typeof refreshMiniCart === 'function') {
                                refreshMiniCart();
                            }
                            @auth
                                window.location.href = "{{ route('checkout') }}";
                            @else
                                $('.auth-step').addClass('d-none');
                                $('#step-email').removeClass('d-none');
                                $('#checkoutAuthTitle').text('Login to Checkout');
                                $('#checkout_email').val('');
                                $('.auth-step .field-error').addClass('d-none').text('');
                                $('#checkoutAuthModal').addClass('show').css('display', 'flex');
                                $('body').addClass('modal-open');
                            @endauth
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: response.message,
                                showConfirmButton: true,
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

            $(document).on('click', '.thumbnail', function() {
                var newImageSrc = $(this).data('image');
                $('#main-img').attr('src', newImageSrc);
            });
        });
    </script>

    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel/slick/slick.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.main-banner').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                arrows: false,
                fade: false,
                asNavFor: '.small-banner'
            });

            // Thumbnails: a vertical column on the left of the main image (CSS sizes it for the screen)
            $('.small-banner').slick({
                slidesToShow: Math.min(4, $('.small-banner').children().length),
                slidesToScroll: 1,
                asNavFor: '.main-banner',
                focusOnSelect: true,
                arrows: true,
                vertical: true,
                verticalSwiping: true
            });

            // Thumbnails: the mouse wheel scrolls them up / down (the page scrolls as usual once the first or last one is reached).
            // Added with passive:false, otherwise the browser would not let us stop the page from scrolling too.
            var thumbs = document.querySelector(".small-banner");
            if (thumbs) {
                var wheelBusy = false;
                thumbs.addEventListener("wheel", function(e) {
                    var slick = $(thumbs).slick("getSlick");
                    var down = e.deltaY > 0;
                    var last = slick.slideCount - slick.options.slidesToShow;
                    if ((down && slick.currentSlide < last) || (!down && slick.currentSlide > 0)) {
                        e.preventDefault();
                        if (!wheelBusy) {
                            wheelBusy = true;
                            $(thumbs).slick(down ? "slickNext" : "slickPrev");
                            setTimeout(function() { wheelBusy = false; }, 250);
                        }
                    }
                }, { passive: false });
            }

            // Wishlist heart on the related product cards: adds / removes without a page reload (works without login too)
            $(document).on('click', '.related-product .card-wishlist-btn', function() {
                var btn = $(this);
                btn.prop('disabled', true);
                $.ajax({
                    url: "{{ url('wishlist') }}/" + btn.data('slug'),
                    method: 'GET',
                    data: { color_id: btn.data('color-id') },
                    success: function(response) {
                        if (response.status) {
                            btn.toggleClass('active', response.wishlisted);
                            btn.attr('aria-pressed', response.wishlisted ? 'true' : 'false');
                            btn.attr('title', response.wishlisted ? 'Remove from wishlist' : 'Add to wishlist');
                            btn.find('i').toggleClass('fa-heart', response.wishlisted).toggleClass('fa-heart-o', !response.wishlisted);
                            $('.wishlist-total-count').text(response.wishlist_count);
                            if (window.appRoutes && window.appRoutes.miniWishlist) {
                                $.get(window.appRoutes.miniWishlist, function(html) {
                                    $('#mini-wishlist-box').html(html);
                                });
                            }
                        }
                        Swal.fire({ toast: true, position: 'top-end', icon: response.status ? 'success' : 'warning', title: response.message, showConfirmButton: false, timer: 2000 });
                    },
                    error: function() {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Something went wrong!', showConfirmButton: false, timer: 2000 });
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
            // Size chart panel: mark the page while it is open (used by the CSS above)
            $('#sizeChartModal')
                .on('show.bs.modal', function() { $('body').addClass('size-chart-open'); })
                .on('hidden.bs.modal', function() { $('body').removeClass('size-chart-open'); });

            // Zoom on the main image (devices with a mouse only): it zooms in and follows the mouse pointer.
            // Events are attached to the page, so images added when the colour changes zoom too.
            if (window.matchMedia('(hover: hover)').matches) {
                $(document)
                    .on('mouseenter', '.main-banner .slick-slide img', function() {
                        $(this).css('transform', 'scale(1.8)');
                    })
                    .on('mousemove', '.main-banner .slick-slide img', function(e) {
                        // position inside the (unzoomed) frame, so the zoom follows the pointer
                        var frame = $(this).closest('.slick-slide')[0].getBoundingClientRect();
                        var x = ((e.clientX - frame.left) / frame.width) * 100;
                        var y = ((e.clientY - frame.top) / frame.height) * 100;
                        $(this).css('transform-origin', x + '% ' + y + '%');
                    })
                    .on('mouseleave', '.main-banner .slick-slide img', function() {
                        $(this).css('transform', 'scale(1)');
                    });
            }
        });
    </script>
@endpush
{{-- Checkout Auth Modal wiring (email check / login / register / forgot-password AJAX from cart-ajax.js, reused here) --}}
@push('scripts')
    <script>
        window.appCsrfToken = window.appCsrfToken || "{{ csrf_token() }}";
        window.appRoutes = window.appRoutes || {};
        window.appRoutes.checkoutCheckEmail = "{{ route('checkout.check-email') }}";
        window.appRoutes.checkoutLogin = "{{ route('checkout.login') }}";
        window.appRoutes.checkoutRegister = "{{ route('checkout.register') }}";
        window.appRoutes.checkoutForgotSendOtp = "{{ route('checkout.forgot-password.send-otp') }}";
        window.appRoutes.checkoutForgotVerifyOtp = "{{ route('checkout.forgot-password.verify-otp') }}";
        window.appRoutes.checkoutForgotReset = "{{ route('checkout.forgot-password.reset') }}";
        window.appRoutes.miniCart = window.appRoutes.miniCart || "{{ route('header.mini-cart') }}";
        window.appRoutes.miniWishlist = window.appRoutes.miniWishlist || "{{ route('header.mini-wishlist') }}";
    </script>
    <script src="{{ asset('public/frontend/js/cart-ajax.js') }}"></script>
@endpush
