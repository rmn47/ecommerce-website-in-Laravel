@php
    $pickup_point_list = array();
    if (get_setting('pickup_point') == 1) {
        $pickup_point_list = get_all_pickup_points();
    }
@endphp

<!-- Inhouse Products -->

    <div class="card mb-3 border-left-0 border-top-0 border-right-0 border-bottom rounded-0 shadow-none">
        <div class="card-header py-3 px-0 border-left-0 border-top-0 border-right-0 border-bottom border-dashed">
            <h5 class="fs-16 fw-700 text-dark mb-0">{{ get_setting('site_name')  }}</h5>
        </div>
        <div class="card-body p-0">
            @include('preorder.frontend.order.partials.delivery_info_details', ['product' => $order->preorder_product, 'order' => $order,  'pickup_point_list' => $pickup_point_list, 'owner_id' => get_admin()->id ])
        </div>
    </div>

