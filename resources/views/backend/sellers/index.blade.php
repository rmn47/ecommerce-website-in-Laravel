@extends('backend.layouts.app')

@section('content')

@php
    $route = Route::currentRouteName() == 'sellers.index' ? 'all_seller_route' : 'seller_rating_followers';
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ $route == 'all_seller_route' ? translate('All Sellers') : translate('Sellers Review & Followers ')}}</h1>
        </div>
        @if(auth()->user()->can('add_seller') && ($route == 'all_seller_route'))
            <div class="col text-right">
                <a href="{{ route('sellers.create') }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Seller')}}</span>
                </a>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ $route == 'all_seller_route' ? translate('Sellers') : translate('Sellers Review & Followers ') }}</h5>
            </div>
            @if($route == 'all_seller_route')
                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @can('delete_seller')
                            <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                        @endcan
                        @can('seller_commission_configuration')
                            <a class="dropdown-item confirm-alert" onclick="set_bulk_commission()">{{translate('Set Bulk Commission')}}</a>
                        @endcan
                    </div>
                </div>
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="verification_status" onchange="sort_sellers()" data-selected="{{ $verification_status }}">
                        <option value="">{{ translate('Filter by Verification Status') }}</option>
                        <option value="verified">{{ translate('Verified') }}</option>
                        <option value="un_verified">{{ translate('Unverified') }}</option>
                    </select>
                </div>
                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="approved_status" id="approved_status" onchange="sort_sellers()">
                        <option value="">{{translate('Filter by Approval')}}</option>
                        <option value="1"  @isset($approved) @if($approved == '1') selected @endif @endisset>{{translate('Approved')}}</option>
                        <option value="0"  @isset($approved) @if($approved == '0') selected @endif @endisset>{{translate('Non-Approved')}}</option>
                    </select>
                </div>
            @endif
            <div class="col-md-3">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name or email & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>
                        @if(auth()->user()->can('delete_seller') && ($route == 'all_seller_route'))
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        @else
                            #
                        @endif
                    </th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Phone')}}</th>
                    <th data-breakpoints="lg">{{translate('Email Address')}}</th>
                    <th data-breakpoints="lg">{{translate('GST NO')}}</th>
                    <th data-breakpoints="lg">{{translate('Licence NO')}}</th>
                    <th data-breakpoints="lg">{{translate('Pan NO')}}</th>
                    @if($route == 'all_seller_route')
                        <!--<th data-breakpoints="lg">{{translate('Verification Info')}}</th>-->
                        <th data-breakpoints="lg">{{translate('Approval')}}</th>
                        <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                        <!--<th data-breakpoints="lg">{{ translate('Due to seller') }}</th>-->
                        @if(get_setting('seller_commission_type') == 'seller_based')
                            <th data-breakpoints="lg">{{ translate('Commission') }}</th>
                        @endif
                        <th data-breakpoints="lg">{{translate('Email Verification')}}</th>
                        <th data-breakpoints="lg">{{ translate('Status') }}</th>
                    @else
                        <th data-breakpoints="lg">{{translate('Rating')}}</th>
                        <th data-breakpoints="lg">{{translate('Followers')}}</th>
                        <th data-breakpoints="lg">{{ translate('Custom Followers') }}</th>
                    @endif
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($shops as $key => $shop)
                    <tr>
                        <td>
                            @if(auth()->user()->can('delete_seller') && ($route == 'all_seller_route'))
                                <div class="form-group">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" class="check-one" name="id[]" value="{{$shop->id}}">
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                            @else
                                {{ ($key+1) + ($shops->currentPage() - 1)*$shops->perPage() }}
                            @endif
                        </td>
                        <td>
                            <div class="row gutters-5  mw-100 align-items-center">
                                <div class="col-auto">
                                    <img src="{{ uploaded_asset($shop->logo) }}" class="size-40px img-fit" alt="Image" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </div>
                                <div class="col">
                                    <span class="text-truncate-2">{{ $shop->name }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{$shop->user->phone}}</td>
                        <td>{{$shop->user->email}}</td>
                        <td>{{ $shop->user->gst_no }}</td>
                        <td>{{ $shop->user->drug_license_no }}</td>
                        <td>{{ $shop->user->pan_card }}</td>
                        @if($route == 'all_seller_route')
                            <!--<td>-->
                            <!--    @if ($shop->verification_status != 1 && $shop->verification_info != null)-->
                            <!--        <a href="{{ route('sellers.show_verification_request', $shop->id) }}">-->
                            <!--            <span class="badge badge-inline badge-info">{{translate('Show')}}</span>-->
                            <!--        </a>-->
                            <!--    @endif-->
                            <!--</td>-->
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input
                                        @can('approve_seller') onchange="update_approved(this)" @endcan
                                        value="{{ $shop->id }}" type="checkbox"
                                        <?php if($shop->verification_status == 1) echo "checked";?>
                                        @cannot('approve_seller') disabled @endcan
                                    >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>{{ $shop->user->products->count() }}</td>
                            <!--<td>-->
                            <!--    @if ($shop->admin_to_pay >= 0)-->
                            <!--        {{ single_price($shop->admin_to_pay) }}-->
                            <!--    @else-->
                            <!--        {{ single_price(abs($shop->admin_to_pay)) }} ({{ translate('Due to Admin') }})-->
                            <!--    @endif-->
                            <!--</td>-->
                            @if(get_setting('seller_commission_type') == 'seller_based')
                                <td>{{ $shop->commission_percentage }}%</td>
                            @endif
                            <td>
                                @if($shop->user->email_verified_at != null)
                                    <span class="badge badge-inline badge-success">{{translate('Verified')}}</span>
                                @else
                                    <span class="badge badge-inline badge-warning">{{translate('Unverified')}}</span>
                                @endif
                            </td>
                            <td>
                                @if($shop->user->banned)
                                    <span class="badge badge-inline badge-danger">{{ translate('Ban') }}</span>
                                @else
                                    <span class="badge badge-inline badge-success">{{ translate('Regular') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-circle btn-soft-primary btn-icon dropdown-toggle no-arrow" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                        <i class="las la-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                        @can('view_seller_profile')
                                            <a href="javascript:void();" onclick="show_seller_profile('{{$shop->id}}');"  class="dropdown-item">
                                                {{translate('Profile')}}
                                            </a>
                                        @endcan
                                        @can('login_as_seller')
                                            <a href="{{route('sellers.login', encrypt($shop->id))}}" class="dropdown-item">
                                                {{translate('Log in as this Seller')}}
                                            </a>
                                        @endcan
                                        @can('pay_to_seller')
                                            <a href="javascript:void();" onclick="show_seller_payment_modal('{{$shop->id}}');" class="dropdown-item">
                                                {{translate('Go to Payment')}}
                                            </a>
                                        @endcan
                                        @can('seller_payment_history')
                                            <a href="{{route('sellers.payment_history', encrypt($shop->user_id))}}" class="dropdown-item">
                                                {{translate('Payment History')}}
                                            </a>
                                        @endcan
                                        @can('seller_commission_configuration')
                                            <a href="javascript:void();" onclick="set_commission('{{ $shop->id }}');" class="dropdown-item">
                                                {{translate('Set Commission')}}
                                            </a>
                                        @endcan
                                        @can('edit_seller')
                                            <a href="{{route('sellers.edit', encrypt($shop->id))}}" class="dropdown-item">
                                                {{translate('Edit')}}
                                            </a>
                                        @endcan
                                        @can('ban_seller')
                                            @if($shop->user->banned != 1)
                                                <a href="javascript:void();" onclick="confirm_ban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                                    {{translate('Ban this seller')}}
                                                    <i class="fa fa-ban text-danger" aria-hidden="true"></i>
                                                </a>
                                            @else
                                                <a href="javascript:void();" onclick="confirm_unban('{{route('sellers.ban', $shop->id)}}');" class="dropdown-item">
                                                    {{translate('Unban this seller')}}
                                                    <i class="fa fa-check text-success" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        @endcan
                                        @can('delete_seller')
                                            <a href="javascript:void();" class="dropdown-item confirm-delete" data-href="{{route('sellers.destroy', $shop->id)}}" class="">
                                                {{translate('Delete')}}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </td>
                        @else
                            <td>
                                {{ $shop->rating }}
                                <span class="rating rating-sm m-0 ml-1">
                                    @for ($i=0; $i < $shop->rating; $i++)
                                        <i class="las la-star active"></i>
                                    @endfor
                                    @for ($i=0; $i < 5-$shop->rating; $i++)
                                        <i class="las la-star"></i>
                                    @endfor
                                </span>
                            </td>
                            <td>{{ $shop->followers()->count() }}</td>
                            <td>{{ $shop->custom_followers }}</td>
                            <td>
                                @if(auth()->user()->can('edit_seller_custom_followers'))
                                    <a href="javascript:void();" onclick="editCustomFollowers({{ $shop->id }}, {{ $shop->custom_followers }});" class="btn btn-primary btn-xs fs-10 fw-700">
                                        {{translate('Edit Custom Follower')}}
                                    </a>
                                @endif
                            </td>
                        @endif
                        
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
              {{ $shops->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
	<!-- Delete Modal -->
	@include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')

	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>

	<!-- Seller Payment Modal -->
	<div class="modal fade" id="payment_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="payment-modal-content">

	        </div>
	    </div>
	</div>

	<!-- Ban Seller Modal -->
	<div class="modal fade" id="confirm-ban">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
					<button type="button" class="close" data-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
                    <p>{{translate('Do you really want to ban this seller?')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
					<a class="btn btn-primary" id="confirmation">{{translate('Proceed!')}}</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Unban Seller Modal -->
	<div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                        <p>{{translate('Do you really want to unban this seller?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a class="btn btn-primary" id="confirmationunban">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Set Selelr Commission --}}
    <div class="modal fade" id="set_seller_commission">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Set Seller Commission')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <form class="form-horizontal" action="{{ route('set_seller_based_commission') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="seller_ids" value="" id="seller_ids">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Selle Commission')}}</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" lang="en" min="0" max="100" step="0.01" placeholder="{{translate('Commission Percentage')}}" name="commission_percentage" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm text-white">{{translate('save!')}}</button>
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Seller Custom Followers --}}
    <div class="modal fade" id="edit_seller_custom_followers">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Edit Seller Custom Followers')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <form class="form-horizontal" action="{{ route('edit_Seller_custom_followers') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="shop_id" value="" id="shop_id">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Custom Followers')}}</label>
                            <div class="col-md-9">
                                <input type="number" lang="en" min="0" step="1" placeholder="{{translate('Custom Followers')}}" value="" name="custom_followers" id="custom_followers" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm text-white">{{translate('save!')}}</button>
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
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

        function show_seller_payment_modal(id){
            $.post('{{ route('sellers.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#payment_modal #payment-modal-content').html(data);
                $('#payment_modal').modal('show', {backdrop: 'static'});
                $('.demo-select2-placeholder').select2();
            });
        }

        function show_seller_profile(id){
            $.post('{{ route('sellers.profile_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#profile_modal #profile-modal-content').html(data);
                $('#profile_modal').modal('show', {backdrop: 'static'});
            });
        }

        function update_approved(el){
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('sellers.approved') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Approved sellers updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_sellers(el){
            $('#sort_sellers').submit();
        }

        function confirm_ban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }

        function confirm_unban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_sellers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-seller-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        // Set Commission
        function set_commission(shop_id){
            var sellerIds = [];
            sellerIds.push(shop_id);
            $('#seller_ids').val(sellerIds);
            $('#set_seller_commission').modal('show', {backdrop: 'static'});
        }

        // Set seller bulk commission
        function set_bulk_commission(){
            var sellerIds = [];
            $(".check-one[name='id[]']:checked").each(function() {
                sellerIds.push($(this).val());
            });
            if(sellerIds.length > 0){
                $('#seller_ids').val(sellerIds);
                $('#set_seller_commission').modal('show', {backdrop: 'static'});
            }
            else{
                AIZ.plugins.notify('danger', '{{ translate('Please Select Seller first.') }}');
            }
        }

        
        // Edit seller custom followers
        function editCustomFollowers(shop_id, custom_followers){
            $('#shop_id').val(shop_id);
            $('#custom_followers').val(custom_followers);
            $('#edit_seller_custom_followers').modal('show', {backdrop: 'static'});
        }

    </script>
@endsection
