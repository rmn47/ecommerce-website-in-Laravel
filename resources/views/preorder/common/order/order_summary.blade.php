<div class="order-summary">
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate('Subtotal') }} ({{$order->unit_price}} x {{$order->quantity}})</span>
        <span>{{ format_price($order->unit_price * $order->quantity) }} </span>
    </div>
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate('Vat & Tax') }}</span>
        <span>{{ format_price($order->tax) }}</span>
    </div>
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate('Shipping Cost') }}</span>
        <span>{{ format_price($order->shipping_cost) }}</span>
    </div>
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate('Product Discount') }}</span>
        <span>{{ format_price($order->product_discount) }}</span>
    </div>

    @if($order->is_coupon_applied)
        <div class="d-flex justify-content-between mt-2">
            <span>{{ translate('Coupon Discount') }}</span>
            <span>{{ format_price($order->coupon_discount) }}</span>
        </div>  
    @endif                 
    @if( $order->prepayment !== null ) 
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate('Prepayment') }}</span>
        <span>{{ format_price($order->prepayment) }}</span>
    </div>
    <div class="d-flex justify-content-between mt-2">
        <span>{{ translate($order->final_order_status !=2 ? 'Remaining' : 'Final Payment') }}</span>
        <span>{{ format_price($order->grand_total - $order->prepayment) }}</span>
    </div>
    @endif                  
    <div class="d-flex justify-content-between mt-2 fw-700">
        <span class="fw-bold ">{{ translate('TOTAL') }}</span>
        <span class="total text-orange fs-16">{{ format_price($order->grand_total) }}</span>
    </div>
</div>