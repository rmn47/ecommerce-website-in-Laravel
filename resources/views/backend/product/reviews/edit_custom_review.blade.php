@extends('backend.layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Edit Custom Review')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('custom-review.update') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <input type="hidden" name="id" value="{{ $review->id }}">
                    <div class="form-group">
                        <label for="custom_reviewer_name">{{ translate('Custom Reviewer Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="custom_reviewer_name" value="{{ $review->custom_reviewer_name }}" class="form-control" id="custom_reviewer_name" required>
                    </div>

                    <div class="form-group">
                        <label for="custom_reviewer_image">{{ translate('Custom Reviewer Image')}}</label>
                        <div class="">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="custom_reviewer_image" value="{{ $review->custom_reviewer_image }}" class="selected-files" id="custom_reviewer_image">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                        <small class="text-muted">{{translate("If you do not use custom reviewer's image it will show default user image.")}}</small>
                    </div>

                    <div class="form-group">
                        <label for="product">{{ translate('Category')}}</label>
                        <input type="text" value="{{ $review->product->main_category->getTranslation('name') }}" class="form-control" readonly>                        
                    </div>
                    <div class="form-group">
                        <label for="product">{{ translate('Product')}} <span class="text-danger">*</span></label>
                        <select class="form-control aiz-selectpicker" disabled>
                            <option data-content="<img src='{{ uploaded_asset($review->product->thumbnail_img)}}' class='img-fit size-40px'><span class='fw-600'> {{ $review->product->getTranslation('name') }} </span>"></option>
                        </select>
                    </div>

                    <!-- Rating -->
                    <div class="form-group">
                        <label class="">{{ translate('Rating')}} <span class="text-danger">*</span></label>
                        <div class="rating rating-input">
                            <label>
                                <input type="radio" name="rating" value="1" @if($review->rating == 1) checked @endif required>
                                <i class="las la-star @if($review->rating >= 1) active @endif"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="2" @if($review->rating == 2) checked @endif>
                                <i class="las la-star @if($review->rating >= 2) active @endif"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="3" @if($review->rating == 3) checked @endif>
                                <i class="las la-star @if($review->rating >= 3) active @endif"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="4" @if($review->rating == 4) checked @endif>
                                <i class="las la-star @if($review->rating >= 4) active @endif"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="5" @if($review->rating == 5) checked @endif>
                                <i class="las la-star @if($review->rating >= 5) active @endif"></i>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date -->
                    <div class="form-group">
                        <label for="comment">
                            {{ $review->created_at_is_custom == 0 ? translate('Date') : translate('Custom Date')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="w-100">
                            @if($review->created_at_is_custom == 0)
                                <div class="d-flex mb-4">
                                    <div class="radio mar-btm mr-5 d-flex align-items-center">
                                        <input id="system_date" 
                                            type="radio" 
                                            name="review_date_type" 
                                            value="system_date" 
                                            onchange="reviewDateType()" 
                                            @if($review->created_at_is_custom == 0) checked @endif>
                                        <label for="system_date" class="mb-0 ml-2">{{translate('System Date')}}</label>
                                    </div>
                                
                                    <div class="radio mar-btm mr-3 d-flex align-items-center">
                                        <input id="custom_date" 
                                            type="radio" 
                                            name="review_date_type" 
                                            value="custom" 
                                            onchange="reviewDateType()"
                                            @if($review->created_at_is_custom == 1) checked @endif>
                                        <label for="custom_date" class="mb-0 ml-2">{{translate('Select')}}</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="date-selection-div @if($review->created_at_is_custom == 0) d-none @endif">
                            <input type="text" class="form-control aiz-date-range" name="custom_date" value="{{ $review->created_at }}" placeholder="{{ translate('Select Date') }}" data-single="true" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-show-dropdown="true" autocomplete="off">
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="form-group">
                        <label for="comment">{{ translate('Comment')}} <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" name="comment" id="comment" placeholder="{{ translate('Your review')}}" required>{{ $review->comment }}</textarea>
                    </div>
                    <!-- Review Images -->
                    <div class="form-group">
                        <label class="" for="photos">{{translate('Review Images')}}</label>
                        <div class="">
                            <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="photos[]" value="{{ $review->photos }}" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <small class="text-muted">{{translate('These images are visible in product review page gallery. Upload square images')}}</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
        
        function reviewDateType(){
            var reviewDateType = $("input[name='review_date_type']:checked").val();
            if(reviewDateType == 'system_date'){
                $('.date-selection-div').addClass('d-none');
            }
            else if(reviewDateType == 'custom'){
                $('.date-selection-div').removeClass('d-none');
            }
        }

    </script>
@endsection