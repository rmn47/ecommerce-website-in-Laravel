<div class="bg-white mb-4 border p-3 p-sm-4">
    <!-- Tabs -->
    <div class="nav aiz-nav-tabs">
        <a href="#tab_default_1" data-toggle="tab" class="mr-5 pb-2 fs-16 fw-700 text-reset">{{ translate('Sample')
            }}</a>
    </div>

    <!-- Description -->
    <div class="tab-content pt-0">
        <!-- Description -->
        <div class="py-2">
            <div class="mw-100 overflow-hidden text-left aiz-editor-data d-flex">
                <div class="mr-4">
                    @if($product->is_sample_order)
                    <p class="text-primary"> {{translate('Sample order is available for this product')}}</p>
                    @endif
                    <p>Maximum order quantity : 1 {{$product->unit}}</p>
                    <p>Sample price : <span><b>{{$product->preorder_sample_order?->sample_price}} /piece</b></span></p>
                    <button type="button"
                        class="btn btn-outline-secondary mr-2 add-to-cart fw-600 min-w-150px rounded-4">
                        {{ translate('Request Sample') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>