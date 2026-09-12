@extends('frontend.layouts.master')

@section('title','Aashi-Ecommerce || PRODUCT PAGE')

@section('main-content')
	<!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{route('home')}}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0);">Shop Grid</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Off-Canvas Filter Drawer -->
    <div id="filter-offcanvas" class="filter-offcanvas">
        <div class="offcanvas-header">
            <h5><i class="ti-filter" style="color: #5db845;"></i> Filter Products</h5>
            <button type="button" class="close-offcanvas" id="close-filter-btn">&times;</button>
        </div>
        <div class="offcanvas-body">
            <div class="shop-sidebar p-0">
                <!-- Single Widget -->
                <div class="single-widget category mb-4">
                    <h3 class="title">Categories</h3>
                    <ul class="categor-list">
                        @php
                            $menu=App\Models\Category::getAllParentWithChild();
                        @endphp
                        @if($menu)
                            @foreach($menu as $cat_info)
                                @if($cat_info->child_cat->count()>0)
                                    <li><a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a>
                                        <ul>
                                            @foreach($cat_info->child_cat as $sub_menu)
                                                <li><a href="{{route('product-sub-cat',[$cat_info->slug,$sub_menu->slug])}}">{{$sub_menu->title}}</a></li>
                                            @endforeach
                                        </ul>
                                    </li>
                                @else
                                    <li><a href="{{route('product-cat',$cat_info->slug)}}">{{$cat_info->title}}</a></li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Shop By Price -->
                <form action="{{route('shop.filter')}}" method="POST">
                    @csrf
                    <div class="single-widget range mb-4">
                        <h3 class="title">Shop by Price</h3>
                        <div class="price-filter">
                            <div class="price-filter-inner">
                                @php
                                    $max=DB::table('products')->max('price');
                                @endphp
                                <div id="slider-range" data-min="0" data-max="{{$max}}"></div>
                                <div class="product_filter mt-3">
                                    <button type="submit" class="filter_button btn btn-sm btn-dark">Filter</button>
                                    <div class="label-input mt-2">
                                        <span>Range:</span>
                                        <input type="text" id="amount" readonly/>
                                        <input type="hidden" name="price_range" id="price_range" value="@if(!empty($_GET['price'])){{$_GET['price']}}@endif"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Brands Widget -->
                <div class="single-widget category">
                    <h3 class="title">Brands</h3>
                    <ul class="categor-list">
                        @php
                            $brands=DB::table('brands')->orderBy('title','ASC')->where('status','active')->get();
                        @endphp
                        @foreach($brands as $brand)
                            <li><a href="{{route('product-brand',$brand->slug)}}">{{$brand->title}}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="filter-overlay" class="filter-overlay"></div>

    <!-- Product Style -->
    <section class="product-area shop-sidebar shop section pt-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="row mb-3">
                        <div class="col-12">
                            <!-- Shop Top -->
                            <div class="shop-top d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <button type="button" class="btn-filter-toggle" id="open-filter-btn">
                                    <i class="ti-filter"></i> Filter
                                </button>
                                <div class="shop-shorter m-0">
                                    <div class="single-shorter">
                                        <label>Show :</label>
                                        <select class="show" name="show" onchange="this.form.submit();">
                                            <option value="">Default</option>
                                            <option value="9" @if(!empty($_GET['show']) && $_GET['show']=='9') selected @endif>09</option>
                                            <option value="15" @if(!empty($_GET['show']) && $_GET['show']=='15') selected @endif>15</option>
                                            <option value="21" @if(!empty($_GET['show']) && $_GET['show']=='21') selected @endif>21</option>
                                            <option value="30" @if(!empty($_GET['show']) && $_GET['show']=='30') selected @endif>30</option>
                                        </select>
                                    </div>
                                    <div class="single-shorter">
                                        <label>Sort By :</label>
                                        <select class='sortBy' name='sortBy' onchange="this.form.submit();">
                                            <option value="">Default</option>
                                            <option value="title" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='title') selected @endif>Name</option>
                                            <option value="price" @if(!empty($_GET['sortBy']) && $_GET['sortBy']=='price') selected @endif>Price</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--/ End Shop Top -->
                        </div>
                    </div>
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                                        <div class="single-product">
                                            <div class="product-img">
                                                <a href="{{route('product-detail',$product->slug)}}">
                                                    @php
                                                        $photo=explode(',',$product->photo);
                                                    @endphp
                                                    <img class="default-img" src="{{asset('public/'.$photo[0])}}" alt="{{asset('public/'.$photo[0])}}">
                                                    @if(isset($photo[1]))
                                                        <img class="hover-img" src="{{asset('public/'.$photo[1])}}" alt="{{asset('public/'.$photo[0])}}">
                                                    @endif
                                                    @if($product->discount)
                                                                <span class="price-dec">{{$product->discount}} % Off</span>
                                                    @endif
                                                </a>
                                                <div class="button-head">
    <div class="product-action">
        <a data-toggle="modal" data-target="#{{$product->id}}" title="Quick View" href="#"><i class=" ti-eye"></i><span>Quick Shop</span></a>
        <a title="Wishlist" href="{{route('add-to-wishlist',$product->slug)}}" class="wishlist" data-id="{{$product->id}}"><i class=" ti-heart "></i><span>Add to Wishlist</span></a>
    </div>
    <div class="product-action-2">
        <a title="Add to cart" href="javascript:void(0);" class="quick-add-to-cart-btn" data-slug="{{ $product->slug }}">Add to cart</a>
    </div>
</div>
                                            </div>
                                            <div class="product-content">
                                                <h3><a href="{{route('product-detail',$product->slug)}}">{{$product->product_code }}</a></h3>
                                                @php
                                                    // $after_discount=($product->price-($product->price*$product->discount)/100);
                                                    $sizeData = json_decode($product->size, true);

                                                    $sizes = json_decode($product->size);
                                                    $priceArr = $sizes->price;
                                                    $productPrice = 0;
                                                    //foreach($priceArr as $k => $v){
                                                        $productPrice = $priceArr[0];
                                                    //}
                                                    $after_discount=($productPrice-($productPrice*$product->discount)/100);
                                                @endphp
                                                @if(isset($product->discount) && $product->discount > 0)
                                                    <small><del class="text-muted">
                                                        ₹{{number_format($sizeData['price'][0],2)}}
                                                    </del></small>
                                                    <span>₹{{number_format($after_discount,2)}}</span>
                                                @else
                                                    @if(isset($sizeData['price'][0]) && is_numeric($sizeData['price'][0]))
                                                        <span>₹{{ number_format($sizeData['price'][0], 2) }}</span>
                                                    @endif
                                                @endif
                                                <!-- <del style="padding-left:4%;">₹{{number_format($product->price,2)}}</del> -->
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                           


                        </div>
                        <div class="row pagination_wrapper my-3">
                            <div class="col-md-12">
                            </div>
                          </div>

                    </div>
                </div>
            </div>
        </section>
    </form>

    <!--/ End Product Style 1  -->



    <!-- Modal -->
    @if($products)
        @foreach($products as $key=>$product)
            <div class="modal fade" id="{{$product->id}}" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span class="ti-close" aria-hidden="true"></span></button>
                            </div>
                            <div class="modal-body">
                                <div class="row no-gutters">
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <!-- Product Slider -->
                                            <div class="product-gallery">
                                                <div class="quickview-slider-active">
                                                    @php
                                                        $photo=explode(',',$product->photo);
                                                    // dd($photo);
                                                    @endphp
                                                    @foreach($photo as $data)
                                                        <div class="single-slider">
                                                            <img src="{{asset('public/'.$data)}}" alt="{{asset('public/'.$data)}}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        <!-- End Product slider -->
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <div class="quickview-content">
                                            <h2>{{$product->title}}</h2>
                                            <div class="quickview-ratting-review">
                                                <div class="quickview-ratting-wrap">
                                                    <div class="quickview-ratting">
                                                        {{-- <i class="yellow fa fa-star"></i>
                                                        <i class="yellow fa fa-star"></i>
                                                        <i class="yellow fa fa-star"></i>
                                                        <i class="yellow fa fa-star"></i>
                                                        <i class="fa fa-star"></i> --}}
                                                        @php
                                                            $rate=DB::table('product_reviews')->where('product_id',$product->id)->avg('rate');
                                                            $rate_count=DB::table('product_reviews')->where('product_id',$product->id)->count();
                                                        @endphp
                                                        @for($i=1; $i<=5; $i++)
                                                            @if($rate>=$i)
                                                                <i class="yellow fa fa-star"></i>
                                                            @else
                                                            <i class="fa fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <a href="#"> ({{$rate_count}} customer review)</a>
                                                </div>
                                                <div class="quickview-stock">
                                                    @if($product->stock >0)
                                                    <span><i class="fa fa-check-circle-o"></i> {{$product->stock}} in stock</span>
                                                    @else
                                                    <span><i class="fa fa-times-circle-o text-danger"></i> {{$product->stock}} out stock</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @php
                                                $after_discount=($product->price-($product->price*$product->discount)/100);
                                            @endphp
                                            <h3><small><del class="text-muted">${{number_format($product->price,2)}}</del></small>    ${{number_format($after_discount,2)}}  </h3>
                                            <div class="quickview-peragraph">
                                                <p>{!! html_entity_decode($product->summary) !!}</p>
                                            </div>
                                            @if($product->size)
                                                <div class="size">
                                                    <!--<h4>Size</h4>-->
                                                    <ul>
                                                        @php
                                                            //$sizes=explode(',',$product->size);
                                                            // dd($sizes);
                                                            $sizes = json_decode($product->size);
                                                        @endphp
                                                        @foreach($sizes->size as $size)
                                                        <!--<li><a href="#" class="one">{{$size}}</a></li>-->
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            <div class="size">
                                                <div class="row">
                                                    <div class="col-lg-6 col-12">
                                                        <h5 class="title">Size</h5>
                                                        <select>
                                                            @php
                                                            $sizes=explode(',',$product->size);
                                                            // dd($sizes);
                                                            @endphp
                                                            @foreach($sizes as $size)
                                                                <option>{{$size}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    {{-- <div class="col-lg-6 col-12">
                                                        <h5 class="title">Color</h5>
                                                        <select>
                                                            <option selected="selected">orange</option>
                                                            <option>purple</option>
                                                            <option>black</option>
                                                            <option>pink</option>
                                                        </select>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <form action="{{route('single-add-to-cart')}}" method="POST">
                                                @csrf
                                                <div class="quantity">
                                                    <!-- Input Order -->
                                                    <div class="input-group">
                                                        <div class="button minus">
                                                            <button type="button" class="btn btn-primary btn-number" disabled="disabled" data-type="minus" data-field="quant[1]">
                                                                <i class="ti-minus"></i>
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="slug" value="{{$product->slug}}">
                                                        <input type="text" name="quant[1]" class="input-number"  data-min="1" data-max="1000" value="1">
                                                        <div class="button plus">
                                                            <button type="button" class="btn btn-primary btn-number" data-type="plus" data-field="quant[1]">
                                                                <i class="ti-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <!--/ End Input Order -->
                                                </div>
                                                <div class="add-to-cart">
                                                    <button type="submit" class="btn">Add to cart</button>
                                                    <a href="{{route('add-to-wishlist',$product->slug)}}" class="btn min"><i class="ti-heart"></i></a>
                                                </div>
                                            </form>
                                            <div class="default-social">
                                            <!-- ShareThis BEGIN --><div class="sharethis-inline-share-buttons"></div><!-- ShareThis END -->
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
    .pagination{
        display:inline-flex;
    }
    .filter_button{
        /* height:20px; */
        text-align: center;
        background:#F7941D;
        padding:8px 16px;
        margin-top:10px;
        color: white;
    }
</style>
@endpush
@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script> --}}
    <script src="{{ asset('public/frontend/js/sweetalert.min.js') }}"></script>
    <script>
$(document).on('click', '.quick-add-to-cart-btn', function () {
    let slug = $(this).data('slug');

    $.ajax({
        url: "{{ route('single-add-to-cart') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            slug: slug,
            quant: { 1: 1 },
            selected_size: '',
            selected_price: '',
            selected_color: ''
        },
        success: function (response) {
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
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Something went wrong!',
            });
        }
    });
});
</script>
    {{-- <script>
        $('.cart').click(function(){
            var quantity=1;
            var pro_id=$(this).data('id');
            $.ajax({
                url:"{{route('add-to-cart')}}",
                type:"POST",
                data:{
                    _token:"{{csrf_token()}}",
                    quantity:quantity,
                    pro_id:pro_id
                },
                success:function(response){
                    console.log(response);
					if(typeof(response)!='object'){
						response=$.parseJSON(response);
					}
					if(response.status){
						swal('success',response.msg,'success').then(function(){
							document.location.href=document.location.href;
						});
					}
                    else{
                        swal('error',response.msg,'error').then(function(){
							// document.location.href=document.location.href;
						});
                    }
                }
            })
        });
    </script> --}}
    <script>
        $(document).ready(function(){
        /*----------------------------------------------------*/
        /*  Jquery Ui slider js
        /*----------------------------------------------------*/
        if ($("#slider-range").length > 0) {
            const max_value = parseInt( $("#slider-range").data('max') ) || 500;
            const min_value = parseInt($("#slider-range").data('min')) || 0;
            const currency = $("#slider-range").data('currency') || '';
            let price_range = min_value+'-'+max_value;
            if($("#price_range").length > 0 && $("#price_range").val()){
                price_range = $("#price_range").val().trim();
            }

            let price = price_range.split('-');
            $("#slider-range").slider({
                range: true,
                min: min_value,
                max: max_value,
                values: price,
                slide: function (event, ui) {
                    $("#amount").val(currency + ui.values[0] + " -  "+currency+ ui.values[1]);
                    $("#price_range").val(ui.values[0] + "-" + ui.values[1]);
                }
            });
            }
        if ($("#amount").length > 0) {
            const m_currency = $("#slider-range").data('currency') || '';
            $("#amount").val(m_currency + $("#slider-range").slider("values", 0) +
                "  -  "+m_currency + $("#slider-range").slider("values", 1));
            }
        })

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
    </script>
@endpush

@push('styles')
<style>
	/* Off-Canvas Filter Drawer CSS */
	.filter-offcanvas {
		position: fixed;
		top: 0;
		left: -350px;
		width: 320px;
		height: 100vh;
		background: #ffffff;
		z-index: 999999;
		box-shadow: 4px 0 25px rgba(0,0,0,0.15);
		transition: left 0.3s ease-in-out;
		overflow-y: auto;
		padding: 20px;
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
		background: rgba(0, 0, 0, 0.4);
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
		padding-bottom: 12px;
		border-bottom: 1px solid #e2e8f0;
		margin-bottom: 15px;
	}
	.offcanvas-header h5 {
		font-size: 16px;
		font-weight: 700;
		color: #1e293b;
		margin: 0;
	}
	.close-offcanvas {
		background: none;
		border: none;
		font-size: 24px;
		cursor: pointer;
		color: #64748b;
		line-height: 1;
	}
	.btn-filter-toggle {
		display: inline-flex;
		align-items: center;
		gap: 6px;
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
	.btn-filter-toggle:hover {
		background: #4ca336;
	}
</style>
@endpush
