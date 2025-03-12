<div class=" border p-4 mt-4 rounded-2">
    <p class="fs-16"><b>{{translate('Preorder Status')}}</b>
    </p>
    <div class="w-100 my-3 "></div>
    <div class="order-summary">
        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->request_preorder_status !== 0 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Requested Pre order')}}</span>
                <span class="opacity-60">{{ $order->request_preorder_time != null ? \Carbon\Carbon::parse($order->request_preorder_time)->format('H:i \h\r\s, j F, Y') : '' }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->request_preorder_status==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Pre order request accepted')}}</span>
                <span class="opacity-60">{{ $order->request_preorder_time != null ? \Carbon\Carbon::parse($order->request_preorder_time)->format('H:i \h\r\s, j F, Y') : '' }}</span>
            </div>
        </div>
        @if($order->preorder_product?->is_prepayment)
        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->prepayment_confirm_status !== 0 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Prepayment confirmation')}}</span>
                <span class="opacity-60">{{ $order->prepayment_confirmation_time != null ? \Carbon\Carbon::parse($order->prepayment_confirmation_time)->format('H:i \h\r\s, j F, Y') : '' }}</span>
            </div>
        </div>
        @endif
        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->prepayment_confirm_status==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Order confirmed')}}</span>
                <span class="opacity-60">{{ $order->prepayment_confirmation_time != null ? \Carbon\Carbon::parse($order->prepayment_confirmation_time)->format('H:i \h\r\s, j F, Y') : '' }}</span>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ (($order->preorder_product->available_date != null && (strtotime($order->preorder_product->available_date) < strtotime(date('d-m-Y')))) || $order->preorder_product->is_available) ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}"></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Product is live')}}</span>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->final_order_status ==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Final order ')}}</span>
                <span class="opacity-60">{{$order->final_order_time != null ? \Carbon\Carbon::parse($order->final_order_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->shipping_status ==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Product In Shipping')}}</span>
                <span class="opacity-60">{{$order->shipping_time != null ? \Carbon\Carbon::parse($order->shipping_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
            </div>
        </div>

        <div class="d-flex align-items-center mb-4 p-0">
            <div>
                <i class="las {{ $order->delivery_status == 2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}  "></i>
            </div>
            <div class="d-flex flex-column ml-4">
                <span>{{translate('Product Delivered')}}</span>
                <span class="opacity-60">{{$order->delivery_time != null ? \Carbon\Carbon::parse($order->delivery_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
            </div>
        </div>
    </div>
</div>