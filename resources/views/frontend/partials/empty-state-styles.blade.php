{{-- Styles for the "empty" blocks on the cart and wishlist pages (partials/empty-cart and partials/empty-wishlist). --}}
<style>
    /* no column headings above an empty block */
    .shopping-summery:has(.empty-cart-row, .empty-wishlist-row) thead {
        display: none;
    }

    .shopping-summery .empty-cart-row td,
    .shopping-summery .empty-wishlist-row td {
        padding: 0 15px;
        border: 0;
        background: transparent;
    }

    /* less empty space around the block (the table margin and the section padding) */
    .shopping-summery:has(.empty-cart-row, .empty-wishlist-row) {
        margin-bottom: 0;
        background: transparent;
    }

    .shopping-cart.section:has(.empty-cart-row, .empty-wishlist-row) {
        padding: 20px 0;
    }

    /* the "secure payment / best price" strip under an empty cart sits closer */
    .shopping-cart.section:has(.empty-cart-row) ~ .shop-services {
        padding-top: 30px;
    }

    @media (min-width: 768px) {
        .breadcrumbs {
            padding: 14px 0;
        }
    }

    /* the card */
    .empty-state {
        position: relative;
        max-width: 620px;
        margin: 10px auto;
        padding: 34px 28px 30px;
        border: 1px solid #e3f1de;
        border-radius: 24px;
        background: linear-gradient(180deg, #f4fbf1 0%, #ffffff 60%);
        box-shadow: 0 12px 34px rgba(63, 154, 44, 0.12);
        text-align: center;
        overflow: hidden;
    }

    /* soft decorative circles in the corners */
    .empty-state::before,
    .empty-state::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        background: #5db845;
        opacity: 0.08;
        pointer-events: none;
    }

    .empty-state::before {
        top: -60px;
        left: -60px;
        width: 170px;
        height: 170px;
    }

    .empty-state::after {
        right: -50px;
        bottom: -50px;
        width: 140px;
        height: 140px;
    }

    .empty-state > * {
        position: relative;
        z-index: 1;
    }

    .empty-state-image {
        display: block;
        width: 160px;
        height: 160px;
        margin: 0 auto 8px;
        max-width: 100%;
        filter: drop-shadow(0 10px 14px rgba(63, 154, 44, 0.22));
        animation: empty-state-float 3.5s ease-in-out infinite;
    }

    @keyframes empty-state-float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    .empty-state-title {
        margin: 8px 0 8px;
        font-size: 26px;
        font-weight: 700;
        color: #1f2d1b;
    }

    .empty-state-text {
        max-width: 440px;
        margin: 0 auto 20px;
        font-size: 15px;
        line-height: 1.65;
        color: #6b7280;
    }

    .empty-state-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
    }

    .empty-state-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 30px;
        border: 2px solid #5db845;
        border-radius: 30px;
        background: linear-gradient(135deg, #5db845, #3f9a2c);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 8px 18px rgba(63, 154, 44, 0.28);
        transition: box-shadow 0.25s ease, color 0.25s ease, transform 0.25s ease, background 0.25s ease;
    }

    .empty-state-btn:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(63, 154, 44, 0.36);
    }

    .empty-state-btn-outline {
        background: #fff;
        color: #3f9a2c;
        box-shadow: none;
    }

    .empty-state-btn-outline:hover {
        background: #5db845;
        border-color: #5db845;
        color: #fff;
        box-shadow: 0 8px 18px rgba(63, 154, 44, 0.25);
    }

    /* small reassurance chips under the buttons */
    .empty-state-perks {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px 22px;
        margin: 24px 0 0;
        padding: 16px 0 0;
        border-top: 1px dashed #cfe5c8;
        list-style: none;
        font-size: 13px;
        color: #4b5563;
    }

    .empty-state-perks li {
        margin: 0;
    }

    .empty-state-perks i {
        margin-right: 6px;
        color: #3f9a2c;
    }

    @media (prefers-reduced-motion: reduce) {
        .empty-state-image {
            animation: none;
        }
    }

    @media (max-width: 767px) {
        /* the wishlist table turns each row into a small grid on phones: show the empty block full width instead */
        .shopping-summery tbody tr.empty-wishlist-row {
            display: block;
        }

        .shopping-summery tbody tr.empty-wishlist-row td {
            display: block;
            width: 100%;
        }

        .empty-state {
            padding: 26px 16px 22px;
            border-radius: 20px;
        }

        .empty-state-image {
            width: 130px;
            height: 130px;
        }

        .empty-state-title {
            font-size: 22px;
        }

        .empty-state-btn {
            justify-content: center;
            width: 100%;
            max-width: 260px;
        }

        .empty-state-perks {
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
    }
</style>
