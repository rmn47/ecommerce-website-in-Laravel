<div class="  bg-white  bg-preorder-review-section rounded-2 mb-4 p-2 mt-2">
    <!-- Ratting -->
    <div class="row align-items-center p-0 m-0">
        <div class="col-md-12 mb-3">
            <div class="ml-1">
                <div class="w-100">
                    <span class="fs-28 mr-3">{{ $product->rating ?? 4.8}}</span>
                    <span class="fs-14 mr-3">{{ translate('out of 5.0') }}</span>
                </div>

                <div class="w-100">
                    @php
                    $total = 0;
                    $total += $product->preorderProductreviews?->count();
                    @endphp
                    <span class="rating rating-mr-2">
                        {{ renderStarRating($product->rating) }}
                    </span>
                    <a href="#pre_review_ratings"><span class="ml-1 fs-14 preorder-text-secondary">({{ $total }}
                        {{ translate('Reviews & Ratings') }})</span></a>
                </div>
            </div>
        </div>
    </div>
</div>