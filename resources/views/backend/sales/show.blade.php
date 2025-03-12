@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="h2 fs-16 mb-0">{{ translate('Order Details') }}</h1>
        </div>
        <div class="card-body">
            <div class="row gutters-5">
                <div class="col text-md-left text-center">
                </div>
                @php
                    $delivery_status = $order->delivery_status;
                    $payment_status = $order->payment_status;
                    $admin_user_id = get_admin()->id;
                @endphp
                @if ($order->seller_id == $admin_user_id || get_setting('product_manage_by_admin') == 1)
                    <!-- Assign Delivery Boy -->
                    @if (addon_is_activated('delivery_boy'))
                        <div class="col-md-3 ml-auto">
                            <label for="assign_deliver_boy">{{ translate('Assign Delivery Boy') }}</label>
                            @if (($delivery_status == 'pending' || $delivery_status == 'confirmed' || $delivery_status == 'picked_up') && auth()->user()->can('assign_delivery_boy_for_orders'))
                                <select class="form-control aiz-selectpicker" data-live-search="true" data-minimum-results-for-search="Infinity" id="assign_deliver_boy">
                                    <option value="">{{ translate('Select Delivery Boy') }}</option>
                                    @foreach ($delivery_boys as $delivery_boy)
                                        <option value="{{ $delivery_boy->id }}" @if ($order->assign_delivery_boy == $delivery_boy->id) selected @endif>
                                            {{ $delivery_boy->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control" value="{{ optional($order->delivery_boy)->name }}" disabled>
                            @endif
                        </div>
                    @endif

                    <!-- Payment Status -->
                    <div class="col-md-3 ml-auto">
                        <label for="update_payment_status">{{ translate('Payment Status') }}</label>
                        @if (auth()->user()->can('update_order_payment_status') && $payment_status == 'unpaid')
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity" id="update_payment_status" onchange="confirm_payment_status()">
                                <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                                    {{ translate('Unpaid') }}
                                </option>
                                <option value="paid" @if ($payment_status == 'paid') selected @endif>
                                    {{ translate('Paid') }}
                                </option>
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ ucfirst($payment_status) }}" disabled>
                        @endif
                    </div>

                    <!-- Delivery Status -->
                    <div class="col-md-3 ml-auto">
                        <label for="update_delivery_status">{{ translate('Delivery Status') }}</label>
                        @if (auth()->user()->can('update_order_delivery_status') && $delivery_status != 'delivered' && $delivery_status != 'cancelled')
                            <select class="form-control aiz-selectpicker" data-minimum-results-for-search="Infinity" id="update_delivery_status" onchange="confirm_delivery_status()">
                                <option value="pending" @if ($delivery_status == 'pending') selected @endif>
                                    {{ translate('Pending') }}
                                </option>
                                <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                                    {{ translate('Confirmed') }}
                                </option>
                                <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>
                                    {{ translate('Picked Up') }}
                                </option>
                                <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                                    {{ translate('On The Way') }}
                                </option>
                                <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                                    {{ translate('Delivered') }}
                                </option>
                                <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>
                                    {{ translate('Cancel') }}
                                </option>
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ ucfirst(str_replace('_', ' ', $delivery_status)) }}" disabled>
                        @endif
                    </div>

                    <!-- Tracking Code -->
                    <div class="col-md-3 ml-auto">
                        <label for="update_tracking_code">{{ translate('Tracking Code (optional)') }}</label>
                        <input type="text" class="form-control" id="update_tracking_code" value="{{ $order->tracking_code }}">
                    </div>
                @endif
            </div>

            <!-- QR Code -->
            <div class="mb-3">
                @php
                    $qrCodeContent = "";
                    if (json_decode($order->shipping_address)) {
                        $shippingAddress = json_decode($order->shipping_address);
                        $qrCodeContent .= "Shipping Address:\n";
                        $qrCodeContent .= "Name: " . $shippingAddress->name . "\n";
                        $qrCodeContent .= "Email: " . $shippingAddress->email . "\n";
                        $qrCodeContent .= "Phone: " . $shippingAddress->phone . "\n";
                        $qrCodeContent .= "Address: " . $shippingAddress->address . ", " . $shippingAddress->city;
                        if (isset($shippingAddress->state)) {
                            $qrCodeContent .= ", " . $shippingAddress->state . " - ";
                        }
                        $qrCodeContent .= " " . $shippingAddress->postal_code . "\n";
                        $qrCodeContent .= "Country: " . $shippingAddress->country . "\n";
                    } else {
                        $qrCodeContent .= "Shipping Address:\n";
                        $qrCodeContent .= "Name: " . $order->user->name . "\n";
                        $qrCodeContent .= "Email: " . $order->user->email . "\n";
                        $qrCodeContent .= "Phone: " . $order->user->phone . "\n";
                    }
                    if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true))) {
                        $paymentData = json_decode($order->manual_payment_data);
                        $qrCodeContent .= "\nPayment Information:\n";
                        $qrCodeContent .= "Name: " . $paymentData->name . "\n";
                        $qrCodeContent .= "Amount: " . single_price($paymentData->amount) . "\n";
                        $qrCodeContent .= "TRX ID: " . $paymentData->trx_id . "\n";
                        $qrCodeContent .= "Payment Proof: " . uploaded_asset($paymentData->photo) . "\n";
                    }
                    $qrCodeContent .= "\nOrder Details:\n";
                    $qrCodeContent .= "Order #: " . $order->code . "\n";
                    $qrCodeContent .= "Order Status: " . ucfirst(str_replace('_', ' ', $order->delivery_status)) . "\n";
                    $qrCodeContent .= "Order Date: " . date('d-m-Y h:i A', $order->date) . "\n";
                    $qrCodeContent .= "Total Amount: " . single_price($order->grand_total) . "\n";
                    $qrCodeContent .= "Payment Method: " . ucfirst(str_replace('_', ' ', $order->payment_type)) . "\n";
                    $qrCodeContent .= "Additional Info: " . ($order->additional_info ?? 'N/A') . "\n";
                    $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                @endphp
                {!! str_replace($removedXML, '', QrCode::size(100)->encoding('UTF-8')->generate($qrCodeContent)) !!}
            </div>

            <!-- Order Information -->
            <div class="row gutters-5">
                <div class="col text-md-left text-center">
                    @if(json_decode($order->shipping_address))
                        <address>
                            <strong class="text-main">{{ json_decode($order->shipping_address)->name }}</strong><br>
                            {{ json_decode($order->shipping_address)->email }}<br>
                            {{ json_decode($order->shipping_address)->phone }}<br>
                            {{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->city }}, 
                            @if(isset(json_decode($order->shipping_address)->state)) {{ json_decode($order->shipping_address)->state }} - @endif 
                            {{ json_decode($order->shipping_address)->postal_code }}<br>
                            {{ json_decode($order->shipping_address)->country }}
                        </address>
                    @else
                        <address>
                            <strong class="text-main">{{ $order->user->name }}</strong><br>
                            {{ $order->user->email }}<br>
                            {{ $order->user->phone }}<br>
                        </address>
                    @endif
                    @if ($order->manual_payment && is_array(json_decode($order->manual_payment_data, true)))
                        <br>
                        <strong class="text-main">{{ translate('Payment Information') }}</strong><br>
                        {{ translate('Name') }}: {{ json_decode($order->manual_payment_data)->name }},
                        {{ translate('Amount') }}: {{ single_price(json_decode($order->manual_payment_data)->amount) }},
                        {{ translate('TRX ID') }}: {{ json_decode($order->manual_payment_data)->trx_id }}
                        <br>
                        <a href="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" target="_blank">
                            <img src="{{ uploaded_asset(json_decode($order->manual_payment_data)->photo) }}" alt="" height="100">
                        </a>
                    @endif
                </div>
                <div class="col-md-4">
                    <table class="ml-auto">
                        <tbody>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order #') }}</td>
                                <td class="text-info text-bold text-right">{{ $order->code }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Status') }}</td>
                                <td class="text-right">
                                    @if ($delivery_status == 'delivered')
                                        <span class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                    @else
                                        <span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $delivery_status))) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Order Date') }}</td>
                                <td class="text-right">{{ date('d-m-Y h:i A', $order->date) }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Total amount') }}</td>
                                <td class="text-right">{{ single_price($order->grand_total) }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Payment method') }}</td>
                                <td class="text-right">{{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                            </tr>
                            <tr>
                                <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                                <td class="text-right">{{ $order->additional_info }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <hr class="new-section-sm bord-no">

            <!-- Order Details Table -->
            <div class="row">
                <div class="col-lg-12 table-responsive">
                    <table class="table-bordered aiz-table invoice-summary table">
                        <thead>
                            <tr class="bg-trans-dark">
                                <th data-breakpoints="lg" class="min-col">#</th>
                                <th width="10%">{{ translate('Photo') }}</th>
                                <th class="text-uppercase">{{ translate('Description') }}</th>
                                <th data-breakpoints="lg" class="text-uppercase">{{ translate('Delivery Type') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">{{ translate('Qty') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-center">{{ translate('Price') }}</th>
                                <th data-breakpoints="lg" class="min-col text-uppercase text-right">{{ translate('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank">
                                                <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                            </a>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank">
                                                <img height="50" src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}">
                                            </a>
                                        @else
                                            <strong>{{ translate('N/A') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                            <strong>
                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-muted">
                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                </a>
                                            </strong>
                                            <small>{{ $orderDetail->variation }}</small>
                                            <br>
                                            <small>
                                                @php
                                                    $product_stock = $orderDetail->product->stocks->where('variant', $orderDetail->variation)->first();
                                                @endphp
                                                {{ translate('SKU') }}: {{ $product_stock['sku'] ?? '' }}
                                            </small>
                                        @elseif ($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                            <strong>
                                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}" target="_blank" class="text-muted">
                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                </a>
                                            </strong>
                                        @else
                                            <strong>{{ translate('Product Unavailable') }}</strong>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
                                            {{ translate('Home Delivery') }}
                                        @elseif ($order->shipping_type == 'pickup_point')
                                            @if ($order->pickup_point != null)
                                                {{ $order->pickup_point->getTranslation('name') }} ({{ translate('Pickup Point') }})
                                            @else
                                                {{ translate('Pickup Point') }}
                                            @endif
                                        @elseif($order->shipping_type == 'carrier')
                                            @if ($order->carrier != null)
                                                {{ $order->carrier->name }} ({{ translate('Carrier') }})
                                                <br>
                                                {{ translate('Transit Time') . ' - ' . $order->carrier->transit_time }}
                                            @else
                                                {{ translate('Carrier') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $orderDetail->quantity }}</td>
                                    <td class="text-center">{{ single_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                    <td class="text-center">{{ single_price($orderDetail->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="clearfix float-right">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong class="text-muted">{{ translate('Sub Total') }} :</strong></td>
                            <td>{{ single_price($order->orderDetails->sum('price')) }}</td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted">{{ translate('Tax') }} :</strong></td>
                            <td>{{ single_price($order->orderDetails->sum('tax')) }}</td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted">{{ translate('Shipping') }} :</strong></td>
                            <td>{{ single_price($order->orderDetails->sum('shipping_cost')) }}</td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted">{{ translate('Coupon') }} :</strong></td>
                            <td>{{ single_price($order->coupon_discount) }}</td>
                        </tr>
                        <tr>
                            <td><strong class="text-muted">{{ translate('TOTAL') }} :</strong></td>
                            <td class="text-muted h5">{{ single_price($order->grand_total) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="no-print text-right">
                    <a href="{{ route('invoice.download', $order->id) }}" type="button" class="btn btn-icon btn-light"><i class="las la-print"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Confirm Delivery Status Modal -->
    <div id="confirm-delivery-status" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                            <g id="alert" transform="translate(0.14 1.02)">
                                <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                            </g>
                        </g>
                    </svg>
                    <p class="mt-3 mb-3 fs-16 fw-700">{{ translate('Are you sure you want to change the delivery status? An email will be sent to the user.') }}</p>
                    <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="button" onclick="update_delivery_status()" class="btn btn-success rounded-2 mt-2 fs-13 fw-700 w-150px">{{ translate('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Payment Status Modal -->
    <div id="confirm-payment-status" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                            <g id="alert" transform="translate(0.14 1.02)">
                                <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                            </g>
                        </g>
                    </svg>
                    <p class="mt-3 mb-3 fs-16 fw-700">{{ translate('Are you sure you want to change the payment status?') }}</p>
                    <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{ translate('Cancel') }}</button>
                    <button type="button" onclick="update_payment_status()" class="btn btn-success rounded-2 mt-2 fs-13 fw-700 w-150px">{{ translate('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        // Assign Delivery Boy
        $('#assign_deliver_boy').on('change', function() {
            var order_id = {{ $order->id }};
            var delivery_boy = $('#assign_deliver_boy').val();
            $.post('{{ route('orders.delivery-boy-assign') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                delivery_boy: delivery_boy
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery boy has been assigned') }}');
            }).fail(function(xhr) {
                console.log('Error:', xhr.responseText);
                AIZ.plugins.notify('danger', '{{ translate('Failed to assign delivery boy') }}');
            });
        });

        // Delivery Status Update
        function confirm_delivery_status() {
            $('#confirm-delivery-status').modal('show');
        }

        function update_delivery_status() {
            $('#confirm-delivery-status').modal('hide');
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            console.log('Sending:', { order_id, status });
            $.post('{{ route('orders.update_delivery_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Delivery status has been updated and an email has been sent to the user.') }}');
                location.reload();
            }).fail(function(xhr) {
                console.log('Error Response:', xhr.responseText);
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong while updating the delivery status.') }}');
            });
        }

        $('#confirm-delivery-status').on('hidden.bs.modal', function() {
            $('#update_delivery_status').focus();
        });

        // Payment Status Update
        function confirm_payment_status() {
            $('#confirm-payment-status').modal('show');
        }

        function update_payment_status() {
            $('#confirm-payment-status').modal('hide');
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val(); // Get the selected status
            console.log('Sending:', { order_id, status });
            $.post('{{ route('orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                $('#update_payment_status').prop('disabled', true);
                AIZ.plugins.bootstrapSelect('refresh');
                AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
                location.reload();
            }).fail(function(xhr) {
                console.log('Error:', xhr.responseText);
                AIZ.plugins.notify('danger', '{{ translate('Failed to update payment status') }}');
            });
        }

        $('#confirm-payment-status').on('hidden.bs.modal', function() {
            $('#update_payment_status').focus();
        });

        // Tracking Code Update
        $('#update_tracking_code').on('change', function() {
            var order_id = {{ $order->id }};
            var tracking_code = $('#update_tracking_code').val();
            $.post('{{ route('orders.update_tracking_code') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                tracking_code: tracking_code
            }, function(data) {
                AIZ.plugins.notify('success', '{{ translate('Order tracking code has been updated') }}');
            }).fail(function(xhr) {
                console.log('Error:', xhr.responseText);
                AIZ.plugins.notify('danger', '{{ translate('Failed to update tracking code') }}');
            });
        });
    </script>
@endsection