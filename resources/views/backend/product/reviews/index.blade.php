@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('All Rating & Reviews')}}</h1>
        </div>
        @can('add_custom_review')
            <div class="col text-right">
                <a href="{{ route('custom-review.create', null) }}" class="btn btn-sm rounded-2" style="background-color: #299395; color: white;">
                    <span>{{translate('Add Custom Reviews')}}</span>
                </a>
            </div>
        @endcan
    </div>
</div>
<br>

<div class="card">
    <form class="" action="" id="sort_reiewed_products" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{ translate('Product Review & Ratings') }}</h5>
            </div>

            <div class="col-lg-2 ml-auto">
                <select class="form-control form-control-sm aiz-selectpicker" name="seller_id" id="seller_id" onchange="sort_reiewed_products()" data-selected="{{ $sellerID }}" >
                    <option value="all">{{ translate('All') }}</option>
                    <option value="{{ get_admin()->id }}">{{ translate('In House') }}</option>
                    @foreach ($sellers as $seller)
                        <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                    
                </select>
            </div>
            <div class="col-lg-2">
                <select class="form-control form-control-sm aiz-selectpicker" name="rating" id="rating" onchange="sort_reiewed_products()" data-selected="{{ $sortByRating }}">
                    <option value="">{{translate('Filter by Rating')}}</option>
                    <option value="desc">{{translate('Rating (High > Low)')}}</option>
                    <option value="asc">{{translate('Rating (Low > High)')}}</option>
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-group form-group-sm mb-0">
                    <input type="text" class="form-control form-control-sm" id="search"
                        name="search" value="{{ $sortSearch }}"
                        placeholder="{{ translate('Type Product Name & Hit Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th width="40%">{{translate('Product Name')}}</th>
                    <th data-breakpoints="lg">{{translate('Product Owner')}}</th>
                    <th data-breakpoints="lg">{{translate('Rating')}}</th>
                    <th data-breakpoints="lg">{{translate('Reviews')}}</th>
                    <th data-breakpoints="lg">{{translate('Custom Reviews')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                <tr>
                    <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                    <td>
                        <div class="row gutters-5">
                            <div class="col-auto">
                                <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-50px img-fit">
                            </div>
                            <div class="col">
                                <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->user->name}}</td>
                    <td>{{ $product->rating}}</td>
                    <td>
                        {{ $product->reviews->count()}}
                        @if($product->reviews()->where('viewed',0)->count() > 0)
                            <span class="badge badge-inline badge-danger">{{ translate('new') }}</span>
                        @endif
                    </td>
                    <td>{{ $product->reviews->where('type','custom')->count()}}</td>
                    <td>
                        <div class="form-group mb-0 text-right">
                            <a href="{{ route('detail-reviews', $product->id) }}" class="btn btn-primary btn-sm rounded-2">{{ translate('View Reviews') }}</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        
        function sort_reiewed_products(el){
            $('#sort_reiewed_products').submit();
        }
    </script>
@endsection
