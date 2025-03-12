<style>
    .substitute-product-link {
        color: #ff6200 !important;
    }
    .substitute-product-link:hover {
        color: #ffc78f !important;
    }
</style>

<div class="bg-white border my-4">
    <div class="p-3 p-sm-4">
        <h3 class="fs-16 fw-700 mb-0">
            <span class="mr-4">{{ translate('Substitute Products') }}</span>
        </h3>
    </div>
    <div class="px-4">
        @if ($substituteProducts->isNotEmpty())
            @foreach ($substituteProducts as $key => $related_product)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <h4 class="mb-0 fs-14 fw-400">
                            <a href="{{ route('product', $related_product->slug) }}"
                               class="d-block text-decoration-none substitute-product-link">{{ $related_product->getTranslation('name') }}</a>
                        </h4>
                        <small class="text-muted">
                            {{ $related_product->manufacturer }}
                        </small>
                    </div>
                    <div class="text-right">
                        <div class="fs-14 fw-700">
                            ₹{{ number_format($related_product->unit_price, 2) }}/{{ $related_product->unit ?: 'Unit' }}
                        </div>
                        <small class="{{ $related_product->price_comparison['is_cheaper'] ? 'text-success' : 'text-danger' }}">
                            {{ $related_product->price_comparison['is_cheaper'] ? $related_product->price_comparison['percentage'] . '% cheaper' : $related_product->price_comparison['percentage'] . '% costlier' }}
                        </small>
                    </div>
                </div>
            @endforeach
            <!--<div class="text-center my-3">-->
            <!--    <a href="{{ route('substitute.products', $detailedProduct->slug) }}" class="btn btn-danger rounded-pill px-4">-->
            <!--        {{ translate('View All Substitutes') }} <i class="las la-arrow-right"></i>-->
            <!--    </a>-->
            <!--</div>-->
        @else
            <div class="p-3 text-center">
                <p>{{ translate('No substitute products found with matching salt composition') }}</p>
            </div>
        @endif
    </div>
</div>