@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card shadow-none rounded-0 border">
        <div class="card-header border-bottom-0">
            <h5 class="mb-0 fs-20 fw-700 text-dark">{{ translate('Preorder List') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead class="text-gray fs-12">
                    <tr>
                        <th class="pl-0 text-center">{{ translate('Order Code')}}</th>
                        <th data-breakpoints="md" class="text-center">{{ translate('Date')}}</th>
                        <th class="text-center">{{ translate('Amount')}}</th>
                        <th class="text-center">{{ translate('Status')}}</th>
                        <th class="text-right pr-0">{{ translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody class="fs-14">
                    @foreach ($orders as $key => $order)

                    <tr class="align-middle text-center">
                        <!-- Code -->
                        <td class="pl-0 d-flex align-items-center">
                            <div class="col-auto">
                                <img src="{{ uploaded_asset($order->preorder_product?->thumbnail) }}" alt="Image" class="size-50px img-fit" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                            <div class="col">
                                <a href="{{ route('preorder.order_details', encrypt($order->id)) }}">{{ $order->order_code }}</a>
                            </div>
                        </td>
                        <!-- Date -->
                        <td class="text-secondary align-middle">{{ \Carbon\Carbon::parse($order->created_at)->format('j F, Y') }}</td>
                        <!-- Amount -->
                        <td class="fw-700 align-middle">
                            {{ single_price($order->grand_total) }}
                        </td>
                        <!-- Status -->
                        <td class="fw-700 align-middle">
                            @if($order->refund_status == 2)
                                <span class="badge badge-inline badge-success m-2 p-2 rounded-3">{{ translate('Refunded')}}</span>
                            @elseif($order->delivery_status == 2)
                                <span class="badge badge-inline badge-success m-2 p-2 rounded-3">{{ translate('Delivered')}}</span>
                            @elseif($order->shipping_status == 2)
                                <span class="badge badge-inline badge-info p-2 m-2 rounded-3">{{ translate('In Shipping')}}</span>
                            @elseif($order->final_order_status == 1)
                                <span class="badge badge-inline badge-warning p-2 m-2 rounded-3">{{ translate('Final Order Requested')}}</span>
                            @elseif($order->final_order_status == 2)
                                <span class="badge badge-inline badge-success p-2 m-2 rounded-3">{{ translate('Final Order Accepted')}}</span>
                            @elseif($order->final_order_status == 3)
                                <span class="badge badge-inline badge-danger p-2 m-2 rounded-3">{{ translate('Final Order Cancelled')}}</span>
                            @elseif($order->prepayment_confirm_status == 1)
                                <span class="badge badge-inline badge-primary p-2 m-2 rounded-3">{{ translate('Prepayment Requested')}}</span>
                            @elseif($order->prepayment_confirm_status == 2)
                                <span class="badge badge-inline badge-dodger-blue p-2 m-2 rounded-3">{{ translate('Prepayment Accepted')}}</span>
                            @elseif($order->prepayment_confirm_status == 3)
                                <span class="badge badge-inline badge-dodger-blue p-2 m-2 rounded-3">{{ translate('Prepayment Cancelled')}}</span>
                            @elseif($order->request_preorder_status == 1)
                                <span class="badge badge-inline badge-secondary p-2 m-2 rounded-3">{{ translate('Preorder Requested')}}</span>
                            @elseif($order->request_preorder_status == 2)
                                <span class="badge badge-inline badge-gray p-2 m-2 rounded-3">{{ translate('Preorder Request Accepted')}}</span>
                            @elseif($order->request_preorder_status == 2)
                                <span class="badge badge-inline badge-danger p-2 m-2 rounded-3">{{ translate('Preorder Request Cancelled')}}</span>
                            @endif
                        </td>
                        <!-- Options -->
                        <td class="text-right pr-0 align-middle">
                            <!-- Details -->
                            <a href="{{ route('preorder.order_details', encrypt($order->id)) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" title="{{ translate('Order Details') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="10" viewBox="0 0 12 10">
                                    <g id="Group_24807" data-name="Group 24807" transform="translate(-1339 -422)">
                                        <rect id="Rectangle_18658" data-name="Rectangle 18658" width="12" height="1" transform="translate(1339 422)" fill="#3490f3" />
                                        <rect id="Rectangle_18659" data-name="Rectangle 18659" width="12" height="1" transform="translate(1339 425)" fill="#3490f3" />
                                        <rect id="Rectangle_18660" data-name="Rectangle 18660" width="12" height="1" transform="translate(1339 428)" fill="#3490f3" />
                                        <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12" height="1" transform="translate(1339 431)" fill="#3490f3" />
                                    </g>
                                </svg>
                            </a>
                            <!-- Invoice -->
                            <a class="btn btn-soft-secondary-base btn-icon btn-circle btn-sm hov-svg-white mt-2 mt-sm-0" href="{{ route('preorder.invoice_download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12.001" viewBox="0 0 12 12.001">
                                    <g id="Group_24807" data-name="Group 24807" transform="translate(-1341 -424.999)">
                                        <path id="Union_17" data-name="Union 17" d="M13936.389,851.5l.707-.707,2.355,2.355V846h1v7.1l2.306-2.306.707.707-3.538,3.538Z" transform="translate(-12592.95 -421)" fill="#f3af3d" />
                                        <rect id="Rectangle_18661" data-name="Rectangle 18661" width="12" height="1" transform="translate(1341 436)" fill="#f3af3d" />
                                    </g>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            <div class="aiz-pagination mt-2">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')

@endsection

