@extends('backend.layouts.app')
@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('Detail Reviews')}}</h1>
        </div>
        @can('add_custom_review')
            <div class="col text-right">
                <a href="{{ route('custom-review.create', $product->id) }}" class="btn btn-sm rounded-2" style="background-color: #299395; color: white;">
                    <span>{{translate('Add Custom Reviews')}}</span>
                </a>
            </div>
        @endcan
    </div>
</div>
<br>

<div class="card">
    <div class="d-flex justify-content-between">
        <div class="row gutters-5 w-400px w-md-500px align-items-center ml-1">
            <div class="col-auto">
                <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="size-80px img-fit">
            </div>
            <div class="col">
                <span class="text-muted text-truncate-2">{{ $product->getTranslation('name') }}</span>
            </div>
        </div>
        <div class="text-right m-3">
            <p class="fs-11 fw-300 m-0">{{ strtoupper(translate('Rating')) }}</p>
            <p class="fs-16 fw-900 m-0">{{ $product->rating }}</p>
            <p class="rating rating-sm m-0">
                @for ($i=0; $i < $product->rating; $i++)
                    <i class="las la-star active"></i>
                @endfor
                @for ($i=0; $i < 5-$product->rating; $i++)
                    <i class="las la-star"></i>
                @endfor
            </p>
        </div>
    </div>

    <hr class="mx-4 my-0">

    <div class="d-sm-flex justify-content-between mx-4">
        <div class="mt-3">
            <form class="" id="sort_by_review_types" action="" method="GET">
                <input type="hidden" name="review_type" value="{{ $reviewType }}">
                @php
                    $activeClasss = 'btn-soft-blue';
                    $inActiveClasses = 'text-secondary border-dashed border-soft-light';
                @endphp
                <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $reviewType == 'real' ? $activeClasss : $inActiveClasses}}"
                    href="javascript:void(0);" onclick="sortByReviewType('real')">
                    {{ translate('Reviews').' ' }}({{ $customerReviewCount }})
                </a>
                <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $reviewType == 'custom' ? $activeClasss : $inActiveClasses}}"
                    href="javascript:void(0);" onclick="sortByReviewType('custom')">
                    {{ translate('Custom Reviews').' ' }}({{ $customReviewCount }})
                </a>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr class="opacity-70">
                    <th data-breakpoints="lg">#</th>
                    <th>{{ strtoupper(translate('Customer')) }}</th>
                    <th>{{ strtoupper(translate('Rating')) }}</th>
                    <th data-breakpoints="lg">{{ strtoupper(translate('Comment')) }}</th>
                    <th @if($reviewType == 'real') class="text-right" @endif width="20%">{{ strtoupper(translate('Published')) }}</th>
                    @if($reviewType == 'custom')
                        <th data-breakpoints="lg" class="text-right">{{ strtoupper(translate('Options')) }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $key => $review)
                <tr>
                    <td>{{ ($key+1) + ($reviews->currentPage() - 1)*$reviews->perPage() }}</td> 
                    <td>
                        @php
                            $customerName = null;
                            $customerAvatar = null;
                            if($review->type == "real"){
                                if($review->user != null){
                                    $customerName = $review->user->name;
                                    $customerAvatar = uploaded_asset($review->user->avatar_original);
                                }
                                else {
                                    $customerName = translate('Customer Not Found');
                                }
                            }
                            else{
                                $customerName = $review->custom_reviewer_name;
                                $customerAvatar = uploaded_asset($review->custom_reviewer_image);
                            }
                        @endphp
                        <div class="row gutters-5 w-200px w-md-300px mw-100 align-items-center">
                            <div class="col-auto">
                                <img src="{{ $customerAvatar }}" class="size-50px img-fit rounded-circle" alt="Image" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                            </div>
                            <div class="col">
                                <span class="fw-700 text-truncate-2">{{ $customerName }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="fw-700">{{ $review->rating }}</td>
                    <td>
                        {{ $review->comment }}
                        @if($review->photos != null)
                            <div class="spotlight-group d-flex flex-wrap mt-2">
                                @foreach (explode(',', $review->photos) as $photo)
                                <a href="{{ uploaded_asset($photo) }}" 
                                    class="mr-2 mr-md-3 mb-2 mb-md-3 border overflow-hidden has-transition hov-scale-img hov-border-primary"
                                    target="_blank">
                                    <img class="img-fit h-60px lazyload has-transition"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($photo) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                </a>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="d-flex d-sm-flex @if($reviewType == 'real') justify-content-end @endif align-items-center">
                            <div class="mr-3 opacity-70">
                                {{ date("j F, Y", strtotime($review->created_at)) }}
                            </div>
                            <div>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_published(this)" value="{{ $review->id }}" type="checkbox" <?php if ($review->status == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </td>
                    @if($reviewType == 'custom')
                        <td class="text-right">
                            @can('edit_custom_review')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('custom-review.edit', $review->id)}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                            @endcan
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $reviews->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        function sortByReviewType(value) {
            $('input[name="review_type"]').val(value);
            $('#sort_by_review_types').submit();
        }

        function update_published(el){
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
            
            $.post('{{ route('reviews.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published reviews updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
