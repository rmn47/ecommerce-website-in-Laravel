<div class=" mt-2 p-3 p-sm-4 preorder-border-dashed-grey rounded-2">
    <div>
        <p class="text-uppercase fw-bold"><b>{{translate('Payment Summary')}}</b></p>
    </div>
    <div class="button-section mt-3 g-0">
        <div class="px-2">
            @if($order->preorder_product?->is_prepayment)
        <div class="row g-0 p-2">
            <div class="col-6 m-0 p-0">
                <div class=" btn-block btn-dark fs-14 fw-700 rounded-0  py-2 mr-2" >
                    <div class="ml-4">
                        <p class="text-white m-0 p-0 fs-12">{{ translate('Total Amount') }}</p>
                        <p class="text-white m-0 p-0 fs-20">{{ format_price($order->grand_total) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 m-0 p-0">
                <div class=" btn-block  fs-14 fw-700 rounded-0  py-2 mr-2 ml-2" style="background-color: #FF6002; color: white;">
                    <div class="ml-4">
                        <p class="text-white m-0 p-0 fs-12">{{ translate('Prepay Amount') }}</p>
                        <p class="text-white m-0 p-0 fs-20">{{ format_price($order->prepayment) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row g-0 p-2">
            <div class="col-6 m-0 p-0">
                <div class=" btn-block btn-dark fs-14 fw-700 rounded-0  py-2 mr-2" >
                    <div class="ml-4">
                        <p class="text-white m-0 p-0 fs-12">{{ translate('Total Amount') }}</p>
                        <p class="text-white m-0 p-0 fs-20">{{ format_price($order->grand_total) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-6 m-0 p-0">
                <div class=" btn-block  fs-14 fw-700 rounded-0  py-2 mr-2 ml-2" style="background-color: #8b8987; color: white;">
                    <div class="ml-4">
                        <p class="text-white m-0 p-0 fs-12">{{ translate('Prepay Amount') }}</p>
                        <p class="text-white m-0 p-0 fs-20">{{ format_price(0) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        </div>
        <p class="preorder-text-primary m-0 p-0"><span class="mt-2">*{{ translate('Amount to be paid while final order ')}}  {{format_price($order->preorder_product?->is_prepayment ? ($order->grand_total - $order->prepayment) : $order->grand_total) }}</span></p>
        {{-- <p class="preorder-text-primary m-0 p-0"><span class="mt-2">*{{ translate('Minimum order quantity')}} {{ $order->quantity }}</span></p> --}}
    </div>
    
    <!-- Coupon System -->    
    @if($order->final_order_status == 0 && $order->preorder_product?->is_coupon)
        <div class="mt-3">
            @php $is_coupon_applied = $order->is_coupon_applied;@endphp
            <form class="" id="apply-coupon-form" enctype="multipart/form-data" action="{{ !$is_coupon_applied ? route('preorder.apply_coupon_code') : route('preorder.remove_coupon_code')}}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="preorder_product_id" value="{{$order->preorder_product->id}}">
                <input type="hidden" name="order_id" value="{{$order->id}}">
                <div class="input-group">
                    <input type="text" class="form-control rounded-0" name="coupon_code"
                        onkeydown="return event.key != 'Enter';"
                        placeholder="{{ !$is_coupon_applied ? translate('Have coupon code? Apply here') : $order->preorder_product?->preorder_coupon?->coupon_code }}" 
                        @if($is_coupon_applied) disabled @else required @endif>
                    <div class="input-group-append">
                        <button type="submit" 
                            class="btn btn-primary rounded-0">{{ $order->is_coupon_applied == 0 ? translate('Apply') : translate('Remove') }}</button>
                    </div>
                </div>
            </form>
        </div>
    @endif


</div>