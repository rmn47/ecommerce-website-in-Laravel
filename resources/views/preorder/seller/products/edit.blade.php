@extends('seller.layouts.app')
@section('panel_content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Update Product Information')}}</h5>
</div>
<div class="">
    <!-- Error Meassages -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form class="form form-horizontal mar-top" action="{{route('seller.preorder-product.update', $product->id)}}" method="POST" enctype="multipart/form-data" id="choice_form">
        <input type="hidden" name="lang" value="{{ $lang }}">
        @csrf
        @method('PUT')
        <div class="row gutters-5">
            <div class="col-lg-8">
                <input type="hidden" name="id" value="{{ $product->id }}">

                <!-- ====================Product Information================= -->
                <div class="card">
                    <ul class="nav nav-tabs nav-fill language-bar">
                        @foreach (get_all_active_language() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('seller.preorder-product.edit', ['id' => $product->id, 'lang' => $language->code]) }}">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                <span>{{$language->name}}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="card-body">
                        {{-- Product Name --}}
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Product Name')}} 
                                <span class="text-danger">*</span>  <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i>
                            </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="product_name" placeholder="{{ translate('Product Name') }}" value="{{ $product->getTranslation('product_name',$lang) }}" required>
                                <small class="text-muted">{{translate('Enter a descriptive name for the product. [e.g. "Wireless Bluetooth Headphones"]')}}</small>
                            </div>
                        </div>

                        {{-- Brand --}}
                        <div class="form-group row" id="brand">
                            <label class="col-md-3 col-from-label">{{translate('Brand')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                    <option value="">{{ translate('Select Brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}" @selected($brand->id == $product->brand_id)>{{ $brand->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ translate('Choose the product\'s brand from the list. [e.g. "Sony"]') }}</small>
                            </div>
                        </div>

                        {{-- Unit --}}
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Unit')}}
                                <span class="text-danger">*</span>  <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i>
                            </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" value="{{ $product->getTranslation('unit',$lang) }}" required>
                                <small class="text-muted">{{translate('Specify the unit of measurement for the product. [e.g. "Piece" or "kg"]')}}</small>
                            </div>
                        </div>

                        {{-- Minimum Purchase Qty --}}
                        <div class="form-group row" style="display: none">
                            <label class="col-md-3 col-from-label">{{translate('Minimum Purchase Qty')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="min_qty"  min="1" value="{{$product->min_qty}}" required>
                                <small class="text-muted">{{translate('Set the minimum quantity a customer must buy. [e.g. "2"]')}}</small>
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Tags')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control aiz-tag-input" name="tags[]" id="tags" value="{{ $product->tags }}" placeholder="{{ translate('Type to add a tag') }}" data-role="tagsinput">
                                <small class="text-muted">{{translate('Add keywords to help customers find this product. [e.g. "wireless, headphones, audio"]')}}</small>
                            </div>
                        </div>

                        {{-- Barcode --}}
                        @if (addon_is_activated('pos_system'))
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Barcode')}}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}" value="{{$product->barcode}}">
                                    <small class="text-muted">{{translate('Enter the product’s barcode or SKU for inventory tracking. [e.g. "1234567890123"]')}}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ====================Product Files & Media================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Files & Media')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}}
                                <small>(600x400)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="images" value="{{ $product->images }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('Upload multiple images, each 600x400 pixels. [e.g. Images showing products from different angles.]')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}}
                                <small>(300x200)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="thumbnail" value="{{ $product->thumbnail }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('Upload a primary image at 300x300 pixels for quick preview. [e.g. A front view of the product.]')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Provider')}}</label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                    <option value="youtube" @selected($product->video_provider == 'youtube')>{{translate('Youtube')}}</option>
                                    <option value="dailymotion" @selected($product->video_provider == 'dailymotion')>{{translate('Dailymotion')}}</option>
                                    <option value="vimeo" @selected($product->video_provider == 'vimeo')>{{translate('Vimeo')}}</option>
                                </select>
                                <small class="text-muted">{{translate('Select the video platform hosting the product video. [e.g. "YouTube"]')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Link')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="video_link" value="{{ $product->video_link }}" placeholder="{{ translate('Video Link') }}">
                                <small class="text-muted">{{ translate('Provide the link to the product video. [e.g. "https://www.youtube.com/watch?v=12345"]') }}</small>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ====================Product Description================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Description')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                {{translate('Description')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i>
                            </label>
                            <div class="col-md-8">
                                <textarea class="aiz-text-editor" name="description">{{ $product->getTranslation('description', $lang) }}</textarea>
                                <small class="text-muted">{{translate('Write a detailed overview of the product, including features. [e.g. "These wireless Bluetooth headphones offer premium sound quality, noise cancellation, and a comfortable design for all-day use."]')}}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====================Product Price & Discounts================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Price & Discounts')}}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <label class="fs-14 fw-700 mb-0 mb-4">{{translate('Price')}}</label>
                            <div class="form-group row">
                                <label class="col-sm-3 control-label" for="start_date">{{translate('Unit price')}} <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" min="0" step="1"
                                        placeholder="{{ translate('Unit price') }}" name="unit_price"
                                        value="{{ $product->unit_price}}" class="form-control" required>
                                        <small class="text-muted">{{translate('Enter the price per unit. [e.g. "$49.99"]')}}</small>
                                </div>
                            </div>
                            <hr style="border-bottom: 1px dashed #e4e5eb;">
                        </div>
                        <!-- ====================Prepayment================= -->
                        <div class="prepayment">
                            <label class="fs-14 fw-700 mb-0 mb-4">{{translate('Prepayment')}}</label>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Enable Prepayment')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="is_prepayment" value="1" id="is_prepayment"
                                            {{$product->is_prepayment ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div id="prepaymentBlock" style="display: {{$product->is_prepayment ? 'block' : 'none' }};">

                                <div class="form-group row">
                                    <label class="col-sm-3 control-label" for="start_date">{{translate('Prepay Amount')}} </label>
                                    <div class="col-sm-9">
                                        <input type="number" min="0" value="{{ $prepayment?->prepayment_amount }}"
                                            step="1" placeholder="{{ translate('Prepay Amount') }}"
                                            name="prepayment_amount" class="form-control">
                                            <small class="text-muted">{{translate('Specify the required prepayment amount for pre-orders. [e.g. "$10.00"]')}}</small>
                                    </div>
                                </div>
                            </div>
                            <hr style="border-bottom: 1px dashed #e4e5eb;">
                        </div>
 
                        <!-- ====================Discount Settings================= -->
                        <div class="discount-settings">
                            <label class="fs-14 fw-700 mb-0 mb-4">{{translate('Discount Settings')}}</label>

                            @php
                                $start_date = date('d-m-Y H:i:s', $product->discount_start_date);
                                $end_date = date('d-m-Y H:i:s', $product->discount_end_date);
                            @endphp
                            <div class="form-group row">
                                <label class="col-sm-3 control-label" for="start_date">{{translate('Discount Date Range')}}</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control aiz-date-range" name="date_range"
                                        @if($product->discount_start_date && $product->discount_end_date) value="{{ $start_date.' to '.$end_date }}" @endif
                                        placeholder="{{translate('Select Date')}}" data-time-picker="true"
                                        data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                        <small class="text-muted">{{translate('Choose start and end dates for a discount period. [e.g. "01/01/2024 - 01/15/2024"]')}}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 control-label" for="start_date">{{translate('Discount')}}</label>
                                <div class="col-sm-9">
                                    <div class="form-row">
                                        <div class="form-group col-md-9">
                                            <input type="number" min="0" step="1"
                                                placeholder="{{ translate('Discount Amount') }}" name="discount"
                                                value="{{$product->discount}}" class="form-control">
                                        </div>
                                        <div class="form-group col-md-3">
                                            <select class="form-control aiz-selectpicker" name="discount_type">
                                                <option value="flat" @selected($product->discount_type == 'flat')>{{translate('Flat')}}</option>
                                                <option value="percent" @selected($product->discount_type == 'percent')>{{translate('Percent')}}</option>
                                            </select>
                                        </div>
                                        <small class="text-muted">{{translate('Specify the discount percentage or amount. [e.g. "10%" or "$5.00"]')}}</small>
                                    </div>
                                </div>
                            </div>

                            <hr style="border-bottom: 1px dashed #e4e5eb;">
                        </div>
                        <!-- ====================Coupons================= -->
                        <div class="coupons">
                            <label class="fs-14 fw-700 mb-0 mb-4">{{translate('Coupons')}}</label>
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{translate('Use Coupon For This Product')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="is_coupon" value="1" id="is_coupon"
                                            {{$product->is_coupon ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div id="couponBlock" style="display: {{$product->is_coupon ? 'block' : 'none' }}">

                                <div class="form-group row">
                                    <label class="col-sm-3 control-label" for="start_date">{{translate('Coupon Code')}}</label>
                                    <div class="col-sm-9">
                                        <div class="row">
                                            <div class="form-group col-md-9">
                                                <input type="text" placeholder="{{ translate('Coupon Code') }}"
                                                    name="coupon_code" value="{{$coupon?->coupon_code}}"
                                                    class="form-control" id="coupon_code">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <span class="btn btn-secondary btn-block"
                                                    id="generate-coupon">{{translate('Generate ')}}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                $start_date = date('d-m-Y H:i:s', $coupon?->coupon_start_date);
                                $end_date = date('d-m-Y H:i:s', $coupon?->coupon_end_date);
                                @endphp
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{translate('Coupon Date Range')}}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control aiz-date-range" name="coupon_date_range" 
                                        @if($coupon?->coupon_start_date && $coupon?->coupon_end_date) value="{{ $start_date.' to '.$end_date }}" @endif 
                                        placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                        <small class="text-muted">{{translate('Choose start and end dates for a coupon discount period. [e.g. "01/01/2024 - 01/15/2024"]')}}</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 control-label" for="start_date">{{translate('Discount')}}</label>
                                    <div class="col-sm-9">
                                        <div class="form-row">
                                            <div class="form-group col-md-9">
                                                <input type="number" min="0" value="{{$coupon?->coupon_amount}}" step="1"
                                                    placeholder="{{ translate('Discount Amount') }}" name="coupon_amount"
                                                    class="form-control">
                                            </div>
                                            <div class="form-group col-md-3">
                                                <select class="form-control aiz-selectpicker" name="coupon_type">
                                                    <option value="flat" @selected($coupon?->coupon_type == 'flat')>{{translate('Flat')}}</option>
                                                    <option value="percent" @selected($coupon?->coupon_type == 'percent')>{{translate('Percent')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <small class="text-muted">{{translate('Specify the coupon amount percentage or amount. [e.g. "10%" or "$5.00"]')}}</small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- ====================SEO Meta Tags================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('SEO Meta Tags')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Meta Title')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="meta_title"
                                    value="{{$product->meta_title}}" placeholder="{{ translate('Meta Title') }}">
                                    <small class="text-muted">{{translate('Add a title for SEO purposes to improve search visibility. [e.g. "Premium Wireless Bluetooth Headphones"]')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-md-8">
                                <textarea name="meta_description" rows="8" class="form-control">{{$product->meta_description}}</textarea>
                                <small class="text-muted">{{translate('Provide a short SEO-friendly description. [e.g. "High-quality Bluetooth headphones with noise cancellation."]')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="meta_image" value="{{$product->meta_image}}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('Upload an image that represents the product in search engines. [e.g. A high-resolution image of the product.]')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right side --}}
            <div class="col-lg-4">
                <!-- Product Category -->
                <div class="card @if($errors->has('category_ids') || $errors->has('category_id')) border border-danger @endif">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Product Category') }}</h5>
                        <h6 class="float-right fs-13 mb-0">
                            {{ translate('Select Main') }}
                            <span class="position-relative main-category-info-icon">
                                <i class="las la-question-circle fs-18 text-info"></i>
                                <span class="main-category-info bg-soft-info p-2 position-absolute d-none border">{{ translate('This will be used for commission based calculations and homepage category wise product Show.') }}</span>
                            </span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="h-300px overflow-auto c-scrollbar-light">
                            @php
                                $old_categories = $product->categories()->pluck('category_id')->toArray();
                            @endphp
                            <ul class="hummingbird-treeview-converter list-unstyled" data-checkbox-name="category_ids[]" data-radio-name="category_id">
                                @foreach ($categories as $category)
                                <li id="{{ $category->id }}">{{ $category->getTranslation('name') }}</li>
                                    @foreach ($category->childrenCategories as $childCategory)
                                        @include('backend.product.products.child_category', ['child_category' => $childCategory])
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ====================Product Settings================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 "> {{translate('Product Settings')}} </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Published')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_published" value="1" {{ $product->is_published == 1 ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                            <small class=" col-md-12 col-from-label text-muted">{{translate('Upload an image that represents the product in search engines. [e.g. A high-resolution image of the product.]')}}</small>
                        </div>


                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Available Now')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" id="is_available" name="is_available" value="1" {{ $product->is_available == 1 ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                            <small class="col-md-12 col-from-label text-muted">
                                {{translate('Indicate if the product is in stock and ready to ship.')}}
                            </small>
                        </div>
                        
                        <div class="form-group row" id="available_date_group" style="{{ $product->is_available == 1 ? 'display: none;' : 'display: flex;' }}">
                            <label class="col col-from-label">{{translate('Available From')}}</label>
                            <div class="col col-from-label">
                                <input type="date" class="form-control aiz-date" name="available_date" placeholder="{{translate('Select Date')}}" 
                                       value="{{ $product->available_date }}">
                                       
                                <span></span>
                            </div>
                            <small class="col-md-12 col-from-label text-muted">
                                {{translate('Set a date when the product will become available. Example: "01/20/2024"')}}
                            </small>
                        </div>
                        

                    </div>
                </div>

                <!-- ====================Refundable================= -->
                @if (addon_is_activated('refund_request'))
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6"> {{translate('Refund')}} </h5>
                        </div>

                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-6 col-from-label">{{translate('Refundable')}}</label>
                                <div class="col-md-6">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="is_refundable" value="1" id="is_refundable" {{ $product->is_refundable == 1 ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div id="refundBlock" style="display: {{ $product->is_refundable == 1 ? 'block' : 'none' }}">
                                <div class="form-group row">
                                    <div class="form-check col-md-12 ml-3">
                                        <input class="form-check-input" type="checkbox" name="show_refund_note" value="1"
                                            id="flexCheckChecked" {{ $refund?->show_refund_note == 1 ? 'checked' : '' }}>

                                        <label class="form-check-label" for="flexCheckChecked">
                                            <b>{{translate('Show notes in refund section in product description page')}}</b>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label class="form-check-label fw-bold" for="flexCheckChecked">
                                            <b>{{translate('Note (Add from preset)')}} </b>
                                        </label>
                                    </div>
                                </div>
                                <input type="hidden" name="refund_note_id" id="refund_note_id" value="{{$refund?->note?->id}}">
                                <div id="refund_note" class="">
                                    @if($refund?->note != null)
                                        <div class="border border-gray my-2 p-2">
                                            {{ $refund->note->getTranslation('description') ?? '' }}
                                        </div>
                                    @endif
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="noteModal('refund')">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Select Refund Note') }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                @endif

                <!-- ====================Shipping================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">
                            {{translate('Shipping')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <h6 class="fw-bold"> {{translate('Shipping Configuration')}}</h6>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Free Shipping')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="free" {{ $shipping->shipping_type == 'free'
                                    ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Flat Rate')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="flat" {{ $shipping->shipping_type == 'flat'
                                    ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>


                        <div class="form-group row">
                            <div class="form-check col-md-12 ml-3">

                                <input class="form-check-input" type="checkbox" value="1" id="show_shipping_days"
                                    name="show_shipping_time" {{ $shipping?->show_shipping_time == 1 ? 'checked' : ''}}>

                                <label class="form-check-label" for="show_shipping_days">
                                    <b> {{translate('Show estimated shipping time in product description page')}}</b>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row" id="show_shipping_days_block" style="display: {{ $product->is_cod == 1 ? 'block' : 'none' }}">
                            <div class="form-group row d-flex">
                                <div class="form-check col-md-6">
                                    <label for="name">{{translate('Minimum Shipping Days')}}</label>
                                    <input type="text" class="form-control" name="min_shipping_days" placeholder="{{ translate('write in days') }}" value="{{$shipping?->min_shipping_days}}">
                                </div>
                                <div class="form-check col-md-6 ">
                                    <label for="name">{{translate('Maximum Shipping Days')}}</label>
                                    <input type="text" class="form-control" name="max_shipping_days" placeholder="{{ translate('write in days') }}" value="{{$shipping?->max_shipping_days}}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row" >
                            <div class=" form-check col-md-12 ml-3">

                                <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked"
                                    name="show_shipping_note" {{ $shipping?->show_shipping_note == 1 ? 'checked' : ''}}>

                                <label class="form-check-label" for="flexCheckChecked">
                                    <b>{{translate('Show notes in shipping time section')}} </b>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-check-label fw-bold" for="flexCheckChecked">
                                    <b>{{translate('Note (Add from preset)')}} </b>
                                </label>
                            </div>
                        </div>
                        
                        <input type="hidden" name="shipping_note_id" id="shipping_note_id" value="{{$shipping?->note?->id}}">
                        <div id="shipping_note" class="">
                            @if($shipping?->note != null)
                                <div class="border border-gray my-2 p-2">
                                    {{ $shipping->note->getTranslation('description') ?? '' }}
                                </div>
                            @endif
                        </div>
                        <button
                            type="button"
                            class="btn btn-block border border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-0 d-flex align-items-center justify-content-center"
                            onclick="noteModal('shipping')">
                            <i class="las la-plus"></i>
                            <span class="ml-2">{{ translate('Select Shipping Note') }}</span>
                        </button>
                    </div>
                </div>

                <!-- ====================Cash On Delivery================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 ">
                            {{translate('Cash On Delivery')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Cash on delivery available')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_cod" value="1" id="is_cod" {{ $product->is_cod == 1
                                    ? 'checked' : '' }}>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div id="codBlock" style="display: {{ $product->is_cod == 1 ? 'block' : 'none' }}">
                            <div class="form-group row">
                                <div class="form-check col-md-12 ml-3" id="prepayment_needed_for_cod" style="display: {{ $product->is_prepayment == 1 ? 'block' : 'none' }}">
                                    <input class="form-check-input" type="checkbox" value="1" name="prepayment_needed" {{ $cod?->prepayment_needed == 1 ? 'checked' : '' }}>

                                    <label class="form-check-label " for="flexCheckChecked">
                                        <b> {{translate('Prepayment needed for cash on delivery')}}</b>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="form-check col-md-12 ml-3">
                                    <input class="form-check-input" type="checkbox" value="1" name="show_cod_note" {{ $cod?->show_cod_note == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        <b> {{translate('Show note in cash on delivery section in product description page')}}</b>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label class="form-check-label fw-bold" for="flexCheckChecked">
                                        <b>{{translate('Note (Add from preset)')}} </b>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="delivery_note_id" id="delivery_note_id" value="{{ $cod?->note?->id}}">
                            <div id="delivery_note" class="">
                                @if($cod?->note != null)
                                    <div class="border border-gray my-2 p-2">
                                        {{ $cod->note->getTranslation('description') ?? '' }}
                                    </div>
                                @endif
                            </div>
                            <button
                                type="button"
                                class="btn btn-block border border-dashed hov-bg-soft-secondary mt-2 fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                onclick="noteModal('delivery')">
                                <i class="las la-plus"></i>
                                <span class="ml-2">{{ translate('Select Delivery Note') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ====================Vat & Tax================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('VAT & Tax')}}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($taxes as $tax)
                            @php
                            @endphp
                            <label for="name">
                                {{$tax->preorder_tax?->name}}
                                <input type="hidden" value="{{$tax->tax_id}}" name="tax_id[]">
                            </label>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="number" min="0" value="{{$tax->tax}}" step="1"
                                        placeholder="{{ translate('Tax') }}" name="tax_amount[]" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <select class="form-control aiz-selectpicker" name="tax_type[]">
                                        <option value="amount" @selected($tax->tax_type == 'amount')>{{translate('Flat')}}
                                        </option>
                                        <option value="percent" @selected($tax->tax_type ==
                                            'percent')>{{translate('Percent')}}</option>
                                    </select>
                                </div>
                                <small class="col-md-12 col-from-label text-muted">{{translate('Enter the vat & tax rate percentage for this product. [e.g. "8%"]')}}</small>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- ====================More Products to Preorder================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 ">
                            {{translate('More Products to Preorder')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <p>{{translate('This is a customised section in product description page where you can select end category or selected products as More Products to Preorder.')}}</p>
                        <label class="fs-14 fw-700 mb-0 mb-4">{{translate('Select Pre Order Products')}}</label>
                        <div class="w-100">
                            <div class="card">
                                <div class="card-body">
                                    <div id="selected-pre-order-products-div">
                                        @php
                                            $more_product_ids = json_decode($product->more_products, true);
                                            if (is_array($more_product_ids) && !empty($more_product_ids)) {
                                                $more_products = \App\Models\PreorderProduct::whereIn('id', $more_product_ids)->get();
                                            } else {
                                                $more_products = collect(); // Empty collection if no valid IDs
                                            }
                                        @endphp
                                        @if(count($more_products) > 0)
                                        <div class="table-responsive mb-4">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="opacity-50 pl-0">{{ translate('Product Thumb') }}</th>
                                                        <th class="opacity-50">{{ translate('Product Name') }} </th>
                                                        <th class="opacity-50">{{ translate('Category') }}</th>
                                                        <th class="opacity-50 text-right pr-0">{{ translate('Options') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($more_products as $more_product)
                                                    <tr class="remove-parent">
                                                        <input type="hidden" name="pre_order_product_ids[]" value="{{ $more_product->id }}">
                                                        <td class="w-150px pl-0" style="vertical-align: middle;">
                                                            <p class="d-block size-48px">
                                                                <img src="{{ uploaded_asset($more_product->thumbnail) }}"
                                                                    alt="{{ translate('Image')}}"
                                                                    class="h-100 img-fit lazyload"
                                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                            </p>
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <p class="d-block fs-13 fw-700 hov-text-primary mb-1 text-dark"
                                                                title="{{ translate('Product Name') }}">
                                                                {{ $more_product->getTranslation('product_name') }}
                                                            </p>
                                                        </td>
                                                        <td style="vertical-align: middle;">{{ $more_product->main_category->name ?? translate('Category Not Found') }}</td>
                                                        <td class="text-right pr-0" style="vertical-align: middle;">
                                                            <button type="button"
                                                                class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger"
                                                                data-toggle="remove-parent"
                                                                data-parent=".remove-parent">
                                                                <i class="las la-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                    </div>
                                    <button type="button"
                                        class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                        onclick="showPreOrderProductModal()">
                                        <i class="las la-plus"></i>
                                        <span class="ml-2">{{ translate('Add More') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ====================Frequently Bought Products================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 ">
                            {{translate('Frequently Bought Products')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <p>{{translate('This is a customised section in product description page where you can select
                            end category or selected products as More Products to Preorder.')}}</p>
                        <div class="w-100">
                            <div class="d-flex mb-4">
                                <div class="radio mar-btm mr-5 d-flex align-items-center">
                                    <input id="fq_bought_select_products" type="radio"
                                        name="frequently_bought_selection_type" value="product"
                                        onchange="fq_bought_product_selection_type()"
                                        @if($product->frequently_bought_type == 'product') checked @endif
                                    >
                                    <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="fq_bought_select_category" type="radio"
                                        name="frequently_bought_selection_type" value="category"
                                        onchange="fq_bought_product_selection_type()"
                                        @if($product->frequently_bought_type == 'category') checked @endif
                                    >
                                    <label for="fq_bought_select_category" class="fs-14 fw-700 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">

                                    <div class="fq_bought_select_product_div d-none">
                                        <div id="selected-fq-bought-products">
                                            @php
                                                $frequently_bought_product_ids = json_decode($product->frequently_bought_product, true);
                                                if (is_array($frequently_bought_product_ids) && !empty($frequently_bought_product_ids)) {
                                                    $fq_bought_products = \App\Models\Product::whereIn('id',
                                                    $frequently_bought_product_ids)->get();
                                                } else {
                                                    $fq_bought_products = collect(); // Empty collection if no valid IDs
                                                }
                                            @endphp
                                            @if(count($fq_bought_products) > 0)
                                                <div class="table-responsive mb-4">
                                                    <table class="table mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th class="opacity-50 pl-0">{{ translate('Product Thumb') }}</th>
                                                                <th class="opacity-50">{{ translate('Product Name') }}</th>
                                                                <th class="opacity-50">{{ translate('Category') }}</th>
                                                                <th class="opacity-50 text-right pr-0">{{translate('Options') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($fq_bought_products as $fQBproduct)
                                                            <tr class="remove-parent">
                                                                <input type="hidden" name="fq_bought_product_ids[]" value="{{ $fQBproduct->id }}">
                                                                <td class="w-150px pl-0" style="vertical-align: middle;">
                                                                    <p class="d-block size-48px">
                                                                        <img src="{{ uploaded_asset($fQBproduct->thumbnail_img) }}"
                                                                            alt="{{ translate('Image')}}"
                                                                            class="h-100 img-fit lazyload"
                                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                                    </p>
                                                                </td>
                                                                <td style="vertical-align: middle;">
                                                                    <p class="d-block fs-13 fw-700 hov-text-primary mb-1 text-dark"
                                                                        title="{{ translate('Product Name') }}">
                                                                        {{ $fQBproduct->getTranslation('name') }}
                                                                    </p>
                                                                </td>
                                                                <td style="vertical-align: middle;">{{ $fQBproduct->main_category->name ?? translate('Category Not Found') }}</td>
                                                                <td class="text-right pr-0" style="vertical-align: middle;">
                                                                    <button type="button"
                                                                        class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger"
                                                                        data-toggle="remove-parent"
                                                                        data-parent=".remove-parent">
                                                                        <i class="las la-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="button"
                                            class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                            onclick="showFqBoughtProductModal()">
                                            <i class="las la-plus"></i>
                                            <span class="ml-2">{{ translate('Add More') }}</span>
                                        </button>
                                    </div>

                                    {{-- Select Category for Frequently Bought Product --}}
                                    <div class="fq_bought_select_category_div d-none">
                                        @php
                                            $fqCategory = $product->frequently_bought_category != null ?
                                            $product->frequently_bought_category : null;
                                        @endphp
                                        <div class="form-group row">
                                            <label class="col-md-3 col-from-label">{{translate('Category')}} <span class="text-danger">*</span></label>
                                            <div class="col-md-9">
                                                <select class="form-control aiz-selectpicker"
                                                    data-placeholder="{{ translate('Select a Category')}}"
                                                    name="fq_bought_product_category_id" data-live-search="true"
                                                    data-selected="{{ $fqCategory }}">
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{$category->getTranslation('name') }}</option>
                                                        @foreach ($category->childrenCategories as $childCategory)
                                                            @include('categories.child_category', ['child_category' => $childCategory])
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="Third group">
                        <button type="submit" name="button" class="btn btn-success action-btn">{{translate('Update Product') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
@section('modal')
    <!-- Frequently Bought Product Select Modal -->
    @include('modals.product_select_modal')

    {{-- Pre Order Product Select Model --}}
    @include('preorder.common.models.pre_order_product_select_modal')

    {{-- Note Modal --}}
    @include('modals.note_modal')

@endsection
@section('script')
<!-- Treeview js -->
<script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function (){
        $("#treeview").hummingbird();
        var main_id = '{{ $product->category_id != null ? $product->category_id : 0 }}';
        var selected_ids = '{{ implode(",",$old_categories) }}';
        if (selected_ids != '') {
            const myArray = selected_ids.split(",");
            for (let i = 0; i < myArray.length; i++) {
                const element = myArray[i];
                $('#treeview input:checkbox#'+element).prop('checked',true);
                $('#treeview input:checkbox#'+element).parents( "ul" ).css( "display", "block" );
                $('#treeview input:checkbox#'+element).parents( "li" ).children('.las').removeClass( "la-plus" ).addClass('la-minus');
            }
        }
        $('#treeview input:radio[value='+main_id+']').prop('checked',true);

        fq_bought_product_selection_type();
        AIZ.plugins.tagify();
    });

    $('form').bind('submit', function (e) {
		if ( $(".action-btn").attr('attempted') == 'true' ) {
			//stop submitting the form because we have already clicked submit.
			e.preventDefault();
		}
		else {
			$(".action-btn").attr("attempted", 'true');
		}
    });

    // frquently bought product add start
    function fq_bought_product_selection_type(){
        var productSelectionType = $("input[name='frequently_bought_selection_type']:checked").val();
        if(productSelectionType == 'product'){
            $('.fq_bought_select_product_div').removeClass('d-none');
            $('.fq_bought_select_category_div').addClass('d-none');
        }
        else if(productSelectionType == 'category'){
            $('.fq_bought_select_category_div').removeClass('d-none');
            $('.fq_bought_select_product_div').addClass('d-none');
        }
    }

    function showFqBoughtProductModal() {
        $('#fq-bought-product-select-modal').modal('show', {backdrop: 'static'});
    }

    function filterFqBoughtProduct() {
        var productID = $('input[name=id]').val();
        var searchKey = $('input[name=search_keyword]').val();
        var fqBroughCategory = $('select[name=fq_brough_category]').val();
        $.post('{{ route('seller.product.search') }}', { _token: AIZ.data.csrf, product_id: productID, search_key:searchKey, category:fqBroughCategory, product_type:"physical" }, function(data){
            $('#product-list').html(data);
            AIZ.plugins.sectionFooTable('#product-list');
        });
    }

    function addFqBoughtProduct() {
        var selectedProducts = [];
        $("input:checkbox[name=fq_bought_product_id]:checked").each(function() {
            selectedProducts.push($(this).val());
        });

        var fqBoughtProductIds = [];
        $("input[name='fq_bought_product_ids[]']").each(function() {
            fqBoughtProductIds.push($(this).val());
        });

        var productIds = selectedProducts.concat(fqBoughtProductIds.filter((item) => selectedProducts.indexOf(item) < 0))

        $.post('{{ route('seller.get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#fq-bought-product-select-modal').modal('hide');
            $('#selected-fq-bought-products').html(data);
            AIZ.plugins.sectionFooTable('#selected-fq-bought-products');
        });
    }
    // frquently bought product add End


    // More Products to Preorder Start
    function showPreOrderProductModal() {
        $('#pre-order-product-select-modal').modal('show', {backdrop: 'static'});
    }

    function filterPreOrderProduct() {
        var productID = $('input[name=id]').val();
        var searchKey = $('input[name=pre_order_search_keyword]').val();
        var preOrderCategory = $('select[name=pre_order_category]').val();
        $.post('{{ route('preorder_product.search') }}', { _token: AIZ.data.csrf, product_id: null, search_key:searchKey, category:preOrderCategory }, function(data){
            $('#pre-order-product-list').html(data);
            AIZ.plugins.sectionFooTable('#pre-order-product-list');
        });
    }
    
    function addPreOrderProduct() {
        var selectedProducts = [];
        $("input:checkbox[name=pre_order_product_id]:checked").each(function() {
            selectedProducts.push($(this).val());
        });

        var preOrderProductIds = [];
        $("input[name='pre_order_product_ids[]']").each(function() {
            preOrderProductIds.push($(this).val());
        });

        var productIds = selectedProducts.concat(preOrderProductIds.filter((item) => selectedProducts.indexOf(item) < 0))
        $.post('{{ route('get-selected-preorder-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#pre-order-product-select-modal').modal('hide');
            $('#selected-pre-order-products-div').html(data);
            AIZ.plugins.sectionFooTable('#selected-pre-order-products-div');
        });
    }
    // More Products to Preorder end

    // Note modal
    function noteModal(note_type){
        $.post('{{ route('get_notes') }}',{_token:'{{ @csrf_token() }}', note_type: note_type}, function(data){
            $('#note_modal #note_modal_content').html(data);
            $('#note_modal').modal('show', {backdrop: 'static'});
        });
    }
    
    // show selected note and set Note ID
    function addNote(noteId, noteType){
        var noteDescription = $('#note_description_'+ noteId).val();
        $('#'+noteType+'_note_id').val(noteId);
        $('#'+noteType+'_note').html(noteDescription);
        $('#'+noteType+'_note').addClass('border border-gray my-2 p-2');
        $('#note_modal').modal('hide');
    }

</script>
<script>
    $(document).ready(function(){
        var hash = document.location.hash;
        if (hash) {
            $('.nav-tabs a[href="'+hash+'"]').tab('show');
        }else{
            $('.nav-tabs a[href="#general"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });



// prepayment section 
  const is_prepayment = document.getElementById('is_prepayment');
  const prepaymentBlock = document.getElementById('prepaymentBlock');
  const cod_block = document.getElementById('prepayment_needed_for_cod');

  is_prepayment.addEventListener('change', function() {
    if (is_prepayment.checked) {
      prepaymentBlock.style.display = 'block'; 
      cod_block.style.display = 'block'; 
    } else {
      prepaymentBlock.style.display = 'none';  
      cod_block.style.display = 'none';  
    }
  });


// Shipping days section 
  const show_shipping_days = document.getElementById('show_shipping_days');
  const show_shipping_days_block = document.getElementById('show_shipping_days_block');
  show_shipping_days.addEventListener('change', function() {
    if (show_shipping_days.checked) {
        show_shipping_days_block.style.display = 'block'; 
    } else {
        show_shipping_days_block.style.display = 'none';  
    }
  });


// CODsection 
  const is_cod = document.getElementById('is_cod');
  const codBlock = document.getElementById('codBlock');
  is_cod.addEventListener('change', function() {
    if (is_cod.checked) {
      codBlock.style.display = 'block'; 
    } else {
      codBlock.style.display = 'none';  
    }
  });

        // Refund section 
        const isRefundAddonActivated = {{ json_encode(addon_is_activated('refund_request')) }};

        if (isRefundAddonActivated) {
            // Refund section
            const is_refundable = document.getElementById('is_refundable');
            const refundBlock = document.getElementById('refundBlock');

            if (is_refundable) {
                is_refundable.addEventListener('change', function () {
                    if (is_refundable.checked) {
                        refundBlock.style.display = 'block';
                    } else {
                        refundBlock.style.display = 'none';
                    }
                });
            }
        }


// Coupon section 
  // Get the radio button and the hidden div
  const is_coupon = document.getElementById('is_coupon');
  const couponBlock = document.getElementById('couponBlock');

  // Function to show/hide div based on radio button checked state
  is_coupon.addEventListener('change', function() {
    if (is_coupon.checked) {
      couponBlock.style.display = 'block'; 
    } else {
      couponBlock.style.display = 'none';  
    }
  });


//Whole sale Add functionality for adding and deleting rows. 
$(document).ready(function() {
    // Add new wholesale price row when 'Add More' button is clicked
    $('.add-more-row').on('click', function() {
        // Clone the hidden template row
        var newRow = $('.wholesale-price-template').html();
        // Append the new row to the wholesale price container
        $('#wholesale-prices-container').append(newRow);
    });

    // Delete a wholesale price row when delete button is clicked
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.wholesale-price-row').remove();
    });
});

//Discount Period Add functionality for adding and deleting rows. 
$(document).ready(function() {
    // Add new discount row when 'Add More' button is clicked
    $('.add-more-discount-row').on('click', function() {
        // Clone the hidden template row
        var newRow = $('.discount-period-template').html();
        // Append the new row to the discount periods container
        $('#discount-periods-container').append(newRow);
    });

    // Delete a discount row when delete button is clicked
    $(document).on('click', '.remove-discount-row', function() {
        $(this).closest('.discount-period-row').remove();
    });
});

// Generate coupon 
$(document).ready(function() {
    $('#generate-coupon').on('click', function() {
        // Function to generate a random alphanumeric coupon code
        var couponCode = generateCouponCode(10); // You can set the length of the code here
        $('#coupon_code').val(couponCode);
    });

    function generateCouponCode(length) {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // You can include lowercase letters if needed
        var result = '';
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        return result;
    }
});

// available date filed hide and show
document.getElementById('is_available').addEventListener('change', function() {
        var dateField = document.getElementById('available_date_group');
        if (this.checked) {
            dateField.style.display = 'none';
        } else {
            dateField.style.display = 'flex';  // Adjust to match your layout
        }
    });

    // Trigger on load to ensure the correct state on page load
    window.addEventListener('DOMContentLoaded', function() {
        var checkbox = document.getElementById('is_available');
        var dateField = document.getElementById('available_date_group');
        if (checkbox.checked) {
            dateField.style.display = 'none';
        } else {
            dateField.style.display = 'flex';
        }
    });
</script>

@endsection