{{-- Shown on the cart page when the cart has no items. Styles: partials/empty-state-styles --}}
<div class="empty-state">
    {{-- Illustration (inline SVG, no image file needed) --}}
    <svg class="empty-state-image" viewBox="0 0 200 200" width="190" height="190" role="img" aria-label="Empty shopping cart">
        <circle cx="100" cy="100" r="88" fill="#eef8ea" />
        <circle cx="36" cy="60" r="6" fill="#cfe9c8" />
        <circle cx="168" cy="46" r="9" fill="#cfe9c8" />
        <circle cx="162" cy="152" r="5" fill="#cfe9c8" />
        <circle cx="50" cy="150" r="4" fill="#cfe9c8" />
        <path d="M72 76 H162 L148 122 H86 Z" fill="#ffffff" />
        <path d="M44 56 H66 L86 122 H148 L162 76 H72" fill="none" stroke="#3f9a2c" stroke-width="8"
            stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="94" cy="144" r="9" fill="#5db845" />
        <circle cx="142" cy="144" r="9" fill="#5db845" />
        <circle cx="106" cy="94" r="3.5" fill="#3f9a2c" />
        <circle cx="132" cy="94" r="3.5" fill="#3f9a2c" />
        <path d="M108 104 Q119 114 130 104" fill="none" stroke="#3f9a2c" stroke-width="3.5" stroke-linecap="round" />
    </svg>

    <h3 class="empty-state-title">Your cart is empty</h3>
    <p class="empty-state-text">Looks like you have not added anything yet. Browse our collection and find something you will love.</p>

    <div class="empty-state-actions">
        <a href="{{ route('product-lists') }}" class="empty-state-btn"><i class="fa fa-shopping-bag" aria-hidden="true"></i>Continue Shopping</a>
        <a href="{{ route('wishlist') }}" class="empty-state-btn empty-state-btn-outline"><i class="fa fa-heart-o" aria-hidden="true"></i>View Wishlist</a>
    </div>

    <ul class="empty-state-perks">
        <li><i class="fa fa-lock" aria-hidden="true"></i>Secure payment</li>
        <li><i class="fa fa-truck" aria-hidden="true"></i>Fast delivery</li>
        <li><i class="fa fa-refresh" aria-hidden="true"></i>Easy returns</li>
    </ul>
</div>
