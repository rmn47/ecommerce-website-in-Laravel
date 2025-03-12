@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">
            {{ isset($wholeseller) ? translate('All Wholesellers') : translate('All Customers') }}
        </h1>        
    </div>
    @can('add_customer')
        <div class="col text-right">
            @if(isset($wholeseller))
                <a href="{{ route('wholesellers.create') }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Wholeseller') }}</span>
                </a>
            @else
                <a href="{{ route('customers.create') }}" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Customer') }}</span>
                </a>
            @endif
        </div>
    @endcan
</div>

<p>
    <span class="bg-danger d-inline-block h-10px rounded-2 w-10px" ></span> {{ translate('This color indicates that the customer is marked as blocked.') }}
    <br>
    <span class="bg-info d-inline-block h-10px rounded-2 w-10px"></span> {{ translate('This color indicates that the customer is marked as suspicious.') }}
</p>

<div class="card">
    <form class="" id="sort_customers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">
                    {{ isset($wholeseller) ? translate('Wholesellers') : translate('Customers') }}
                </h5>
            </div>

            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                </div>
            </div>
            <div class="col-lg-2 ml-auto">
                <select class="form-control aiz-selectpicker" name="verification_status" onchange="sort_customers()" data-selected="{{ $verification_status }}">
                    <option value="">{{ translate('Filter by Verification Status') }}</option>
                    <option value="verified">{{ translate('Verified') }}</option>
                    <option value="un_verified">{{ translate('Unverified') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type email or name & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
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
                        <th>{{translate('Name')}}</th>
                        <th data-breakpoints="lg">{{translate('Email Address')}}</th>
                        <th data-breakpoints="lg">{{translate('Phone')}}</th>
                        @if(isset($wholeseller))
                            <th data-breakpoints="lg">{{translate('GST Number')}}</th>
                            <th data-breakpoints="lg">{{translate('Drug License Number')}}</th>
                        @endif
                        <th data-breakpoints="lg">{{translate('Address')}}</th>
                        <th data-breakpoints="lg">{{translate('Wallet Balance')}}</th>
                        <th data-breakpoints="lg">{{translate('Verification Status')}}</th>
                        <th class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $key => $user)
                        @if ($user != null)
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]" value="{{$user->id}}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="@if($user->banned == 1) text-danger @elseif($user->is_suspicious == 1) text-info @endif">
                                        @if($user->banned == 1) 
                                            <i class="las la-ban las" aria-hidden="true"></i>
                                        @elseif($user->is_suspicious == 1) 
                                            <i class="las la-exclamation-circle" aria-hidden="true"></i> 
                                        @endif 
                                        {{$user->name}}
                                    </p>
                                </td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone}}</td>
                                @if(isset($wholeseller))
                                    <td>{{ $user->gst_no ?? 'N/A' }}</td>
                                    <td>{{ $user->drug_license_no ?? 'N/A' }}</td>
                                @endif
                                <td>
                                    {{$user->address}}
                                </td>
                                <td>{{single_price($user->balance)}}</td>
                                <td>
                                    @if($user->email_verified_at != null)
                                        <span class="badge badge-inline badge-success">{{translate('Verified')}}</span>
                                    @else
                                        <span class="badge badge-inline badge-warning">{{translate('Unverified')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <!--@can('edit_customer')-->
                                    <!--    <a href="{{ isset($wholeseller) ? route('wholesellers.edit', encrypt($user->id)) : route('customers.edit', encrypt($user->id)) }}" -->
                                    <!--       class="btn btn-soft-primary btn-icon btn-circle btn-sm" -->
                                    <!--       title="{{ isset($wholeseller) ? translate('Edit this Wholeseller') : translate('Edit this Customer') }}">-->
                                    <!--        <i class="las la-edit"></i>-->
                                    <!--    </a>-->
                                    <!--@endcan-->
                                    
                                    <a href="{{ isset($wholeseller) ? route('wholesellers.edit', encrypt($user->id)) : route('customers.edit', encrypt($user->id)) }}" 
                                       class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
                                       title="{{ isset($wholeseller) ? translate('Edit this Wholeseller') : translate('Edit this Customer') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    
                                    @can('login_as_customer')
                                        @if($user->email_verified_at != null)
                                            <a href="{{ isset($wholeseller) ? route('wholesellers.login', encrypt($user->id)) : route('customers.login', encrypt($user->id)) }}" 
                                               class="btn btn-soft-primary btn-icon btn-circle btn-sm" 
                                               title="{{ isset($wholeseller) ? translate('Log in as this Wholeseller') : translate('Log in as this Customer') }}">
                                                <i class="las la-sign-in-alt"></i>
                                            </a>
                                        @endif
                                    @endcan
                                
                                    @can('ban_customer')
                                        @if($user->banned != 1)
                                            <a href="#" 
                                               class="btn btn-soft-danger btn-icon btn-circle btn-sm" 
                                               onclick="confirm_ban('{{ isset($wholeseller) ? route('wholesellers.ban', encrypt($user->id)) : route('customers.ban', encrypt($user->id)) }}');" 
                                               title="{{ isset($wholeseller) ? translate('Ban this Wholeseller') : translate('Ban this Customer') }}">
                                                <i class="las la-user-slash"></i>
                                            </a>
                                        @else
                                            <a href="#" 
                                               class="btn btn-soft-success btn-icon btn-circle btn-sm" 
                                               onclick="confirm_unban('{{ isset($wholeseller) ? route('wholesellers.ban', encrypt($user->id)) : route('customers.ban', encrypt($user->id)) }}');" 
                                               title="{{ isset($wholeseller) ? translate('Unban this Wholeseller') : translate('Unban this Customer') }}">
                                                <i class="las la-user-check"></i>
                                            </a>
                                        @endif
                                    @endcan
                                
                                    @can('delete_customer')
                                        <a href="#" 
                                           class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" 
                                           data-href="{{ isset($wholeseller) ? route('wholesellers.destroy', $user->id) : route('customers.destroy', $user->id) }}" 
                                           title="{{ isset($wholeseller) ? translate('Delete Wholeseller') : translate('Delete Customer') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>                                
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $users->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="confirm-ban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{translate('Do you really want to ban this Customer?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm-unban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{translate('Do you really want to unban this Customer?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="confirmationunban" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
    @include('modals.bulk_delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }
        });

        function sort_customers() {
            $('#sort_customers').submit();
        }

        function confirm_ban(url) {
            if('{{env('DEMO_MODE')}}' == 'On') {
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href', url);
        }

        function confirm_unban(url) {
            if('{{env('DEMO_MODE')}}' == 'On') {
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href', url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_customers')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-customer-delete')}}",
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
    </script>
@endsection