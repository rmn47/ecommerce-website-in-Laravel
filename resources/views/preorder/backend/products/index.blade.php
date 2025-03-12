@extends('backend.layouts.app')

@section('content')
    @php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    @endphp

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{ translate('All Preorder Products') }}</h1>
            </div>
            @can('add_preorder_product')
                <div class="col text-right">
                    <a href="{{ route('preorder-product.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Product') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>
    <br>

    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header">
                <div class="row">

                    <div class="col-12 d-flex align-items-center">
                        <h5 class="mb-0 me-2 h6">{{ translate('All Preorder Products') }}</h5>
                        <span class="ml-4 fs-16">
                            {{ translate('Frontend Page URL ') }} :  
                            <a href="{{ route('home')  . '/all-preorder-products' }}" target="_blank" rel="noopener noreferrer">
                                {{ route('home')  . '/all-preorder-products' }}
                            </a>
                        </span>
                    </div>

                    <div class="col-8 mt-4">
                        <div class="badges ">
                            <div class="d-flex">
                                <input type="hidden" id="user_type" name="user_type" value="">
                                <div class="bg-light rounded-1">
                                    @php
                                        $activeClasss = 'p-3 m-2 text-white rounded-3 fs-12 bg-soft-dark';
                                        $inActiveClasses = 'bg-white p-3 m-2 rounded-3 text-muted fs-12 fw-600';
                                    @endphp
                                    <a href="javascript:void(0);" class="badge badge-inline {{ $type == 'all' ? $activeClasss : $inActiveClasses}}" onclick="sort_product_by_user('all')">{{ translate('All') }}</a>
                                    <a href="javascript:void(0);" class="badge badge-inline {{ $type == 'in_house' ? $activeClasss : $inActiveClasses}}" onclick="sort_product_by_user('in_house')">{{ translate('In-house') }}({{ $inHouseProductCount }})</a>
                                    <a href="javascript:void(0);" class="badge badge-inline {{ $type == 'seller' ? $activeClasss : $inActiveClasses}}" onclick="sort_product_by_user('seller')">{{ translate('Seller’s') }}({{ $sellerProductCount }})</a>
                                </div>
                                <div>
                                    <span class="badge badge-inline preorder-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Published') }}({{ $publishedProductCount }})</span>
                                    <span class="badge badge-inline preorder-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Unpublished') }}({{ $unpublishedProductCount }})</span>
                                    <span class="badge badge-inline preorder-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Discounted') }}({{ $discountedProductCount }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="row ml-0">
                            @can('delete_preorder_product')
                                <div class="dropdown mb-2 mb-md-0">
                                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                                        {{translate('Bulk Action')}}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal"> {{translate('Delete selection')}}</a>
                                    </div>
                                </div>
                            @endcan
                            <div class="col-lg-2">
                                <select class="form-control form-control-sm aiz-selectpicker" name="type" onchange="sort_products()">
                                    <option value="">{{ translate('Filter by') }}</option>
                                    <option value="unit_price,desc" @isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>{{translate('Base Price (High > Low)')}}</option>
                                    <option value="unit_price,asc" @isset($col_name , $query) @if($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>{{translate('Base Price (Low > High)')}}</option>
                                </select>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-0">
                                    <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Search Products') }}">

                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-sm btn-soft-primary text-primary fw-700">{{ translate('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr class="text-muted fs-12 fw-600">
                            @if (auth()->user()->can('product_delete'))
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
                            <th>{{ translate('Image') }}</th>
                            <th data-breakpoints="md" width="15%">{{ translate('Product details') }}</th>
                            <th data-breakpoints="sm">{{ translate('Product details') }}</th>
                            <th data-breakpoints="lg">{{ translate('Price') }}</th>
                            <th data-breakpoints="lg">{{ translate('Discount') }}</th>
                            <th data-breakpoints="lg">{{ translate('Availability') }}</th>
                            <th data-breakpoints="lg">{{ translate('Orders') }}</th>
                            <th data-breakpoints="lg">{{ translate('Status') }}</th>
                            @if($type == 'seller' && get_setting('product_approve_by_admin'))
                                <th data-breakpoints="lg">{{ translate('Approval') }}</th>
                            @endif
                            <th data-breakpoints="sm" class="text-right">{{ translate('Actions') }}</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                            <tr>
                                @if (auth()->user()->can('product_delete'))
                                    <td>
                                        <div class="form-group d-inline-block">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="ids[]" value="{{ $product->id }}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </td>
                                @else
                                    <td>{{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                @endif
                                <td>
                                    <div class="row gutters-5 ">
                                        <div class="col-auto">
                                            <img src="{{ uploaded_asset($product->thumbnail) }}" alt="Image" class="size-50px img-fit">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="text-muted text-truncate-3 break-word ">{{ Str::limit($product?->getTranslation('product_name'), 50, ' ...')  }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2">{{ translate('Category') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700">{{ $product->category?->name }}</span>
                                    </div>
                                    <div>
                                        <span class="text-muted text-truncate-2 text-info">{{ translate( $product->user->user_type != 'seller' ? 'In-house' : $product->user->shop->name) }}</span>
                                        <span class="text-muted text-truncate-2 fw-700">{{ translate('Product Created :') . $product->created_at->format('d.m.Y') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Min Purchase Qty') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700 fs-13">{{ $product->min_qty . ' ' . $product->unit }}</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Refund') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700 fs-13">{{ $product->is_refundable ? 'Refundable' : 'Not Refundable' }}</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Price') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700 fs-13">{{ $product->unit_price . ' / ' . $product->unit }}
                                            <span class="badge badge-inline badge-soft-success fs-13 fw-600 p-2 rounded-pill ml-1">{{ translate('Fixed') }}</span>
                                        </span>
                                    </div>
                                    @if($product->is_prepayment)
                                        <div class="mb-2">
                                            <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Prepayment') }}</span>
                                            <span class="text-muted text-truncate-2 fw-700 fs-13">{{ $product->preorder_prepayment?->prepayment_amount }}
                                                <span class="badge badge-inline badge-soft-primary fs-13 fw-600 p-2 rounded-pill">{{ translate('Needed') }}</span>
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($product->discount > 0)
                                        <div class="bg-soft-primary px-4 py-2 rounded-1 mt-2">
                                            <span class="opacity-60 text-muted text-truncate-2 fs-12 text-primary">
                                                -{{ $product->discount_type == 'flat' ? single_price($product->discount) : $product->discount.'%' }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_available || $product->available_date != null)
                                        <div class="bg-soft-secondary px-4 py-2 rounded-1 mt-2">
                                            @if($product->is_available)
                                                <span class="text-muted text-truncate-2 fw-700 fs-13 text-success">{{ translate('Available now') }}</span>
                                            @elseif($product->available_date != null)

                                                <span class="opacity-60 text-muted text-truncate-2 fs-12 text-secondary">{{ $product->available_date }}</span>
                                                <span class="text-muted text-truncate-2 fw-700 fs-13 text-secondary">{{strtotime($product->available_date) > strtotime(date('d-m-Y'))  ? translate('Approx') : translate('Available now') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('PreOrder') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700 fs-13">
                                            @if($product->is_prepayment)
                                                {{ $product->preorder->where('request_preorder_status',2)->whereIn('prepayment_confirm_status',[0,1])->count() }}
                                            @else
                                                {{ $product->preorder->where('request_preorder_status',2)->whereIn('final_order_status',[0, 1])->count() }}
                                            @endif
                                        </span>
                                    </div>
                                    <div>
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Final Order') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700 fs-13">{{ $product->preorder->where('final_order_status', 2)->count() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Publish') }}</span>
                                        <label class="aiz-switch aiz-switch-success mb-0 mt-2">
                                            <input onchange="update_published(this)" 
                                                value="{{ $product->id }}"
                                                type="checkbox" 
                                                @if ($product->is_published == 1) checked @endif
                                                @if(!auth()->user()->can('update_preorder_product_status')) disabled @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2 fs-12">{{ translate('Featured') }}</span>
                                        <label class="aiz-switch aiz-switch-success mb-0 mt-2">
                                            <input onchange="update_featured(this)" 
                                                value="{{ $product->id }}"
                                                type="checkbox" 
                                                @if($product->is_featured == 1) checked @endif
                                                @if(!auth()->user()->can('update_preorder_product_status')) disabled @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>
                                @if($type == 'seller' && get_setting('product_approve_by_admin'))
                                    <td>
                                        <label class="aiz-switch aiz-switch-success mb-0 mt-2">
                                            <input onchange="update_approval(this)" 
                                                value="{{ $product->id }}"
                                                type="checkbox" 
                                                @if ($product->is_approved == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                @endif
                                <td class="text-right">
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{ route('preorder-product.details', $product->product_slug) }}"
                                        target="_blank" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>
                                    @can('edit_preorder_product')
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            href="{{ route('preorder-product.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')]) }}"
                                            title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete_preorder_product')
                                    @if($product->preorder->count() == 0)
                                        <a href="#"
                                            class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('preorder-product.destroy', $product->id) }}"
                                            title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endif    
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $products->appends(request()->input())->links() }}
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

        function update_published(el) {

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('preorder-product.published') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Published product updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_approval(el) {

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('preorder-product.approval') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Product approval updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        update_approval

        function update_featured(el) {

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('preorder-product.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Featured product updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_show_on_homepage(el) {

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('preorder-product.show_on_homepage') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success',
                        '{{ translate('Show on homepage product updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el) {
            $('#sort_products').submit();
        }

        function sort_product_by_user(userType) {
            $('#user_type').val(userType);
            sort_products();
        }

        function bulk_delete() {
            let productIds = [];
            $(".check-one[name='ids[]']:checked").each(function() {
                productIds.push($(this).val());
            });
            $.post('{{ route('preorder-product.bulk-destroy') }}', {
                _token: '{{ csrf_token() }}',
                product_ids: productIds
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Products deleted successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
                location.reload();
            });
        }

    </script>
@endsection
