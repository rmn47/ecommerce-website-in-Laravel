@extends('seller.layouts.app')
@section('panel_content')

<div class="row">
    <!-- Products, Orders -->
    <div class="col-lg-12">
        <div class="row gutters-16">
            <!-- Total Preorder Products -->
            <div class="col-md-6 col-lg-3">
                <div class="dashboard-box bg-white h-200px mb-2rem overflow-hidden" style="border: 2px solid #2D4059">
                    <div class="d-flex flex-column justify-content-between h-80">
                        <div class="">
                            <div>
                                <h1 class="fs-14 fw-600 text-dark mb-1">{{ translate('Total Preorder Products')}}</h1>
                                <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Total product uploaded as preorder product') }}</h3>
                            </div>
                            <div class="mt-3">
                                <h1 class="fs-30 fw-600 text-dark mb-1">{{ $total_preorder_roducts }}</h1>
                                <h3 class="fs-13 fw-600 mb-0 mt-3">
                                    <a href="{{ route('seller.preorder-product.index') }}" class="text-dark">
                                        {{ translate('View all products') }}<i class="las la-arrow-right"></i>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Live Preorder Products -->
            <div class="col-md-6 col-lg-3">
                <div class="dashboard-box h-200px mb-2rem overflow-hidden preorder-dashboard-box" style="background-color: #FAFAFA;">
                    <div class="d-flex flex-column justify-content-between h-80">
                        <div class="">
                            <div>
                                <h1 class="fs-14 fw-600 text-dark mb-1">{{ translate('Live Preorder Products')}}</h1>
                                <h3 class="fs-12 fw-600 text-secondary mb-0">{{ translate('Preorder products currently available to order ') }}</h3>
                            </div>
                            <div class="mt-3">
                                <h1 class="fs-30 fw-600 text-dark mb-1">{{ $live_preorder_products }}</h1>
                                <h3 class="fs-13 fw-600 mb-0 mt-3">
                                    <a href="{{ route('seller.preorder-product.index') }}" class="text-dark">
                                        {{ translate('View all live products') }} <i class="las la-arrow-right"></i>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Delayed prepayment & Delayed Final Orders --}}
            <div class="col-sm-6 col-md-6 col-lg-3">
                <div class="dashboard-box h-200px mb-2rem overflow-hidden" style="background-color: #FEEFE5">
                    <div class="d-flex flex-column justify-content-between h-100">
                        <div class="row">
                            <div class="col-lg-12 col-xs-12" >
                                <div class="border-bottom-dashed">
                                    <p class="fs-14 fw-600 text-dark mb-1">
                                        <a href="{{ route('seller.delayed_prepayment_preorders.list') }}" class="text-dark">{{translate('Delayed prepayment orders')}} <i class="las la-arrow-right"></i></a>
                                    </p> 
                                    <p class="fs-20 fw-600 mb-0 pb-3">{{ $delayed_prepayment_orders_count }}</p>
                                </div>
                                <div class=" mt-3">
                                    <p class="fs-14 fw-600 text-dark mb-1">
                                        <a href="{{ route('seller.delayed_final_orders.list') }}"  class="text-dark">{{translate('Delayed Final Orders')}} <i class="las la-arrow-right"></i></a>
                                    </p>
                                    <p class="fs-20 fw-600 mb-0 pb-3">{{ $delayed_final_orders_count }}</p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            {{-- Add New Preorder Product --}}
            <div class="col-sm-6 col-md-6 col-lg-3 ">
                <div class="dashboard-box bg-white h-200px mb-2rem overflow-hidden" style="background-color: #E7F2FF">
                    <a href="{{ route('seller.preorder-product.create') }}" class=" mb-4 p-4 text-center h-180px">
                        <div class="m-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                                <g id="Group_22724" data-name="Group 22724" transform="translate(-1284 -875)">
                                    <rect id="Rectangle_17080" data-name="Rectangle 17080" width="2" height="48" rx="1"
                                        transform="translate(1307 875)" fill="#6B798A" />
                                    <rect id="Rectangle_17081" data-name="Rectangle 17081" width="2" height="48" rx="1"
                                        transform="translate(1332 898) rotate(90)" fill="#6B798A" />
                                </g>
                            </svg>
                        </div>
                        <div class="fs-16 fw-600 text-primary text-dark">
                            {{ translate('Add New Preorder Product') }}
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row gutters-16">
            <div class="col-md-6 col-lg-6">
                <div class="bg-white mb-2rem">
                    {{-- Sales Stats --}}
                    <div class="dashboard-box border p-4 h-md-320px">
                        <div class="row">
                            <div class="col-lg-6 col-xs-12" >
                                <div class="border-bottom-dashed mt-3">
                                    <p class="fs-16 fw-600 mb-0 pb-1">{{translate('Sales Stats')}}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-3 text-secondary">{{translate('All sales in pre order system')}}</p>
                                </div>
                                <div class="border-bottom-dashed mt-3">
                                    <p class="fs-14 fw-400 mb-0 pb-1">{{translate('Total sales')}}</p>
                                    <p class="fs-15 fw-600 mb-0 pb-3">{{format_price($total_sales) }}</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="container mt-3">
                                    <div class="card p-3" style="background-color: #6B798A;">
                                        <canvas id="yearlySaleChart"></canvas>
                                    </div>
                                </div>
                                <div class="px-3">
                                    <div class="bg-grey rounded-1 p-2 bg-gunmetal-blue">
                                        <div class="d-flex justify-content-between px-2 py-2">
                                            <p class="text-white m-0">{{translate('Sales this month')}}</p>
                                            <p class="text-white m-0">{{format_price($totalSalesThisMonth) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Order status --}}
                    <div class="dashboard-box border mt-4 p-4 h-md-260px">
                        <h4 class="fs-16 fw-600">{{translate('Order Status')}}</h4>
                        <p class="fs-12 fw-400">{{translate('Order status represents the delivery and order status
                            of your preorders.')}}</p>
                        <div class="row">
                            <div class="col-6">
                                <div class="bg-soft-secondary border p-3 rounded-1">
                                    <p class="fs-17 fw-600 m-0">{{ translate('In Shipping') }}</p>
                                    <p class="fs-20 fw-700 m-0">{{$in_shipping_orders}}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border p-3 rounded-1">
                                    <p class="fs-17 fw-600 m-0">{{ translate('Delivered') }}</p>
                                    <p class="fs-20 fw-700 m-0">{{$is_delivered_orders}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Preorder States --}}
            <div class="col-md-6 col-lg-6">
                <div class="dashboard-box bg-white mb-2rem overflow-hidden h-md-640px preorder-dashboard-box">
                    <div class="d-flex flex-column justify-content-between">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <p class="fs-16 fw-700" >{{ translate('Preorder States') }}</p>
                                    <p class="fs-12 fw-600 text-secondary mb-0">{{ translate('All states of the preorder system up-to final order. All the states here has multiple actions.') }}</p>
                                </div>


                                <div class="my-4 pt-4">
                                    <a href="{{ route('seller.all_preorder.list') }}">
                                        <button class="btn btn-block text-white bg-gunmetal-blue">{{translate('View all Preorders')}}</button>
                                    </a>
                                </div>
                                <div class="container my-4 px-0">
                                    <div class="card p-3 text-white" style="background-color: #6B798A;">
                                        <!-- grey background -->
                                        <canvas id="preorderStatesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-soft-secondary px-4 pt-2 pb-1 rounded-1">
                                    <p class="fs-14 fw-700 mb-0 pb-1">{{ translate('Preorder Requests') }}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-2 text-secondary">{{ translate('Customers applied for a preorder product') }}</p>
                                    <p class="fs-18 fw-700">{{$preorder_request_count}}</p>
                                </div>
                                <div class="pt-4 px-4">
                                    <p class="fs-14 fw-700 mb-0 pb-1">{{ translate('Accepted Requests') }}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-2 text-secondary">{{ translate('Requests accepted & order profile created') }}</p>
                                    <p class="fs-18 fw-700">{{$accepted_request_count}}</p>
                                </div>
                                <div class="pt-2 px-4">
                                    <p class="fs-14 fw-700 mb-0 pb-1">{{ translate('Prepayment Requests') }}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-2 text-secondary">{{ translate('Prepayment Requests for admin to verify') }}</p>
                                    <p class="fs-18 fw-700">{{$prepayment_request_count}}</p>
                                </div>
                                <div class="pt-2 px-4">
                                    <p class="fs-14 fw-700 mb-0 pb-1">{{ translate('Confirmed Prepayments') }}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-2 text-secondary">{{ translate('Prepayments accepted by admin') }}</p>
                                    <p class="fs-18 fw-700">{{$confirmed_prepayment_request_count}}</p>
                                </div>
                                <div class="pt-2 px-4">
                                    <p class="fs-14 fw-700 mb-0 pb-1">{{ translate('Final Preorders') }}</p>
                                    <p class="fs-12 fw-600 mb-0 pb-2 text-secondary">{{ translate('Completed orders of preorder products') }}</p>
                                    <p class="fs-18 fw-700">{{$final_preorder_request_count}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preorder by Product --}}
        <div class="border mt-4 p-4">
            <div class="dashboard-box bg-white mb-2rem overflow-hidden p-2rem">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h2 class="fs-16 fw-500 mb-2">{{ translate('Preorder by Product')}}</h2>
                        <h4 class="fs-13 fw-500 text-secondary mb-0">{{ translate('View preorders of the product and & order number') }}</h4>
                    </div>
                </div>
                <div class="position-relative">
                    <div class="top_sellers_product_table top-products-table table-responsive c-scrollbar-light show" style="width: 100%;">
                        <table class="table dashboard-table mb-0">
                            <thead>
                                <tr class="fs-11 fw-600 text-secondary">
                                    <th class="pl-0 border-top-0 border-bottom-1">{{ translate('Product') }}</th>
                                    <th class="border-top-0 border-bottom-1">{{ translate('Request') }}</th>
                                    <th class="border-top-0 border-bottom-1">{{ translate('Prepayment') }}</th>
                                    <th class="border-top-0 border-bottom-1">{{ translate('Final Order') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preorderProducts as $row)
                                    @php
                                        $product = \App\Models\PreorderProduct::where('id', $row['id'])->first();
                                        $product_url = route('preorder-product.details', $product->product_slug);
                                    @endphp
                                    <tr>
                                        <td class="pl-0" style="vertical-align: middle">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-2 overflow-hidden"
                                                    style="min-height: 48px !important; min-width: 48px !important;max-height: 48px !important; max-width: 48px !important;">
                                                    <a href="{{ $product_url }}" class="d-block" target="_blank">
                                                        <img src="{{ uploaded_asset($product->thumbnail) }}" alt="{{ translate('category')}}" 
                                                            class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </a>
                                                </div>
                                                <a href="{{ $product_url }}" target="_blank" class="d-block fw-400 ml-2 fs-13" title="{{ $product->getTranslation('product_name') }}">
                                                    {{ Str::limit($product->getTranslation('product_name'), 50, ' ...') }}
                                                </a>
                                            </div>
                                        </td>
                                        <td style="vertical-align: middle" class="fw-700">
                                            {{ $product->preorder->where('preorder_request_status',1)->count() }}
                                        </td>
                                        <td style="vertical-align: middle" class="fw-700">
                                            {{ $product->preorder->where('prepayment_confirm_status',1)->count() }}
                                        </td>
                                        <td style="vertical-align: middle" class="fw-700">
                                            {{ $product->preorder->where('final_order_status',1)->count() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
@section('script')
<!-- dashboard script -->
@include('backend.dashboard.dashboard_js')

<script type="text/javascript">

</script>
<script>
    $(document).ready(function(){
        preorder_by_products_tab('all');
    });

    $(".preorder_by_products_tab").click(function () {
        preorder_by_products_tab($(this).data("target"));
    });

    var monthlySalesData = @json($monthlySales);
    AIZ.plugins.chart('#yearlySaleChart', {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June','july', 'August', 'September','October','November', 'December'],
            datasets: [{
                fill: false,
                borderColor: '#ffffff',
                backgroundColor: '#ffffff',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 8,
                data: monthlySalesData,
                // data: [12, 19, 3, 5, 2, 3,12, 19, 3, 5, 2, 3],
            }, ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        },
    });

    AIZ.plugins.chart('#preorderStatesChart', {
        type: 'bar',
        data: {
            labels: [
                "{{ translate('Preorder Requests') }}",
                "{{ translate('Accepted Requests') }}",
                "{{ translate('Prepayment Requests') }}",
                "{{ translate('Confirmed Prepayments') }}",
                "{{ translate('Final Preorders') }}"
            ],
            datasets: [{
                fill: false,
                borderColor: '#ffffff',
                backgroundColor: '#ffffff',
                borderWidth: 2,
                borderRadius: 5,
                borderSkipped: false,
                barThickness: 8,
                data: [{{$preorder_request_count}}, {{$accepted_request_count}}, {{$prepayment_request_count}}, {{$confirmed_prepayment_request_count}}, {{$final_preorder_request_count}}],
            }, ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        },
    });

    function preorder_by_products_tab(user_type) {
        $("#preorder_by_products_section").html(spinner);
        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "POST",
            url: AIZ.data.appUrl + "/seller/preorder/dashboard/preorder-by-products-section",
            data: { user_type: user_type,},
            success: function (data) {
                $("#preorder_by_products_section").html(data);
                AIZ.plugins.slickCarousel();
            },
        });
    }
        

</script>

@endsection