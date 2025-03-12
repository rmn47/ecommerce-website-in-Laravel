@extends('backend.layouts.app')

@section('content')
@php
    $routeName = Route::currentRouteName();
@endphp

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="mx-4 mt-4">
            <h5 class="mb-md-0 h6">{{ translate('All Preorders') }}</h5>
            <div class="row">
                @if(!in_array($routeName, ['delayed_prepayment_preorders.list', 'delayed_final_orders.list']))
                    <div class="col-12 mt-4">
                        <div class="badges ">
                            @php
                                $activeClasss = 'bg-soft-dark fs-12 mr-2 my-2 p-3 rounded-3 text-white';
                                $inActiveClasses = 'preorder-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600';
                            @endphp
                            <input type="hidden" id="order_status" name="order_status" value="">
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'all' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('all')">
                                {{translate('All') }} ({{ $preorder_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'requested' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('requested')">
                                {{ translate('Requests')}} ({{ $preorder_request_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'accepted_requests' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('accepted_requests')">
                                {{ translate('Accepted Requests')}} ({{ $accepted_request_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'prepayment_requests' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('prepayment_requests')">
                                {{ translate('Prepayment Requests')}}  ({{ $prepayment_request_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'confirmed_prepayments' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('confirmed_prepayments')">
                                {{ translate('Confirmed Prepayments')}}  ({{ $confirmed_prepayment_request_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'final_preorders' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('final_preorders')">
                                {{ translate('Final Preorders')}}  ({{ $final_preorder_request_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'in_shipping' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('in_shipping')">
                                {{ translate('In Shipping')}}  ({{ $preorder_request_in_shipping_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'delivered' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('delivered')">
                                {{ translate('Delivered') }}  ({{ $preorder_product_delivered_count }})
                            </a>
                            <a href="javascript:void(0);" class="badge badge-inline {{ $status == 'refund' ? $activeClasss : $inActiveClasses}}" onclick="sort_order_by_status('refund')">
                                {{ translate('Refund') }}  ({{ $preorder_product_refunded_count }})
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row align-items-center mt-4">
                <!-- Left element -->
                <div class="col-9">
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="form-group mb-0">
                                <input type="text" class="aiz-date-range form-control form-control-sm" value="{{ $date }}"
                                    name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                                    data-separator=" to " data-advanced-range="true" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <input type="text" class="form-control form-control-sm" id="search"
                                name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset
                                placeholder="{{ translate('Search Orders') }}">
                        </div>
                        <div class="col-auto">
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-sm btn-soft-primary text-primary">{{ translate('Filter')}}</button>
                            </div>
                        </div>
                       
                    </div>
                </div>
                <!-- Right element -->
                <div class="col-3 text-end">
                    <div class="row">
                        <div class="col-lg-8">
                            <select class="form-control form-control-sm aiz-selectpicker" id="bulk_action">
                                <option value="">{{ translate('Bulk Action') }}</option>
                                @can('delete_preorder')
                                    <option value="bulk_delete">{{ translate('Bulk Delete') }}</option>
                                @endcan
                                @if((($routeName == 'delayed_prepayment_preorders.list' && auth()->user()->can('delayed_prepayment_preorder_notification_send')) ||
                                    ($routeName == 'delayed_final_orders.list' && auth()->user()->can('final_preorder_notification_send'))) && 
                                        $canSendNotification)
                                    <option value="prepayment_final_preorder_reminder">{{ translate('Send Notification') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-auto">
                            <div class="form-group mb-0">
                                <button type="button" onclick="bulkAction()" class="btn btn-sm btn-soft-primary text-primary">{{ translate('Apply')}}</button>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        @if (auth()->user()->can('delete_preorder'))
                            <th>
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-all">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            </th>
                        @else
                        <th data-breakpoints="lg">#</th>
                        @endif
                        <th>{{ translate('Product/Quantity') }}</th>
                        <th>{{ translate('Preorder Code').'/'.translate('Created') }}</th>
                        <th data-breakpoints="md">{{ translate('Price').'/'.translate('Prepayment') }}</th>
                        @if($routeName != 'inhouse_preorder.list')
                            <th data-breakpoints="md">{{ translate('Seller') }}</th>
                        @endif
                        <th data-breakpoints="md">{{ translate('Customer') }}</th>
                        <th data-breakpoints="md">{{ translate('Status') }}</th>
                        @if (addon_is_activated('refund_request'))
                            <th>{{ translate('Refund') }}</th>
                        @endif
                        <th class="text-right" width="15%">{{ translate('options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)
                    <tr>
                        @if (auth()->user()->can('delete_preorder') || auth()->user()->can('export_order'))
                        <td>
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{ $order->id }}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        </td>
                        @else
                        <td>{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                        @endif
                        <td class="pl-0" style="vertical-align: middle">
                            <div class="d-flex align-items-center">
                                <div class="rounded-2 overflow-hidden" style="min-height: 48px !important; min-width: 48px !important;max-height: 48px !important; max-width: 48px !important;">
                                    <img src="{{ uploaded_asset($order->preorder_product?->thumbnail) }}" alt="{{ translate('category')}}" 
                                            class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                <div class="ml-2">
                                    <span class="text-muted text-truncate-1">{{ Str::limit($order->preorder_product?->getTranslation('product_name'), 50, ' ...') }}</span>
                                    <br>
                                    <span class="opacity-60 text-muted text-truncate-1">{{ translate('QTY') }} : {{ $order->quantity }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-600 text-muted text-primary">{{ $order->order_code }}</span>
                            @if ($order->is_viewed == 0)
                                <span class="badge badge-inline badge-primary">{{ translate('New') }}</span>
                            @endif
                            <br>
                            <span class="opacity-60 text-muted text-truncate-1">{{ translate('Created') }} : {{ $order->created_at }}</span>
                        </td>
                        <td>{{ single_price($order->grand_total) }}/ {{ single_price($order->preorder_product?->is_prepayment ? $order->preorder_product->preorder_prepayment?->prepayment_amount : 0)}}</td>
                        @if($routeName != 'inhouse_preorder.list')
                            <td>
                                {{ $order->product_owner == 'admin' ? env('APP_NAME') : ( $order?->shop->name ?? $order->product_owner)}}
                            </td>
                        @endif
                        <td>
                            @if($order->user != null) 
                                <span class="text-muted text-truncate-1">{{ $order->user->name }}</span>
                                <br>
                                <span class="opacity-60 text-muted text-truncate-1">{{ $order->user->email ?? $order->user->phone }}</span>
                            @else
                                {{ translate('Customer not found') }}
                            @endif
                        </td>
                        <td>
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
                        @if (addon_is_activated('refund_request'))
                            <td> 
                                @if($order->preorder_product?->is_refundable == 1)
                                <span class="badge badge-inline badge-success m-2 p-2 rounded-3">{{ translate('Refundable')}}</span>
                                @else
                                <span class="badge badge-inline badge-soft-primary m-2 p-2 rounded-3">{{ translate('No Refund')}}</span>
                                @endif
                            </td>
                        @endif
                        <td class="text-right">
                            @can('view_preorder_details')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('preorder-order.show', encrypt($order->id)) }}"
                                    title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            @endcan
                            @can('download_preorder_invoice')
                                <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                    href="{{ route('preorder.invoice_download', $order->id) }}"
                                    title="{{ translate('Download Invoice') }}">
                                    <i class="las la-download"></i>
                                </a>
                            @endcan
                            @if((($routeName == 'delayed_prepayment_preorders.list' && auth()->user()->can('delayed_prepayment_preorder_notification_send')) ||
                                ($routeName == 'delayed_final_orders.list' && auth()->user()->can('final_preorder_notification_send'))) && 
                                    $canSendNotification)
                                <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                    href="javascript:void();" onclick="single_prepayment_final_preorder_reminder('{{ $order->id }}');"
                                    title="{{ ($routeName == 'delayed_prepayment_preorders.list') ? translate('Prepayment Reminder Notification') : translate('Final Order Reminder Notification') }}">
                                    <i class="las la-bell"></i>
                                </a>
                            @endif

                            @can('delete_preorder')
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                    data-href="{{ route('preorder-order.destroy', $order->id) }}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>

        </div>
    </form>
</div>
@endsection

@section('modal')
<!-- Delete modal -->
@include('modals.delete_modal')

<!-- Bulk Delete modal -->
@include('modals.bulk_delete_modal')

{{-- Bulk Prepayment/Final Order Notification --}}
<div id="prepayment_final_preorder_reminder_modal" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content pb-2rem px-2rem">
            <div class="modal-header border-0">
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <form class="form-horizontal" action="{{ route('prepayment_final_preorder_reminder') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-center">
                    <input type="hidden" name="order_ids" value="" id="order_ids">
                    <input type="hidden" name="reminder_type" value="{{ ($routeName == 'delayed_prepayment_preorders.list') ?  'preorder_prepayment_reminder_customer' : 'preorder_final_order_reminder_customer' }}" id="order_ids">
                    <p class="mt-2 mb-2 fs-16 fw-700">
                        {{ translate('Are you sure you want to send a notification for the selected orders?') }}
                    </p>
                    <button type="submit" class="btn btn-warning rounded-2 mt-2 fs-13 fw-700 w-250px">
                        {{ translate('Send Notification') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).on("change", ".check-all", function() {
        if (this.checked) {
            // Iterate each checkbox
            $('.check-one:checkbox').each(function() {
                this.checked = true;
            });
        } else {
            $('.check-one:checkbox').each(function() {
                this.checked = false;
            });
        }
    });
    
    function bulk_delete() {
        let orderIds = [];
        $(".check-one[name='id[]']:checked").each(function() {
            orderIds.push($(this).val());
        });
        $.post('{{ route('bulk-preorder-delete') }}', { _token: '{{ csrf_token() }}', order_ids: orderIds }, function(data) {
            if (data == 1) {
                AIZ.plugins.notify('success', '{{ translate('Orders deleted successfully') }}');
            } else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
            location.reload();
        });
    }

    function bulkAction() {
        var actionType = $('#bulk_action').val();
        if(actionType == 'bulk_delete'){
            $('#bulk-delete-modal').modal('show', {backdrop: 'static'});
        }
        if(actionType == 'prepayment_final_preorder_reminder'){
            bulk_prepayment_final_preorder_reminder();
        }
    }

    // Bulk Prepayment/Final Order Notification 
    function single_prepayment_final_preorder_reminder(order_id){
        var orderIds = [];
        orderIds.push(order_id);
        $('#order_ids').val(orderIds);
        $('#prepayment_final_preorder_reminder_modal').modal('show', {backdrop: 'static'});
    }

    // Bulk Prepayment/Final Order Notification 
    function bulk_prepayment_final_preorder_reminder(){
        var orderIds = [];
        $(".check-one[name='id[]']:checked").each(function() {
            orderIds.push($(this).val());
        });
        if(orderIds.length > 0){
            $('#order_ids').val(orderIds);
            $('#prepayment_final_preorder_reminder_modal').modal('show', {backdrop: 'static'});
        }
        else{
            AIZ.plugins.notify('danger', '{{ translate('Please Select Preorder first.') }}');
        }
    }

    function sort_orders(el) {
        $('#sort_orders').submit();
    }

    function sort_order_by_status(userType) {
        $('#order_status').val(userType);
        sort_orders();
    }

</script>
@endsection