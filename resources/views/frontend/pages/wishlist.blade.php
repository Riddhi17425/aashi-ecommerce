@extends('frontend.layouts.master')
@section('title', 'Aashi-Ecommerce || Wishlist Page')
@section('main-content')
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ 'home' }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Wishlist</a></li>
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
                                <th class="text-center">TOTAL</th>
                                <th class="text-center">ADD TO CART</th>
                                <th class="text-center"><i class="ti-trash remove-icon"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (Helper::getAllProductFromWishlist())
                                @foreach (Helper::getAllProductFromWishlist() as $key => $wishlist)
                                    @php
                                        $photo = explode(',', $wishlist->product['photo']);
                                        $wishlistSizeData = json_decode($wishlist->product['size'], true);
                                        $wishlistDefaultSize = $wishlistSizeData['size'][0] ?? '';
                                    @endphp
                                    <tr id="wishlist-row-{{ $wishlist->id }}">
                                        <td class="image" data-title="No">
                                            @if (isset($wishlist->color_img) && $wishlist->color_img != null)
                                                <img src="{{ $wishlist->color_img }}" alt="{{ $wishlist->color_img }}">
                                            @else
                                                <img src="{{ asset('public/' . $photo[0]) }}"
                                                    alt="{{ asset('public/' . $photo[0]) }}">
                                            @endif
                                        </td>
                                        <td class="product-des" data-title="Description">
                                            <p class="product-name"><a
                                                    href="{{ route('product-detail', $wishlist->product['slug']) }}">{{ $wishlist->product['product_code'] }}
                                                    @if (isset($wishlist->color_id) && $wishlist->color_id != null)
                                                        ({{ optional($wishlist->color)->color_name }})
                                                    @endif
                                                </a></p>
                                            <p class="product-des">{!! $wishlist['summary'] !!}</p>
                                        </td>
                                        <td class="total-amount" data-title="Total"><span>₹{{ $wishlist['amount'] }}</span>
                                        </td>
                                        <td>
                                            @if ($wishlist->product['stock'] > 0)
                                                <button type="button" class="btn text-white wishlist-add-to-cart-btn"
                                                    data-slug="{{ $wishlist->product['slug'] }}"
                                                    data-price="{{ $wishlist->price }}"
                                                    data-size="{{ $wishlistDefaultSize }}"
                                                    data-color="{{ $wishlist->color_id }}">
                                                    Add To Cart
                                                </button>
                                            @else
                                                <span class="text-danger">Out of Stock</span>
                                            @endif
                                        </td>
                                        <td class="action" data-title="Remove"><a
                                                href="{{ route('wishlist-delete', $wishlist->id) }}"><i
                                                    class="ti-trash remove-icon"></i></a></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center">
                                        There are no any wishlist available. <a href="{{ route('product-grids') }}"
                                            style="color:blue;">Continue shopping</a>

                                    </td>
                                </tr>
                            @endif


                        </tbody>
                    </table>
                    <!--/ End Shopping Summery -->
                </div>
            </div>
        </div>
    </div>
    <!--/ End Shopping Cart -->

    <!-- Start Shop Services Area  -->
    <section class="shop-services section mb-3">
        <div class="container">
            <div class="row">
            </div>
        </div>
    </section>
    <!-- End Shop Newsletter -->

@endsection
@push('scripts')
<script src="{{ asset('public/frontend/js/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.wishlist-add-to-cart-btn', function() {
        let btn = $(this);
        let slug  = btn.data('slug');
        let price = btn.data('price');
        let size  = btn.data('size');
        let color = btn.data('color');

        btn.prop('disabled', true).text('Adding...');

        $.ajax({
            url: "{{ route('single-add-to-cart') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                slug: slug,
                quant: { 1: 1 },
                selected_size: size,
                selected_price: price,
                selected_color: color
            },
            success: function(response) {
                btn.prop('disabled', false).text('Add To Cart');
                if (response.status) {
                    // Header count turant update - Swal se independent
                    let newCount = response.cart_count || 0;
                    $('.total-count').text(newCount);

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
                btn.prop('disabled', false).text('Add To Cart');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong!',
                });
            }
        });
    });
});
</script>
@endpush
