@extends('backend.layouts.app')
@section('content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add New Custom Review')}}</h5>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    
                    <div class="form-group">
                        <label for="custom_reviewer_name">{{ translate('Custom Reviewer Name')}} <span class="text-danger">*</span></label>
                        <input type="text" name="custom_reviewer_name" class="form-control" id="custom_reviewer_name" required>
                    </div>

                    <div class="form-group">
                        <label for="custom_reviewer_image">{{ translate('Custom Reviewer Image')}}</label>
                        <div class="">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="custom_reviewer_image" class="selected-files" id="custom_reviewer_image">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                        <small class="text-muted">{{translate("If you do not use custom reviewer's image it will show default user image.")}}</small>
                    </div>

                    <div class="form-group">
                        <label for="product">{{ translate('Category')}}</label>
                        @if($product != null)
                            <input type="text" value="{{ $product->main_category->getTranslation('name') }}" class="form-control" readonly>                        
                        @else
                            <select class="form-control aiz-selectpicker" 
                                data-live-search="true" 
                                data-placeholder="{{ translate('Select Category') }}" 
                                name="category_id">
                                    <option value="">{{ translate('Select Category') }}</option>
                                    @foreach ($categories as $p_category)
                                        <option value="{{ $p_category->id }}">{{ $p_category->getTranslation('name') }}</option>
                                        @foreach ($p_category->childrenCategories as $childCategory)
                                            @include('categories.child_category', ['child_category' => $childCategory])
                                        @endforeach
                                    @endforeach
                            </select>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label for="product">{{ translate('Product')}} <span class="text-danger">*</span></label>
                        <select name="product_id" 
                                id="product_selection" 
                                class="form-control aiz-selectpicker"  
                                data-placeholder="{{ translate('Choose Product') }}" 
                                data-live-search="true"
                                disabled
                                required>
                            @if($product == null)
                                <option value="">{{translate('Please Select Category First') }}</option>
                            @else
                                <option value="{{ $product->id }}"
                                    data-content="<img src='{{ uploaded_asset($product->thumbnail_img)}}' class='img-fit size-40px'><span class='fw-600'> {{ $product->getTranslation('name') }} </span>">
                                </option>
                            @endif
                        </select>
                        <small class="text-muted">{{translate('Select Product for Custom Review')}}</small>
                    </div>
                    @if($product != null)
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @endif

                    <!-- Rating -->
                    <div class="form-group">
                        <label class="">{{ translate('Rating')}} <span class="text-danger">*</span></label>
                        <div class="rating rating-input">
                            <label>
                                <input type="radio" name="rating" value="1" required>
                                <i class="las la-star"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="2">
                                <i class="las la-star"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="3">
                                <i class="las la-star"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="4">
                                <i class="las la-star"></i>
                            </label>
                            <label>
                                <input type="radio" name="rating" value="5">
                                <i class="las la-star"></i>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Date -->
                    <div class="form-group">
                        <label>{{ translate('Date')}} <span class="text-danger">*</span></label>
                        <div class="w-100">
                            <div class="d-flex mb-4">
                                <div class="radio mar-btm mr-5 d-flex align-items-center">
                                    <input id="system_date" type="radio" name="review_date_type" value="system_date" onchange="reviewDateType()" checked >
                                    <label for="system_date" class="mb-0 ml-2">{{translate('System Date')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="custom_date" type="radio" name="review_date_type" value="custom" onchange="reviewDateType()">
                                    <label for="custom_date" class="mb-0 ml-2">{{translate('Select')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="date-selection-div d-none">
                            <input type="text" class="form-control aiz-date-range" name="custom_date" placeholder="{{ translate('Select Date') }}" data-single="true" data-time-picker="true" data-format="Y-MM-DD HH:mm:ss" data-show-dropdown="true" autocomplete="off">
                        </div>
                    </div>

                    <!-- Comment -->
                    <div class="form-group">
                        <label for="comment">{{ translate('Comment')}} <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="4" name="comment" id="comment" placeholder="{{ translate('Your review')}}" required></textarea>
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
                                <input type="hidden" name="photos[]" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <small class="text-muted">{{translate('These images are visible in product review page gallery. Upload square images')}}</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
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

        $(document).on('change', '[name=category_id]', function() {
            var categoryId = $(this).val();
            get_products(categoryId);
        });


        function get_products(CategoryId) {
            $('#product_selection').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-custom-review-product-by-category')}}",
                type: 'POST',
                data: {
                    category_id  : CategoryId
                },
                success: function (data) {
                    if(data != '') {
                        $("#product_selection").removeAttr("disabled");
                        $('#product_selection').html(data);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

    </script>
@endsection