@extends('backend.layouts.app')
@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate("All FAQ's")}}</h1>
        </div>
    </div>
</div>
<br>

<div class="row">
    <div class="@if(auth()->user()->can('add_faq')) col-lg-7 @else col-lg-12 @endif">
        <div class="card">
            <form class="" id="sort_faqs" action="" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col">
                        <h5 class="mb-md-0 h6">{{ translate('All FAQ') }}</h5>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <input type="text" class="form-control form-control-sm" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type & Enter') }}">
                        </div>
                    </div>
                </div>
        
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th data-breakpoints="lg">#</th>
                                <th>{{translate('Question')}}</th>
                                <th>{{translate('Status')}}</th>
                                <th data-breakpoints="sm" class="text-right">{{translate('Options')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($faqs as $key => $faq)
                            <tr>
                                <td>{{ ($key+1) + ($faqs->currentPage() - 1)*$faqs->perPage() }}</td>
                                <td>{{ $faq->getTranslation('question')}}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_status(this)" 
                                            value="{{ $faq->id }}" type="checkbox" 
                                            @if ($faq->status == 1) checked @endif 
                                            @if(!auth()->user()->can('update_faq_status')) disabled @endif
                                            >
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    @can('edit_faq')
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('faq.edit', ['id'=>$faq->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete_faq')
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('faq.destroy', $faq->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $faqs->appends(request()->input())->links() }}
                    </div>
                </div>
            </form>
        </div>
    </div>
    @can('add_faq')
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Add New FAQ') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('faqs.store') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" for="question">{{translate('Question')}}</label>
                            <input type="text" placeholder="{{translate('Question')}}" name="question" id="question" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="name" for="answer">{{translate('Answer')}}</label>
                            <textarea class="form-control" name="answer" placeholder="{{ translate('Answer') }}" id="answer" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-group mb-3 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
</div>


@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function sort_faqs(el){
            $('#sort_faqs').submit();
        }

        function update_status(el){
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
            $.post('{{ route('faq.update-status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('FAQ status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
