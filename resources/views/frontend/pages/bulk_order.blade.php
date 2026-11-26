@extends('frontend.layouts.master')
@section('title', 'Bulk Order')

@section('main-content')
<section class="bulk-order section">
    <div class="container">
        <div class="bulk-order-card">
            <h2>Share Your Bulk Order Details</h2>

            @if (session('error'))
                <div class="bulk-alert">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="bulk-alert">Please fix the highlighted fields and try again.</div>
            @endif

            <form action="{{ route('bulk-order.store') }}" method="POST" class="bulk-form">
                @csrf

                <div class="bulk-grid">
                    <div class="bulk-field">
                        <label>Full name <span>*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                               placeholder="Enter your full name" required>
                        @error('full_name') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field">
                        <label>Mobile number <span>*</span></label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}"
                               placeholder="10-digit mobile" inputmode="numeric" maxlength="10" required>
                        @error('mobile') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field bulk-full">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="Enter your email address" required>
                        @error('email') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field">
                        <label>Address <span>*</span></label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="Enter your address" required>
                        @error('address') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field">
                        <label>Shop name <span>*</span></label>
                        <input type="text" name="shop_name" value="{{ old('shop_name') }}"
                               placeholder="Enter shop name" required>
                        @error('shop_name') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field">
                        <label>Category <span>*</span></label>
                        <select name="category_id" required>
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field">
                        <label>Required quantity <span>*</span></label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}"
                               placeholder="e.g., 240" min="1" required>
                        @error('quantity') <small>{{ $message }}</small> @enderror
                    </div>

                    <div class="bulk-field bulk-full">
                        <label>Message <span>*</span></label>
                        <textarea name="message" rows="4" placeholder="Message" required>{{ old('message') }}</textarea>
                        @error('message') <small>{{ $message }}</small> @enderror
                    </div>
                </div>

                <button type="submit" class="bulk-submit">Submit Request</button>
            </form>
        </div>
    </div>
</section>

<style>
    .bulk-order-card {
        max-width: 760px;
        margin: 30px auto;
        padding: 28px 32px;
        border: 1px solid #d6d9e0;
        border-radius: 18px;
    }
    .bulk-order-card h2 { font-size: 24px; margin-bottom: 22px; }
    .bulk-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 18px;
    }
    .bulk-full { grid-column: 1 / -1; }
    .bulk-field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; }
    .bulk-field label span { color: #dc2626; }
    .bulk-field input,
    .bulk-field select,
    .bulk-field textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd0d8;
        border-radius: 8px;
        background: #f8f9fb;
        font-size: 14px;
    }
    .bulk-field small { color: #dc2626; font-size: 12px; }
    .bulk-alert {
        padding: 10px 14px;
        margin-bottom: 16px;
        border-radius: 8px;
        background: #fde8e8;
        color: #b91c1c;
        font-size: 14px;
    }
    .bulk-submit {
        margin-top: 22px;
        padding: 12px 26px;
        border: 0;
        border-radius: 999px;
        background: #222;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .bulk-submit:hover { background: #5db845; }
    @media (max-width: 576px) {
        .bulk-grid { grid-template-columns: 1fr; }
        .bulk-order-card { padding: 20px; }
    }
</style>
@endsection