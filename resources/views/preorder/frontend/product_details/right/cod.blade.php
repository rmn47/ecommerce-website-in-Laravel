<div class="mt-2 p-3 p-sm-4 ">
    @if($product->is_cod)
    <div class="fw-16 mt-2">
        <p class="text-capitalize fs-16"><b>{{translate('Cash on Delivery')}}</b></p>
    </div>
    <div class="free-shipping bg-soft-warning p-2 rounded ">
        <p class="m-0 p-0 text-yellow"><i class="las la-check-circle fs-16"></i> {{translate('Cash on Delivery
            Available')}}</p>
    </div>
    <div class="mt-1">

        @if($product->is_prepayment)
        <p><b>{{translate('Prepayment needed for cash on delivery')}}</b></p>
        <p><b>{{translate('Pay only '. $product->preorder_prepayment?->prepayment_amount .' to avail Cash on Delivery')}}</b></p>
        @endif
        <p>{{ $product->preorder_cod?->note?->description }}</p>
    </div>
    @endif
</div>