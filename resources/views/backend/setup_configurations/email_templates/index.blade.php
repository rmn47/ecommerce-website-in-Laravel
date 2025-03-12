@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('Email Templates') }}</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ ucfirst($emailReceiver).' '.translate('Email Templates') }}</h5>
            </div>
            <div class="col-md-4">
                <form class="" id="sort_email_templates" action="" method="GET">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm h-100"
                            name="email_template_sort_search"
                            @isset($email_template_sort_search) value="{{ $email_template_sort_search }}" @endisset
                            placeholder="{{ translate('Type & Enter') }}">
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('Email Type') }}</th>
                            <th>{{ translate('Subject') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th class="text-right">{{ translate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emailTemplates as $key => $emailTemplate)
                            <tr>
                                <td>{{ ($key+1) + ($emailTemplates->currentPage() - 1)*$emailTemplates->perPage() }}</td>
                                <td>{{ translate($emailTemplate->email_type) }}</td>
                                <td>
                                    {{ $emailTemplate->subject }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_status(this)" 
                                            value="{{ $emailTemplate->id }}"
                                            type="checkbox" 
                                            @if($emailTemplate->status == 1) checked @endif
                                            @if($emailTemplate->is_status_changeable == 0) disabled @endif>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{ route('email-templates.edit', $emailTemplate->id) }}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $emailTemplates->appends(request()->input())->links() }}
                </div>
            </div>

        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        function sort_email_templates(value) {
            $('input[name="email_user_type"]').val(value);
            $('#sort_email_templates').submit();
        }

        function update_status(el) {
            var status = el.checked ? 1 : 0;
            $.post('{{ route('email-template.update-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success',
                        '{{ translate('Email Template status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });
    </script>
@endsection
