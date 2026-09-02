<header class="header shop">
    <!-- Topbar -->
    <div class="topbar">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Left -->
                    <div class="top-left">
                        <ul class="list-main">
                            @php
                                $settings=DB::table('settings')->get();
                                
                            @endphp
                            <li><i class="ti-headphone-alt"></i>
                                @foreach($settings as $data) 
                                    <a href="tel:{{ preg_replace('/\s+/', '', $data->phone) }}">
                                        {{ $data->phone }}
                                    </a>
                                @endforeach
                            </li>
                            <li><i class="ti-email"></i> 
                                @foreach($settings as $data) 
                                    <a href="mailto:{{ $data->email }}">
                                        {{ $data->email }}
                                    </a>
                                @endforeach
                            </li>
                        </ul>
                    </div>
                    <!--/ End Top Left -->
                </div>
                <div class="col-lg-6 col-md-12 col-12">
                    <!-- Top Right -->
                    <div class="right-content">
                        <ul class="list-main">
                        @auth
                        <li><i class="ti-location-pin"></i> 
                            @if(Auth::user()->role != 'admin')
                                <a href="{{route('order.track')}}">Track Order</a>
                            @else
                                <a href="{{route('login.form')}}">Track Order</a>
                            @endif
                        </li>
                        @endauth
                            {{-- <li><i class="ti-alarm-clock"></i> <a href="#">Daily deal</a></li> --}}
                            @auth 
                                @if(Auth::user()->role=='admin')
                                    {{-- <li><i class="ti-user"></i> <a href="{{route('admin')}}"  target="_blank">Dashboard</a></li> --}}
                                    <li><i class="ti-power-off"></i><a href="{{route('login.form')}}">Login /</a> <a href="{{route('register.form')}}">Register</a></li>
                                @else 
                                    <li><i class="ti-user"></i> <a href="{{route('user-profile')}}">My Profile</a></li>
                                    <li><i class="ti-shopping-cart"></i> <a href="{{route('myorders')}}">My Orders</a></li>
                                    <li><i class="ti-power-off"></i> <a href="{{route('user.logout')}}">Logout</a></li>
                                @endif
                            @else
                                <li><i class="ti-power-off"></i><a href="{{route('login.form')}}">Login /</a> <a href="{{route('register.form')}}">Register</a></li>
                            @endauth
                        </ul>
                    </div>
                    <!-- End Top Right -->
                </div>
            </div>
        </div>
    </div>
    <!-- End Topbar -->
    <div class="middle-inner">
        <div class="container">
            <div class="row">
                <div class="col-lg-2 col-md-2 col-12">
                    <!-- Logo -->
                    <div class="logo">
                        @php
                            $settings=DB::table('settings')->get();
                        @endphp                    
                        <a href="{{route('home')}}"><img src="@foreach($settings as $data) {{asset('public'.$data->logo)}} @endforeach" alt="logo" class="header-logo-img" style="max-width: 240px; width: 100%; height: auto;"></a>
                    </div>
                    <!--/ End Logo -->
                    <!-- Search Form -->
                    <div class="search-top">
                        <div class="top-search"><a href="#0"><i class="ti-search"></i></a></div>
                        <!-- Search Form -->
                        <div class="search-top">
                            <form class="search-form">
                                <input type="text" placeholder="Search here..." name="search">
                                <button value="search" type="submit"><i class="ti-search"></i></button>
                            </form>
                        </div>
                        <!--/ End Search Form -->
                    </div>
                    <!--/ End Search Form -->
                    <div class="mobile-nav"></div>
                </div>
                <div class="col-lg-8 col-md-7 col-12">
                    <div class="search-bar-top">
                        <div class="search-bar">
                            <select>
                                <option >All Category</option>
                                @foreach(Helper::getAllCategory() as $cat)
                                    <option>{{$cat->title}}</option>
                                @endforeach
                            </select>
                            <form method="Get" action="{{route('product.search')}}">
                                <!--@csrf-->
                                <input id="header-search-input" name="search" placeholder="Search Products Here....." type="search" autocomplete="off">
                                <button class="btnn" type="submit"><i class="ti-search"></i></button>
                            </form>
                            <div id="header-search-suggestions" style="position: absolute; top: calc(100% + 4px); left: 0; width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; max-height: 380px; overflow-y: auto; z-index: 99999; display: none; box-shadow: 0 15px 30px rgba(0,0,0,0.12); padding: 6px;"></div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Pure frontend mock products data with categories
                        const productsData = [
                            { title: 'Luggage & Travel Bag - Executive Series', category: 'Luggage & Travel Bags', code: 'LUG-101', photo: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Premium Rainwear Jacket', category: 'Rainwear Products', code: 'RAIN-202', photo: 'https://images.unsplash.com/photo-1544441893-675973e31985?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Winter Wear Windcheater Hoodie', category: 'Winter Wear', code: 'WIN-303', photo: 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=150&auto=format&fit=crop&q=80' },
                            { title: 'PVC Packaging Bag Set', category: 'PVC Packaging', code: 'PVC-404', photo: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Waterproof Travel Backpack', category: 'Luggage & Travel Bags', code: 'LUG-102', photo: 'https://images.unsplash.com/photo-1622560480605-d83c853bc5c3?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Classic PVC Lay-Flat Tubing', category: 'PVC Lay-Flat Tubing', code: 'PVC-405', photo: 'https://images.unsplash.com/photo-1607522370275-f14206abe5d3?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Heavy-Duty Raincoat Suit', category: 'Rainwear Products', code: 'RAIN-203', photo: 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=150&auto=format&fit=crop&q=80' },
                            { title: 'Home Furnishing Bag Container', category: 'PVC Packaging', code: 'PVC-406', photo: 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=150&auto=format&fit=crop&q=80' }
                        ];

                        const searchInput = document.getElementById('header-search-input');
                        const suggestionsBox = document.getElementById('header-search-suggestions');

                        if (!searchInput || !suggestionsBox) return;

                        // Customize suggestions box style with line-height reset
                        suggestionsBox.style.cssText = `
                            position: absolute;
                            top: calc(100% + 4px);
                            left: 0;
                            width: 100%;
                            background: #ffffff;
                            border: 1px solid #e2e8f0;
                            border-radius: 10px;
                            max-height: 350px;
                            overflow-y: auto;
                            z-index: 99999;
                            display: none;
                            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
                            padding: 6px;
                            line-height: 1.3 !important;
                            text-align: left !important;
                        `;

                        searchInput.addEventListener('input', function() {
                            const query = this.value.trim().toLowerCase();
                            if (query.length === 0) {
                                suggestionsBox.style.display = 'none';
                                suggestionsBox.innerHTML = '';
                                return;
                            }

                            const matches = productsData.filter(item => 
                                item.title.toLowerCase().includes(query) ||
                                item.code.toLowerCase().includes(query) ||
                                item.category.toLowerCase().includes(query)
                            ).slice(0, 6);

                            if (matches.length === 0) {
                                suggestionsBox.innerHTML = `
                                    <div style="padding: 16px; text-align: center; color: #94a3b8; font-size: 13px; font-weight: 500; line-height: 1.4 !important;">
                                        <i class="ti-search" style="font-size: 20px; display: block; margin-bottom: 4px; color: #cbd5e1;"></i>
                                        No products found for "${query}"
                                    </div>`;
                                suggestionsBox.style.display = 'block';
                                return;
                            }

                            let html = '';
                            matches.forEach(item => {
                                html += `
                                    <div class="search-suggestion-item" style="display: flex; align-items: center; justify-content: space-between; padding: 8px 10px; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; margin-bottom: 3px; line-height: 1.3 !important;">
                                        <div style="display: flex; align-items: center; gap: 10px; overflow: hidden; flex: 1;">
                                            <img src="${item.photo}" alt="${item.title}" style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; flex-shrink: 0; margin: 0;">
                                            <div style="overflow: hidden; text-align: left; line-height: 1.3 !important;">
                                                <div style="font-size: 13px; font-weight: 600; color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.3 !important; margin: 0 0 2px 0;">${item.title}</div>
                                                <div style="font-size: 11px; font-weight: 700; color: #5db845; text-transform: uppercase; line-height: 1.2 !important; margin: 0;">${item.code}</div>
                                            </div>
                                        </div>
                                        <div style="font-size: 11px; font-weight: 600; color: #475569; flex-shrink: 0; margin-left: 10px; background: #f1f5f9; padding: 4px 10px; border-radius: 20px; line-height: 1.2 !important; border: 1px solid #e2e8f0;">
                                            ${item.category}
                                        </div>
                                    </div>
                                `;
                            });

                            suggestionsBox.innerHTML = html;
                            suggestionsBox.style.display = 'block';

                            const items = suggestionsBox.querySelectorAll('.search-suggestion-item');
                            items.forEach(el => {
                                el.addEventListener('mouseenter', () => {
                                    el.style.background = '#f8fafc';
                                    el.style.transform = 'translateX(2px)';
                                });
                                el.addEventListener('mouseleave', () => {
                                    el.style.background = '#ffffff';
                                    el.style.transform = 'translateX(0)';
                                });
                                el.addEventListener('click', () => {
                                    const titleText = el.querySelector('div > div').textContent;
                                    searchInput.value = titleText;
                                    suggestionsBox.style.display = 'none';
                                });
                            });
                        });

                        document.addEventListener('click', function(e) {
                            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                                suggestionsBox.style.display = 'none';
                            }
                        });

                        searchInput.addEventListener('focus', function() {
                            if (this.value.trim().length > 0 && suggestionsBox.children.length > 0) {
                                suggestionsBox.style.display = 'block';
                            }
                        });
                    });
                    </script>
                </div>
                <div class="col-lg-2 col-md-3 col-12">
                    <div class="right-bar">
                        <!-- Search Form -->
                        <div class="sinlge-bar shopping">
                            @php 
                                $total_prod=0;
                                $total_amount=0;
                            @endphp
                           @if(session('wishlist'))
                                @foreach(session('wishlist') as $wishlist_items)
                                    @php
                                        $total_prod+=$wishlist_items['quantity'];
                                        $total_amount+=$wishlist_items['amount'];
                                    @endphp
                                @endforeach
                           @endif
                            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o"></i> <span class="total-count">{{Helper::wishlistCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromWishlist())}} Items</span>
                                        <a href="{{route('wishlist')}}">View Wishlist</a>
                                    </div>
                                    <ul class="shopping-list">
                                        {{-- {{Helper::getAllProductFromCart()}} --}}
                                            @foreach(Helper::getAllProductFromWishlist() as $data)
                                                    @php
                                                        $photo=explode(',',$data->product['photo']);
                                                    @endphp
                                                    <li>
                                                        <a href="{{route('wishlist-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                        <a class="cart-img" href="#">
                                                            @if(isset($data->color_img) && $data->color_img != null) 
                                                                <img src="{{$data->color_img}}" alt="{{ $data->color_img }}">
                                                            @else
                                                                <img src="{{asset('public/'.$photo[0])}}" alt="{{asset('public/'.$photo[0])}}">
                                                            @endif
                                                        </a>
                                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['product_code']}}</a></h4>
                                                        <!--<p class="quantity">{{$data->quantity}} x - -->
                                                        <!--    {{-- <span class="amount">₹{{number_format($data->price,2)}}</span> --}}-->
                                                        <!--    {{-- @if(isset($data->after_discount) && $data->after_discount > 0) --}}-->
                                                        <!--        <span>₹{{number_format($data->after_discount,2)}}</span>-->
                                                        <!--    {{-- @else-->
                                                        <!--        <span class="amount">-->
                                                        <!--            ₹{{number_format($data->price,2)}}-->
                                                        <!--        </span>-->
                                                        <!--    @endif --}}-->
                                                        <!--</p>-->
                                                    </li>
                                            @endforeach
                                    </ul>
                                    {{-- <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span class="total-amount">₹{{number_format(Helper::totalWishlistPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('cart')}}" class="btn animate">Cart</a>
                                    </div> --}}
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                        {{-- <div class="sinlge-bar">
                            <a href="{{route('wishlist')}}" class="single-icon"><i class="fa fa-heart-o" aria-hidden="true"></i></a>
                        </div> --}}
                        <div class="sinlge-bar shopping">
                            <a href="{{route('cart')}}" class="single-icon"><i class="ti-bag"></i> <span class="total-count">{{Helper::cartCount()}}</span></a>
                            <!-- Shopping Item -->
                            @auth
                                <div class="shopping-item">
                                    <div class="dropdown-cart-header">
                                        <span>{{count(Helper::getAllProductFromCart())}} Items</span>
                                        <a href="{{route('cart')}}">View Cart</a>
                                    </div>
                                    <ul class="shopping-list">
                                        {{-- {{Helper::getAllProductFromCart()}} --}}
                                            @foreach(Helper::getAllProductFromCart() as $data)
                                                    @php
                                                        $photo=explode(',',$data->product['photo']);
                                                    @endphp
                                                    <li>
                                                        <a href="{{route('cart-delete',$data->id)}}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
                                                        <a class="cart-img" href="#">
                                                            {{-- <img src="{{asset('public/'.$photo[0])}}" alt="{{asset('public/'.$photo[0])}}"> --}}
                                                            @if(isset($data->color_img) && $data->color_img != null) 
                                                                <img src="{{$data->color_img}}" alt="{{ $data->color_img }}">
                                                            @else
                                                                <img src="{{asset('public/'.$photo[0])}}" alt="{{asset('public/'.$photo[0])}}">
                                                            @endif
                                                        </a>
                                                        <h4><a href="{{route('product-detail',$data->product['slug'])}}" target="_blank">{{$data->product['product_code']}}</a></h4>
                                                        <!--<p class="quantity">{{$data->quantity}} x - -->
                                                        <!--    {{-- <span class="amount">₹{{number_format($data->price,2)}}</span> --}}-->
                                                        <!--    @if(isset($data->after_discount) && $data->after_discount > 0)-->
                                                        <!--        <small><del class="text-muted">-->
                                                        <!--            ₹{{number_format($data->price,2)}}-->
                                                        <!--        </del></small>-->
                                                        <!--        <span>₹{{number_format($data->after_discount,2)}}</span>-->
                                                        <!--    @else-->
                                                        <!--        <span class="amount">-->
                                                        <!--            ₹{{number_format($data->price,2)}}-->
                                                        <!--        </span>-->
                                                        <!--    @endif-->
                                                        <!--</p>-->
                                                    </li>
                                            @endforeach
                                    </ul>
                                    {{-- <div class="bottom">
                                        <div class="total">
                                            <span>Total</span>
                                            <span class="total-amount">₹{{number_format(Helper::totalCartPrice(),2)}}</span>
                                        </div>
                                        <a href="{{route('checkout')}}" class="btn animate">Checkout</a>
                                    </div> --}}
                                </div>
                            @endauth
                            <!--/ End Shopping Item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header Inner -->
    <div class="header-inner">
        <div class="container">
            <div class="cat-nav-head">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="menu-area">
                            <!-- Main Menu -->
                            <nav class="navbar navbar-expand-lg">
                                <div class="navbar-collapse">	
                                    <div class="nav-inner">	
                                    @php
                                        $menuCategory=App\Models\Category::where('status','active')->where('is_parent', 1)->get();
                                    @endphp
                                        <ul class="nav main-menu menu navbar-nav">
                                            <li class="{{Request::path()=='home' ? 'active' : ''}}"><a href="{{route('home')}}">Home</a></li>
                                            <li class="{{Request::path()=='about-us' ? 'active' : ''}}"><a href="{{route('about-us')}}">About Us</a></li>
                                        
                                            @if($menuCategory)
                                                @foreach($menuCategory as $cat)
                                                    <li class="{{Request::path()=='product-cat' ? 'active' : ''}}"><a href="{{route('product-cat',$cat->slug)}}">{{ $cat->title }}</a>
                                                        @if($cat->child_cat->count()>0)
                                                        <ul class="dropdown border-0 shadow">
                                                            
                                                                @foreach($cat->child_cat as $cat_sub_menu)
                                                                    <li><a href="{{route('product-sub-cat',[$cat->slug,$cat_sub_menu->slug]); }}">{{$cat_sub_menu->title; }}</a></li>
                                                                @endforeach
                                                            
                                                        </ul>
                                                        @endif
                                                    </li>									
                                                @endforeach
                                            @endif
                                            <li class="{{Request::path()=='blog' ? 'active' : ''}}"><a href="{{route('blog')}}">Blog</a></li>									
                                               
                                            <li class="{{Request::path()=='contact' ? 'active' : ''}}"><a href="{{route('contact')}}">Contact Us</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            <!--/ End Main Menu -->	
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ End Header Inner -->
</header>
