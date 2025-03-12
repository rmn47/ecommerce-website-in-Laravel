@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Notes') }}</h1>
            </div>
            @can('add_note')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('note.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Note') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="form-group mb-0 row">
                <label class="col-md-2 col-from-label">{{translate('Seller Can Add Note')}}?</label>
                <div class="col-md-10">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'seller_can_add_note')" @if(get_setting('seller_can_add_note')) checked @endif >
                        <span></span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_notes" action="" method="GET">
            <input type="hidden" name="note_user_type" value="{{ $noteUserType }}">
            <div class="d-sm-flex justify-content-between mx-4">
                <div class="mt-3">
                    @php
                        $activeClasss = 'btn-soft-blue';
                        $inActiveClasses = 'text-secondary border-dashed border-soft-light';
                    @endphp
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $noteUserType == 'all' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notes('all')">
                        {{ translate('All') }}
                    </a>
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $noteUserType == 'in_house' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notes('in_house')">
                        {{ translate('In-House') }}
                    </a>
                    <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $noteUserType == 'seller' ? $activeClasss : $inActiveClasses }}"
                        href="javascript:void(0);" onclick="sort_notes('seller')">
                        {{ translate('Seller') }}
                    </a>
                </div>
                <div class="d-flex mt-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm h-100" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="Type & Enter">
                    </div>
                </div>
            </div>
        </form>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th data-breakpoints="lg">{{ translate('User') }}</th>
                        <th data-breakpoints="lg">{{ translate('Type') }}</th>
                        <th data-breakpoints="lg" width="60%">{{ translate('Description') }}</th>
                        <th data-breakpoints="lg">{{ translate('Seller Can Access') }}?</th>
                        <th width="10%" class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notes as $key => $note)
                        <tr>
                            <td>{{ $key + 1 + ($notes->currentPage() - 1) * $notes->perPage() }}</td>
                            <td>{{ $note->user_id == get_admin()->id ? 
                                        translate('In-House') : 
                                        ($note->user->shop->name ?? $note->user->name) 
                                    }}</td>
                            <td>{{ translate($note->note_type) }}</td>
                            <td><p class="text-truncate-2">{{ $note->getTranslation('description') }}</p></td>
                            <td>
                                @if( $note->user_id == get_admin()->id)
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="updateSellerAccess(this)" value="{{ $note->id }}" type="checkbox" <?php if ($note->seller_access == 1) echo "checked"; ?> >
                                        <span class="slider round"></span>
                                    </label>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="javascript:void(0);" onclick="noteView('{{ route('get-single-note', $note->id )}}')" class="btn btn-soft-success btn-icon btn-circle btn-sm" title="{{ translate('Note Description') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                @can('edit_note')
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{route('note.edit', ['id'=>$note->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan
                                @can('delete_note')
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('note.delete', $note->id) }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $notes->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection


@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade note-view-modal" id="modal-basic">
    	<div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title h6">{{translate('Note Description')}}</h5>
                  <button type="button" class="close" data-dismiss="modal"></button>
              </div>
              <div class="modal-body note-view">
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Close')}}</button>
              </div>
          </div>
    	</div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        function sort_notes(value) {
            $('input[name="note_user_type"]').val(value);
            $('#sort_notes').submit();
        }

        function updateSellerAccess (el){
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            var isCanAccess = el.checked ? 1 : 0;
            $.post('{{ route('note.update-seller-access') }}', {
                _token      :   '{{ csrf_token() }}',
                id          :   el.value,
                status      :   isCanAccess
            }, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Admin note seller access status update successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function updateSettings(el, type) {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            var value = ($(el).is(':checked')) ? 1 : 0;
            $.post('{{ route('business_settings.update.activation') }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }

        function noteView(url){
            $.get(url, function(data){
                $('.note-view').html(data);
                $('.note-view-modal').modal('show');
            });
        }
    </script>
@endsection
