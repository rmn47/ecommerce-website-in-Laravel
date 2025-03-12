<div class=" mt-3 rounded-2 p-2 preorder-border-dashed-blue">

<div class="ml-3 my-2 d-flex justify-content-between">
    <div>
        <p class="fs-20 fw-700 m-0 p-0"><b>{{format_price(preorder_discount_price($product))}}</b></p>
    <p class="opacity-60 m-0 p-0">{{translate('Regular price') .' '. format_price($product->unit_price)}}</p>
    {{-- <p class="">{{translate('Minimum order quantity ') .' '. $product->min_qty}}</p> --}}
    </div>

    @if($product->is_prepayment)
    <div class="pr-3">
        <p class="text-capitalize m-0 p-0"><b>{{translate('Prepayment')}}</b></p>
        @if($product->is_prepayment_nedded)
        <p class="opacity-60 m-0 p-0">{{translate('PrepaPrepayment needed for Cash on Deliveryyment')}}</p>
        @endif
        <p class="m-0 p-0 fs-20 fw-700">{{format_price($product->preorder_prepayment?->prepayment_amount)}}</p>
    </div>
    @endif

</div>
</div>