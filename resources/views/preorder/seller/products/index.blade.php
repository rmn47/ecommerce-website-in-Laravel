@extends('seller.layouts.app')
@section('panel_content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{ translate('Preorder Products') }}</h1>
            </div>
        </div>
    </div>

    <div class="row gutters-10 justify-content-center">
        @if (addon_is_activated('seller_subscription'))
            <div class="col-md-4 mx-auto mb-3" >
                <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                  <span class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                      <i class="las la-upload la-2x text-white"></i>
                  </span>
                  <div class="px-3 pt-3 pb-3">
                      <div class="h4 fw-700 text-center">{{ max(0, auth()->user()->shop->preorder_product_upload_limit - auth()->user()->preorderProducts()->count()) }}</div>
                      <div class="opacity-50 text-center">{{  translate('Remaining Uploads') }}</div>
                  </div>
                </div>
            </div>
        @endif

        <div class="col-md-4 mx-auto mb-3" >
            <a href="{{ route('seller.preorder-product.create')}}">
              <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition">
                  <span class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                      <i class="las la-plus la-3x text-white"></i>
                  </span>
                  <div class="fs-18 text-primary">{{ translate('Add New Product') }}</div>
              </div>
            </a>
        </div>

        @if (addon_is_activated('seller_subscription'))
            @php
                $seller_package = \App\Models\SellerPackage::find(Auth::user()->shop->seller_package_id);
            @endphp
            <div class="col-md-4">
                <a href="{{ route('seller.seller_packages_list') }}" class="text-center bg-white shadow-sm hov-shadow-lg text-center d-block p-3 rounded">
                    @if($seller_package != null)
                        <img src="{{ uploaded_asset($seller_package->logo) }}" height="44" class="mw-100 mx-auto">
                        <span class="d-block sub-title mb-2">{{ translate('Current Package')}}: {{ $seller_package->getTranslation('name') }}</span>
                    @else
                        <i class="la la-frown-o mb-2 la-3x"></i>
                        <div class="d-block sub-title mb-2">{{ translate('No Package Found')}}</div>
                    @endif
                    <div class="btn btn-outline-primary py-1">{{ translate('Upgrade Package')}}</div>
                </a>
            </div>
        @endif
    </div>

    <div class="card">
        <form class="" id="sort_products" action="" method="GET">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-md-0 h6">{{ translate('All Preorder Products') }}</h5>
                    </div>

                    <div class="col-8 mt-4">
                        <div class="badges">
                            <span class="badge badge-inline preorder-badge-border-dashed p-3 my-2 mr-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Total Products') }}({{ $allProducts }})</span>
                            <span class="badge badge-inline preorder-badge-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Published') }}({{ $publishedProductCount }})</span>
                            <span class="badge badge-inline preorder-badge-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Unpublished') }}({{ $unpublishedProductCount }})</span>
                            <span class="badge badge-inline preorder-badge-border-dashed p-3 m-2 rounded-3 text-muted fs-12 fw-600">{{ translate('Discounted') }}({{ $discountedProductCount }})</span>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="row ml-0">
                            <div class="dropdown mb-2 mb-md-0">
                                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Bulk Action')}}
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal"> {{translate('Delete selection')}}</a>
                                </div>
                            </div>
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
                            <th>{{ translate('Image') }}</th>
                            <th data-breakpoints="md" width="15%">{{ translate('Product details') }}</th>
                            <th data-breakpoints="sm">{{ translate('Product details') }}</th>
                            <th data-breakpoints="lg">{{ translate('Price settings') }}</th>
                            <th data-breakpoints="lg">{{ translate('Discount Settings') }}</th>
                            <th data-breakpoints="lg">{{ translate('Availability') }}</th>
                            <th data-breakpoints="lg">{{ translate('Orders') }}</th>
                            <th data-breakpoints="lg">{{ translate('Status') }}</th>
                            @if(get_setting('product_approve_by_admin'))
                                <th data-breakpoints="lg">{{ translate('Approval') }}</th>
                            @endif
                            <th data-breakpoints="sm" class="text-right">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key => $product)
                            <tr>
                                <td>
                                    <div class="form-group d-inline-block">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="ids[]" value="{{ $product->id }}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="row gutters-5 ">
                                        <div class="col-auto">
                                            <img src="{{ uploaded_asset($product->thumbnail) }}" alt="Image" class="size-50px img-fit">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="mb-2">
                                        <span class="text-muted text-truncate-3 break-word ">{{ Str::limit($product?->getTranslation('product_name'), 50, ' ...') }}</span>
                                    </div>

                                    <div class="mb-2">
                                        <span class="opacity-60 text-muted text-truncate-2">{{ translate('Category') }}</span>
                                        <span class="text-muted text-truncate-2 fw-700">{{ $product->category?->name }}</span>
                                    </div>
                                    <div>
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
                                            <span class="opacity-60 text-blue text-truncate-2 fs-12">
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
                                                <span class="text-muted text-truncate-2 fw-700 fs-13 text-secondary">{{ translate('Approx') }}</span>
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
                                                @if ($product->is_published == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>
                                @if(get_setting('product_approve_by_admin'))
                                    <td>
                                        @if($product->is_approved == 1)
                                        <span class="badge badge-inline badge-success m-2 p-2 rounded-3">{{ translate('Yes')}}</span>
                                        @else
                                        <span class="badge badge-inline badge-danger p-2 m-2 rounded-3">{{ translate('No')}}</span>
                                        @endif
                                    </td>
                                @endif

                                <td class="text-right">
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm" href="{{ route('preorder-product.details', $product->product_slug) }}"
                                        target="_blank" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>

                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('seller.preorder-product.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')]) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>

                                    @if($product->preorder->count() == 0)
                                    <a href="#"
                                        class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('seller.preorder-product.destroy', $product->id) }}"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                    @endif

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
            $.post('{{ route('seller.preorder-product.published') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Published product updated successfully') }}');
                }
                else if(data == 2){
                    AIZ.plugins.notify('danger', '{{ translate('Please upgrade your package.') }}');
                } 
                else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
                location.reload();
            });
        }

        function update_featured(el) {

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('seller.preorder-product.featured') }}', {
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

        function sort_products(el) {
            $('#sort_products').submit();
        }

        function bulk_delete() {
            let productIds = [];
            $(".check-one[name='ids[]']:checked").each(function() {
                productIds.push($(this).val());
            });
            $.post('{{ route('seller.preorder-product.bulk-destroy') }}', {
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
