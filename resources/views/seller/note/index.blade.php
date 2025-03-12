@extends('seller.layouts.app')
@section('panel_content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Notes') }}</h1>
            </div>
            @if(get_setting('seller_can_add_note'))
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('seller.note.create') }}" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Note') }}</span>
                    </a>
                </div>
            @endcan
        </div>
    </div>
    <div class="card">
        <div class="card-header d-block d-md-flex">
            <h5 class="mb-0 h6">{{ translate('notes') }}</h5>
            <form id="sort_notes" action="" method="GET">
                <div class="box-inline pad-rgt pull-left">
                    <div style="min-width: 200px;">
                        <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th data-breakpoints="lg">{{ translate('User') }}</th>
                        <th data-breakpoints="lg">{{ translate('Type') }}</th>
                        <th data-breakpoints="lg" width="60%">{{ translate('Description') }}</th>
                        <th width="10%" class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notes as $key => $note)
                        @php $user = auth()->user(); @endphp
                        <tr>
                            <td>{{ $key + 1 + ($notes->currentPage() - 1) * $notes->perPage() }}</td>
                            <td>
                                {{ $note->user_id == $user->id ? $user->shop->name : get_setting('site_name') }}
                            </td>
                            <td>{{ translate($note->note_type) }}</td>
                            <td><p class="text-truncate-2">{{ $note->getTranslation('description') }}</p></td>
                            <td class="text-right">
                                <a href="javascript:void(0);" onclick="noteView('{{ route('get-single-note', $note->id )}}')" class="btn btn-soft-success btn-icon btn-circle btn-sm" title="{{ translate('Note Description') }}">
                                    <i class="las la-eye"></i>
                                </a>
                                @if($note->user_id == auth()->id())
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                        href="{{route('seller.note.edit', ['id'=>$note->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('seller.note.delete', $note->id) }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endif
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
            $('#sort_notes').submit();
        }

        function noteView(url){
            $.get(url, function(data){
                $('.note-view').html(data);
                $('.note-view-modal').modal('show');
            });
        }
        
    </script>
@endsection
