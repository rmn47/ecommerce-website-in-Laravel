<div class="carousel-box h-100 text-center has-transition mb-4">
    <div class="px-3 px-xl-2 py-2 ">
        <!-- Shop logo & Verification Status -->
        <div class="mx-auto position-relative">
            <a href="{{ route('preorder-product.details', $product->product_slug) }}"
                class="d-block h-100"
                tabindex="0"
                >
                <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail) }}" alt="{{ $product->product_name }}"
                    class="img-fit lazyload h-100 has-transition"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
            </a>

            <!-- Top-left label -->
            <div class="position-absolute top-0 left-0  text-white px-2 py-1"
                style="background-color: #FF6002; color: white;">
            <small><i class="las la-clock fs-16"></i></small>
            </div>

        </div>
        <!-- Shop name -->
        <h2 class="fw-400 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px text-center mt-2">
            <a href="{{ route('preorder-product.details', $product->product_slug) }}"
                class="text-reset hov-text-primary" tabindex="0">{{ $product->product_name }}</a>
        </h2>
        <!-- Shop Rating -->
        <div class="rating rating-mr-2 text-dark mt-2">
            {{ renderStarRating($product->rating) }} <br>

        </div>

    </div>
</div>