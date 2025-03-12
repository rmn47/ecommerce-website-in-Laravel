<!--frontend.substitute_products.blade.php-->

@extends('frontend.layouts.app')

@section('content')
    <section class="mb-4 pt-3">
        <div class="container">
            <div class="bg-white py-3">
                <h2 class="fs-18 fw-700 mb-4">{{ translate('All Substitute Products for') }} {{ $detailedProduct->getTranslation('name') }}</h2>
                <p class="text-muted mb-4">{{ translate('For informational purposes only. Consult a doctor before taking any medicines.') }}</p>
                <div class="px-4">
                    @foreach ($substituteProducts as $key => $related_product)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <h4 class="mb-0 fs-14 fw-400">
                                    {{ $related_product->getTranslation('name') }}
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
                </div>
            </div>
        </div>
    </section>
@endsection