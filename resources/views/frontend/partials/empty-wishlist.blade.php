{{-- Shown on the wishlist page when nothing is saved. Styles: partials/empty-state-styles --}}
<div class="empty-state">
    {{-- Illustration (inline SVG, no image file needed) --}}
    <svg class="empty-state-image" viewBox="0 0 200 200" width="190" height="190" role="img" aria-label="Empty wishlist">
        <circle cx="100" cy="100" r="88" fill="#eef8ea" />
        <circle cx="36" cy="64" r="6" fill="#cfe9c8" />
        <circle cx="168" cy="50" r="9" fill="#cfe9c8" />
        <circle cx="164" cy="150" r="5" fill="#cfe9c8" />
        <circle cx="48" cy="148" r="4" fill="#cfe9c8" />
        <path d="M100 150 C58 118 48 92 58 74 C68 58 92 60 100 80 C108 60 132 58 142 74 C152 92 142 118 100 150 Z"
            fill="#ffffff" stroke="#3f9a2c" stroke-width="8" stroke-linejoin="round" />
        <path d="M80 96 Q100 112 120 96" fill="none" stroke="#5db845" stroke-width="3.5" stroke-linecap="round" />
        <circle cx="86" cy="86" r="3.5" fill="#3f9a2c" />
        <circle cx="114" cy="86" r="3.5" fill="#3f9a2c" />
        <path d="M150 104 l3 7 7 3 -7 3 -3 7 -3 -7 -7 -3 7 -3 z" fill="#5db845" />
    </svg>

    <h3 class="empty-state-title">Your wishlist is empty</h3>
    <p class="empty-state-text">Tap the heart on any product to save it here, so you can find it again later.</p>

    <div class="empty-state-actions">
        <a href="{{ route('product-lists') }}" class="empty-state-btn"><i class="fa fa-shopping-bag" aria-hidden="true"></i>Start Shopping</a>
        <a href="{{ route('cart') }}" class="empty-state-btn empty-state-btn-outline"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Go to Cart</a>
    </div>

    <ul class="empty-state-perks">
        <li><i class="fa fa-lock" aria-hidden="true"></i>Secure payment</li>
        <li><i class="fa fa-truck" aria-hidden="true"></i>Fast delivery</li>
        <li><i class="fa fa-refresh" aria-hidden="true"></i>Easy returns</li>
    </ul>
</div>
