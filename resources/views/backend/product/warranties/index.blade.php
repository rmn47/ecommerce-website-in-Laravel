@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
		<h1 class="h3">{{translate('All Warranties')}}</h1>
	</div>
</div>

<div class="row">
	<div class="@if(auth()->user()->can('add_product_warranty')) col-lg-7 @else col-lg-12 @endif">
		<div class="card">
		    <div class="card-header row gutters-5">
				<div class="col text-center text-md-left">
					<h5 class="mb-md-0 h6">{{ translate('Warranties') }}</h5>
				</div>
				<div class="col-md-4">
					<form class="" id="sort_brands" action="" method="GET">
						<div class="input-group input-group-sm">
					  		<input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
						</div>
					</form>
				</div>
		    </div>
		    <div class="card-body">
		        <table class="table aiz-table mb-0">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>{{translate('Warranty Text')}}</th>
		                    <th>{{translate('Logo')}}</th>
		                    <th class="text-right">{{translate('Options')}}</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($warranties as $key => $warranty)
		                    <tr>
		                        <td>{{ ($key+1) + ($warranties->currentPage() - 1)*$warranties->perPage() }}</td>
		                        <td>{{ $warranty->getTranslation('text') }}</td>
								<td>
		                            <img src="{{ uploaded_asset($warranty->logo) }}" alt="{{translate('Warranty Logo')}}" class="h-50px">
		                        </td>
		                        <td class="text-right">
									@can('edit_product_warranty')
										<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('warranties.edit', ['id'=>$warranty->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
											<i class="las la-edit"></i>
										</a>
									@endcan
									@can('delete_product_warranty')
										<a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('warranties.destroy', $warranty->id)}}" title="{{ translate('Delete') }}">
											<i class="las la-trash"></i>
										</a>
									@endcan
		                        </td>
		                    </tr>
		                @endforeach
		            </tbody>
		        </table>
		        <div class="aiz-pagination">
                	{{ $warranties->appends(request()->input())->links() }}
            	</div>
		    </div>
		</div>
	</div>
	@can('add_product_warranty')
		<div class="col-md-5">
			<div class="card">
				<div class="card-header">
					<h5 class="mb-0 h6">{{ translate('Add New Warranty') }}</h5>
				</div>
				<div class="card-body">
					<form action="{{ route('warranties.store') }}" method="POST">
						@csrf
						<div class="form-group mb-3">
							<label for="warranty_text">{{translate('Warranty Text')}}</label>
							<input type="text" name="warranty_text" class="form-control"  placeholder="{{translate('Warranty Text')}}" id="warranty_text" required>
						</div>
						<div class="form-group mb-3">
							<label for="name">{{translate('Logo')}} <small>({{ translate('40x40') }})</small></label>
							<div class="input-group" data-toggle="aizuploader" data-type="image">
								<div class="input-group-prepend">
										<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
								</div>
								<div class="form-control file-amount">{{ translate('Choose File') }}</div>
								<input type="hidden" name="logo" class="selected-files">
							</div>
							<div class="file-preview box sm">
							</div>
                            <small class="text-muted">{{ translate('Minimum dimensions required: 40px width X 40px height.') }}</small>
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
    @include('modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_brands(el){
        $('#sort_brands').submit();
    }
</script>
@endsection
