@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Notification Types') }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="d-sm-flex justify-content-between mt-4 mx-4">
            <div>
                <p class="fs-13 fw-700 mb-0">{{ translate('Notification Types') }}</p>
            </div>
        </div>

        <form class="" id="sort_notification_types" action="" method="GET">
            <input type="hidden" name="notification_user_type" value="{{ $notificationUserType }}">
            <div class="d-sm-flex justify-content-between mx-4">
                <div class="mt-3">
                    @php
                        $activeClasss = 'btn-soft-blue';
                        $inActiveClasses = 'text-secondary border-dashed border-soft-light';
                    @endphp
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'customer' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notification_types('customer')">
                        {{ translate('Customer') }}
                    </a>
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'seller' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notification_types('seller')">
                        {{ translate('Seller') }}
                    </a>
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'admin' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notification_types('admin')">
                        {{ translate('Admin') }}
                    </a>
                </div>
                <div class="d-flex mt-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm h-100"
                            name="notification_type_sort_search"
                            @isset($notification_type_sort_search) value="{{ $notification_type_sort_search }}" @endisset
                            placeholder="{{ translate('Type & Enter') }}">
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>{{ translate('Image') }}</th>
                            <th width="25%">{{ translate('Type') }}</th>
                            <th width="40%">{{ translate('DEfault Text') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th class="text-right">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notificationTypes as $key => $notificationType)
                            <tr>
                                <td>{{ ($key+1) + ($notificationTypes->currentPage() - 1)*$notificationTypes->perPage() }}</td>
                                <td>
                                    <img src="{{ uploaded_asset($notificationType->image) }}"
                                        alt="{{ translate('Image') }}" class="h-30px">
                                </td>
                                <td class="fs-12 fw-700">{{ $notificationType->getTranslation('name') }}</td>
                                <td class="fs-11">{{ $notificationType->getTranslation('default_text') }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_status(this)" 
                                            value="{{ $notificationType->id }}"
                                            type="checkbox" 
                                            @if($notificationType->status == 1) checked @endif
                                            @if(!auth()->user()->can('update_preorder_notification_status')) disabled @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    @can('edit_preorder_notification_type')
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            href="{{ route('preorder.notification-type.edit', ['id' => $notificationType->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                            title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $notificationTypes->appends(request()->input())->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        function sort_notification_types(value) {
            $('input[name="notification_user_type"]').val(value);
            $('#sort_notification_types').submit();
        }

        function update_status(el) {
            var status = el.checked ? 1 : 0;
            $.post('{{ route('notification-type.update-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success',
                        '{{ translate('Notification type status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
