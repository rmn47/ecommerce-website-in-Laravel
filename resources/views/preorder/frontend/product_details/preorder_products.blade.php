<div class=" border mb-4 rounded-2" style="background: #F5F7FC">
    <div class="p-3 p-sm-4">
        <h3 class="fs-16 fw-700 mb-0">
            <span class="mr-4 text-uppercase">{{ translate('More Products to Pre-order...') }}</span>
        </h3>
    </div>
    <div class="px-4">
        <div class="aiz-carousel gutters-5 half-outside-arrow" data-items="5" data-xl-items="3" data-lg-items="4"
            data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
            @foreach ($more_products as $key => $more_product)
                @if($more_product->is_published)
                    @include('preorder.frontend.product_box2',['product' => $more_product])
                @endif
            @endforeach
        </div>
    </div>
</div>