<div class="dropdown-cart-header">
    <span>{{ count(Helper::getAllProductFromWishlist()) }} Items</span>
    <a href="{{ route('wishlist') }}">View Wishlist</a>
</div>
<ul class="shopping-list">
    @forelse(Helper::getAllProductFromWishlist() as $data)
        @php $photo = explode(',', $data->product['photo']); @endphp
        <li>
            <a href="{{ route('wishlist-delete', $data->id) }}" class="remove" title="Remove this item"><i class="fa fa-remove"></i></a>
            <a class="cart-img" href="#">
                @if(isset($data->color_img) && $data->color_img != null)
                    <img src="{{ $data->color_img }}" alt="{{ $data->color_img }}">
                @else
                    <img src="{{ asset('public/' . $photo[0]) }}" alt="{{ asset('public/' . $photo[0]) }}">
                @endif
            </a>
            <h4><a href="{{ route('product-detail', $data->product['slug']) }}" target="_blank">{{ $data->product['product_code'] }}</a></h4>
        </li>
    @empty
        <li style="border:none; text-align:center; padding:10px 0;">Your wishlist is empty.</li>
    @endforelse
</ul>