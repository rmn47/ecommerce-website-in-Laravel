@extends('backend.layouts.app')

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Product')}}</h5>
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
    <form class="form form-horizontal mar-top" action="{{route('preorder-product.store')}}" method="POST" enctype="multipart/form-data" id="choice_form">
        <div class="row gutters-5">
            <div class="col-lg-8">
                @csrf
                <input type="hidden" name="added_by" value="admin">

<!-- ====================Product Information================= -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Product Name')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="product_name" placeholder="{{ translate('Product Name') }}" onchange="update_sku()" required>
                            </div>
                        </div>
                        <div class="form-group row" id="category">
                            <label class="col-md-3 col-from-label">{{translate('Category')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="category_id" id="category_id" data-live-search="true" required>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                    @foreach ($category->childrenCategories as $childCategory)
                                    @include('categories.child_category', ['child_category' => $childCategory])
                                    @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="brand">
                            <label class="col-md-3 col-from-label">{{translate('Brand')}}</label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="brand_id" id="brand_id" data-live-search="true">
                                    <option value="">{{ translate('Select Brand') }}</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Unit')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="unit" placeholder="{{ translate('Unit (e.g. KG, Pc etc)') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Weight')}} <small>({{ translate('In Kg') }})</small></label>
                            <div class="col-md-8">
                                <input type="number" class="form-control" name="weight" step="1" value="0.00" placeholder="0.00">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Minimum Purchase Qty')}} <span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input type="number"  class="form-control" name="min_qty" value="1" min="1" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Barcode')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="barcode" placeholder="{{ translate('Barcode') }}">
                            </div>
                        </div>
                        


                    </div>
                </div>
<!-- ====================Product Files & Media================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Product Files & Media')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}} <small>(600x600)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="images" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('These images are visible in product details page gallery. Use 600x600 sizes images.')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(300x300)</small></label>
                            <div class="col-md-8">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="thumbnail" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.')}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Provider')}}</label>
                            <div class="col-md-8">
                                <select class="form-control aiz-selectpicker" name="video_provider" id="video_provider">
                                    <option value="youtube">{{translate('Youtube')}}</option>
                                    <option value="dailymotion">{{translate('Dailymotion')}}</option>
                                    <option value="vimeo">{{translate('Vimeo')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Video Link')}}</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="video_link" placeholder="{{ translate('Video Link') }}">
                                <small class="text-muted">{{translate("Use proper link without extra parameter. Don't use short share link/embeded iframe code.")}}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('PDF Specification')}}</label>
                            <div class="col-md-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="document">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="pdf_specification" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
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
                            <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-md-8">
                                <textarea class="aiz-text-editor" name="description"></textarea>
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

                        
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Price')}}</label>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Price Type')}} </label>
                            <div class="col-md-9">
                                <div class=" mb-4 row">
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="price_type" value="fixed" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Fixed')}}</label>
                                        </div>
                                        <span>{{translate('Select this option if the price is fixed')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="price_type" value="not_fixed" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Not Fixed')}}</label>
                                        </div>
                                        <span>{{translate('Select if the price is variable i.e: $99.99 to $119.99')}}</span>
                                    </div>
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="price_type" value="later" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Will be announced later')}}</label>
                                        </div>
                                        <span>{{translate('Publish price later')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="price_type" value="call" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Use Call for Price Sticker')}}</label>
                                        </div>
                                        <span>{{translate('In the place of price there will be a sticker Call for Price')}}</span>
                                    </div>
                                
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
	                        <label class="col-sm-3 control-label" for="start_date">{{translate('Unit price')}} <span class="text-danger">*</span></label>
	                        <div class="col-sm-9">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control" required>
	                        </div>
	                    </div>
                        <hr style="border-bottom: 1px dashed #e4e5eb;">
                    </div>
<!-- ====================Prepayment================= -->
                    <div class="prepayment">
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Prepayment')}}</label>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Enable Prepayment')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_prepayment" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Price Type')}} </label>
                            <div class="col-md-9">
                                <div class=" mb-4 row">
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="prepayment_type" value="fixed" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Fixed')}}</label>
                                        </div>
                                        <span>{{translate('Select this option if the Prepayment amount is fixed')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="prepayment_type" value="need_for_cod" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Needed only for Cash On Delivery')}}</label>
                                        </div>
                                        <span>{{translate('Select if the Prepayment is only for Cash On Delivery')}}</span>
                                    </div>                           
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
	                        <label class="col-sm-3 control-label" for="start_date">{{translate('Prepay Amount')}} </label>
	                        <div class="col-sm-9">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Prepay Amount') }}" name="prepayment_amount" class="form-control" >
	                        </div>
	                    </div>
                        <hr style="border-bottom: 1px dashed #e4e5eb;">
                    </div>
<!-- ====================Sample Order================= -->
                    <div class="sample-order">
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Sample Order')}}</label>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Enable Sample Order')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_sample_order" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Price Type')}} </label>
                            <div class="col-md-9">
                                <div class=" mb-4 row">
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="sample_price_type" value="fixed" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Fixed')}}</label>
                                        </div>
                                        <span>{{translate('Select this option if the sample price is fixed')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="sample_price_type" value="call" onchange="" checked >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Use Call for Sample Price Sticker')}}</label>
                                        </div>
                                        <span>{{translate('In the place of sample price there will be a sticker Call for sample Price')}}</span>
                                    </div>                           
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
	                        <label class="col-sm-3 control-label" for="start_date">{{translate('Sample Price')}} </label>
	                        <div class="col-sm-9">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Sample Price') }}" name="sample_price" class="form-control" required>
	                        </div>
	                    </div>
                        <div class="form-group row">
	                        <label class="col-sm-3 control-label" for="start_date">{{translate('Sample Making & Delivery Time')}} </label>
	                        <div class="col-sm-9">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('10 Days') }}" name="delivery_day" class="form-control" required>
	                        </div>
	                    </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Prepayment Needed for Sample')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_prepayment_nedded" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
	                        <label class="col-sm-3 control-label" for="start_date">{{translate('Prepay Amount for Sample')}} </label>
	                        <div class="col-sm-9">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('100') }}" name="prepayment_amount" class="form-control" required>
	                        </div>
	                    </div>
                        <hr style="border-bottom: 1px dashed #e4e5eb;">
                    </div>
<!-- ====================Wholesale================= -->
                    <div class="wholesale">
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Wholesale')}}</label>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Add Wholesale Price')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="add_wholesale_price" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Show Lead Time')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="show_lead_time" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-from-label">{{translate('Wholesale Prices')}}</label>
                            </div>
                           
                            <div class="col-md-9">
                                <div class="row m-0 p-0">
                                    <div class="col-md-2 p-1">
                                        <input type="text" class="form-control" name="wholesale_min_qty[]" placeholder="{{ translate('Min Quantity') }}" >
                                    </div>
                                    <div class="col-md-2 p-1">
                                        <input type="text" class="form-control" name="wholesale_max_qty[]" placeholder="{{ translate('Max Quantity') }}" >
                                    </div>
                                    <div class="col-md-4 p-1">
                                        <input type="text" class="form-control" name="wholesale_price[]" placeholder="{{ translate('Price') }}" >
                                    </div>
                                    <div class="col-md-2 p-1 m-0">
                                        <input type="text" class="form-control" name="wholesale_lead_time[]" placeholder="{{ translate('Lead Time') }}" >
                                    </div>
                                    <div class="col-md-2 p-1">
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="" title="{{ translate('Delete') }}">
                                            <i class="las la-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="row m-0 p-0">
                                    <div class="col-md-2 p-1">
                                        <input type="text" class="form-control" name="wholesale_min_qty[]" placeholder="{{ translate('Min Quantity') }}" >
                                    </div>
                                    <div class="col-md-2 p-1">
                                        <input type="text" class="form-control" name="wholesale_max_qty[]" placeholder="{{ translate('Max Quantity') }}" >
                                    </div>
                                    <div class="col-md-4 p-1">
                                        <input type="text" class="form-control" name="wholesale_price[]" placeholder="{{ translate('Price') }}" >
                                    </div>
                                    <div class="col-md-2 p-1 m-0">
                                        <input type="text" class="form-control" name="wholesale_lead_time[]" placeholder="{{ translate('Lead Time') }}" >
                                    </div>
                                    <div class="col-md-2 p-1">
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="" title="{{ translate('Delete') }}">
                                            <i class="las la-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-4">
                            <div class="col-md-3"></div>
                            <div class="fq_bought_select_product_div col-md-9">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add More') }}</span>
                                </button>
                            </div>
                        </div>
                        
                        <hr style="border-bottom: 1px dashed #e4e5eb;">
                    </div>
<!-- ====================Discount Settings================= -->
                    <div class="discount-settings">
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Discount Settings')}}</label>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('Discount Date Range')}}</label>
                            <div class="col-sm-9">
                              <input type="text" class="form-control aiz-date-range" name="date_range" placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('Discount')}}</label>
                            <div class="col-sm-9">
                                <div class="form-row">
                                    <div class="form-group col-md-9">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Discount Amount') }}" name="discount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control aiz-selectpicker" name="discount_type">
                                            <option value="flat">{{translate('Flat')}}</option>
                                            <option value="percent">{{translate('Percent')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Use Advance Discount Option')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_Advance_discount" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('Discount On Preorder Periods')}}</label>
                            <div class="col-sm-9">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('discount') }}" name="discount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('discount') }}" name="discount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('discount') }}" name="discount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control aiz-selectpicker" name="discount_type">
                                            <option value="amount">{{translate('Flat')}}</option>
                                            <option value="percent">{{translate('Percent')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="fq_bought_select_product_div col-md-9">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add More') }}</span>
                                </button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('After Preorder Period Discount')}}</label>
                            <div class="col-sm-9">
                                <div class="form-row">
                                    <div class="form-group col-md-9">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Amount') }}" name="after_preorder_discount_amount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control aiz-selectpicker" name="after_preorder_discount_type">
                                            <option value="amount">{{translate('Flat')}}</option>
                                            <option value="percent">{{translate('Percent')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('Discount For Direct Purchase')}}</label>
                            <div class="col-sm-9">
                                <div class="form-row">
                                    <div class="form-group col-md-9">
                                        <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Amount') }}" name="direct_purchase_discount_amount" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <select class="form-control aiz-selectpicker" name="direct_purchase_discount_type[]">
                                            <option value="amount">{{translate('Flat')}}</option>
                                            <option value="percent">{{translate('Percent')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <hr style="border-bottom: 1px dashed #e4e5eb;">
                    </div>
<!-- ====================Coupons================= -->
                    <div class="coupons">
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Coupons')}}</label>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Use Coupon For This Product')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_coupon" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Show Other Counpons For This Seller')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="show_seller_coupon" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 control-label" for="start_date">{{translate('Coupon Code')}}</label>
                            <div class="col-sm-9">
                                <div class="row">
                                    <div class="form-group col-md-9">
                                        <input type="text"  placeholder="{{ translate('Coupon Code') }}" name="coupon_code" class="form-control" >
                                    </div>
                                    <div class="form-group col-md-3">
                                        <button class="btn btn-secondary btn-block">{{translate('Generate ')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Coupon Date Range')}}</label>
                            <div class="col-md-9">
                               <div class="row">
                                <label class="col-sm-3 aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_published" value="1">
                                    <span></span>
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control aiz-date-range" name="coupon_date_range" placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                                  </div>
                               </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Use Advanced Coupon Option')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_Advance_coupon" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Coupon Type')}} </label>
                            <div class="col-md-9">
                                <div class=" mb-4 row">
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="coupon_type" value="for_this_order" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('For this order')}}</label>
                                        </div>
                                        <span>{{translate('Use for Direct Discount on this order')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="coupon_type" value="free_delivery" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Free Delivery')}}</label>
                                        </div>
                                        <span>{{translate('Coupon Code will only provide Free Delivery')}}</span>
                                    </div>
                                    <div class="col-md-4 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="coupon_type" value="for_seller_store" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('For Seller Store')}}</label>
                                        </div>
                                        <span>{{translate('Use for other orders of this seller after final order')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="coupon_type" value="assign_coupon" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Assign Coupons from System')}}</label>
                                        </div>
                                        <span>{{translate('Assign a coupon code from the system')}}</span>
                                    </div>
                                    <div class="col-md-6 radio mar-btm mt-2 align-items-center">
                                        <div>
                                            <input id="fq_bought_select_products" type="radio" name="coupon_type" value="other_benefits" onchange=""  >
                                        <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Other Benefits')}}</label>
                                        </div>
                                        <span>{{translate('Specify the benefits you want to give to customers')}}</span>
                                    </div>
                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Coupon Benefits')}}</label>
                            <div class="col-md-9 d-flex justify-content-between">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check1" name="coupon_benefits[]" value="gift" >
                                    <label class="form-check-label" for="check1">{{translate('Gift')}}</label>
                                  </div>
                                  <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check2" name="coupon_benefits[]" value="service">
                                    <label class="form-check-label" for="check2">{{translate('Service')}}</label>
                                  </div>
                                  <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="check2" name="coupon_benefits[]" value="customization">
                                    <label class="form-check-label" for="check2">{{translate('Customization')}}</label>
                                  </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3 " ></div>
                            <div class="col-md-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="document">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="coupon_instructions" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
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
                                <input type="text" class="form-control" name="meta_title" placeholder="{{ translate('Meta Title') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-md-8">
                                <textarea name="meta_description" rows="8" class="form-control"></textarea>
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
                                    <input type="hidden" name="meta_image" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                {{-- Right side --}}
                
<!-- ====================Product Settings================= -->

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 text-orange">
                            {{translate('Product Settings')}}
                        </h5>
                    </div>

                    <div class="card-body">
                       
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Published')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_published" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Featured')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_featured" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label text-orange">{{translate('Show In Homepgae')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_show_on_homepage" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-12 col-from-label text-orange fw-bold"><b>{{translate('Preorder Campaign')}}</b></label>
                            <div class="col-md-12">
                                <select class="form-control aiz-selectpicker" name="campaign" id="campaign" data-live-search="true">
                                    <option value="0">{{ translate('Select One') }}</option>
                                    <option value="1">{{ translate('Select First') }}</option>
                                    <option value="2">{{ translate('Select Second') }}</option>
                                </select>
                            </div>
                        </div>
                       
                    </div>
                </div>
<!-- ====================Refundable================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">
                            {{translate('Refund')}}
                        </h5>
                    </div>

                    <div class="card-body">
                       
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Refundable')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_refundable" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <div class="form-check col-md-12 ml-3">
                                
                                <input class="form-check-input" type="checkbox" name="show_refund_note" value="1" id="flexCheckChecked" checked>
                               
                                <label class="form-check-label" for="flexCheckChecked">
                                    <b>{{translate('Show notes in refund section in product description page')}}</b> 
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-check-label" for="flexCheckChecked">
                                    {{translate('Notes (Add from preset)')}} 
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="fq_bought_select_product_div col-md-12">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add New Preset') }}</span>
                                </button>
                            </div>
                        </div>
                       
                    </div>
                </div>
<!-- ====================Club Point================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">
                            {{translate('Club Point')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="col-from-label">{{translate('Set club point for this product')}}</label>
                            </div>
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="club_point" placeholder="{{ translate('Club Point') }}" onchange="update_sku()" required>
                            </div>
                        </div>
                    </div>
                </div>
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
                            <label class="col-md-6 col-from-label">{{translate('Cash On Delivery')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="cod">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Free Shipping')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="free">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label ">{{translate('Flat Rate')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="flat">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label ">{{translate('Is Product Quantity Multiply')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="radio" name="shipping_type" value="multiply">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <h6 class="fw-bold"> {{translate('Estimated Shipping Time')}}</h6>
                        <div class="form-group mb-3">
                            <label for="name">
                                {{translate('Shipping Days')}}
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="est_shipping_days" min="1" step="1" placeholder="{{translate('Shipping Days')}}">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend">{{translate('Days')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-md-12 ml-3">
                                
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked>
                               
                                <label class="form-check-label" for="flexCheckChecked">
                                   <b> {{translate('Show estimated shipping time in product description page')}}</b> 
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class=" form-check col-md-12 ml-3">
                                
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked>
                               
                                <label class="form-check-label" for="flexCheckChecked">
                                    <b>{{translate('Show notes in shipping time section')}} </b>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-check-label fw-bold" for="flexCheckChecked">
                                    <b>{{translate('Notes (Add from preset)')}} </b>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="fq_bought_select_product_div col-md-12">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add New Preset') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
<!-- ====================Cash On Delivery================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 text-orange">
                            {{translate('Cash On Delivery')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label">{{translate('Cash on delivery available')}}</label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_cod" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-md-12 ml-3">
                                
                                <input class="form-check-input" type="checkbox" value="1"   name="prepayment_needed">
                               
                                <label class="form-check-label text-orange" for="flexCheckChecked">
                                   <b> {{translate('Prepayment needed for cash on delivery')}}</b> 
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="form-check col-md-12 ml-3">
                                
                                <input class="form-check-input" type="checkbox" value="1"  name="show_cod_note">
                               
                                <label class="form-check-label" for="flexCheckChecked">
                                   <b> {{translate('Show note in cash on delivery section in product description page')}}</b> 
                                </label>
                            </div>
                        </div>
                       
                      
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="form-check-label fw-bold" for="flexCheckChecked">
                                    <b>{{translate('Notes (Add from preset)')}} </b>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="fq_bought_select_product_div col-md-12">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add New Preset') }}</span>
                                </button>
                            </div>
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
                        <label for="name">
                            {{$tax->name}}
                            <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                        </label>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <input type="number"  min="0" value="0" step="1" placeholder="{{ translate('Tax') }}" name="tax_amount[]" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <select class="form-control aiz-selectpicker" name="tax_type[]">
                                    <option value="amount">{{translate('Flat')}}</option>
                                    <option value="percent">{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
<!-- ====================Stock & Order Display Settings================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 text-orange">
                            {{translate('Stock & Order Display Settings')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-6 col-from-label"><b>{{translate('Stock Visibility State')}}</b></label>
                            <div class="col-md-6">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="is_stock_visibility" value="1">
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stock_visibility_state" id="stock_visibility_state1">
                                    <label class="form-check-label" for="stock_visibility_state1">
                                        {{translate('All Time')}}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stock_visibility_state" id="stock_visibility_state2" >
                                    <label class="form-check-label" for="stock_visibility_state2">
                                        {{translate('After Product became available')}}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stock_visibility_state" id="stock_visibility_state2" >
                                    <label class="form-check-label" for="stock_visibility_state2">
                                        {{translate('Show only In stock or Out of Stock')}}
                                    </label>
                                </div>

                                <p class="fw-bold mt-4"> <b>{{translate('Current Stock')}}</b></p>
                                <div class="form-group row">
                                    <label class="col-md-4 col-from-label">{{translate('stock')}}</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="current_stock" placeholder="{{ translate('10') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-6 col-from-label"><b>{{translate('Low Stock Quantity Warning')}}</b></label>
                                    <div class="col-md-6 d-flex">
                                        <label class="aiz-switch aiz-switch-success mb-0  ">
                                            <input type="checkbox" name="is_low_stock_warning" value="1" class="mb-0">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-from-label">{{translate('Stcok')}}</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="low_stock_stock" placeholder="{{ translate('10') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row d-flex justify-content-between align-items-center">
                                    <label class="col-md-6 col-from-label"><b>{{translate('Custom Order Display')}}</b></label>
                                    <div class="col-md-6 ms-auto">
                                        <label class="aiz-switch aiz-switch-success mb-0 ms-auto">
                                            <input type="checkbox" name="is_custom_order_show" value="1" class="ms-auto">
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-from-label text-orange">{{translate('Preorder Number')}}</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="preorder_quantity" placeholder="{{ translate('10') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 col-from-label text-orange">{{translate('Final Order Number')}}</label>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" name="final_order_quantity" placeholder="{{ translate('10') }}" required>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
<!-- ====================More Products to Preorder================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 text-orange">
                            {{translate('More Products to Preorder')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <p>{{translate('This is a customised section in product description page where you can select end category or selected products as More Products to Preorder.')}}</p>
                        <label  class="fs-14 fw-700 mb-0 mb-4">{{translate('Select Pre Order Products')}}</label>

                        <table class="table mb-0">
                            <tbody>
                                @foreach(\App\Models\Product::take(5)->get() as $product)
                                    <tr class="remove-parent">
                                    
                                        <td class="w-80px pl-0" style="vertical-align: middle;">
                                            <p class="d-block size-48px">
                                                <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ translate('Image')}}"
                                                    class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                            </p>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <p class="d-block fs-13  hov-text-primary mb-1 text-dark" title="{{ translate('Product Name') }}">
                                                {{$product->name}}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <b>{{single_price($product->unit_price)}}</b>
                                                </div>
                                                <div>
                                                    {{$product->categories->first()->name}}
                                                </div>
                                            </div>
                                        </td>
                                 
                                        <td class="text-right pr-0" style="vertical-align: middle;">
                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                <i class="las la-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="form-group row mt-4">
                            <div class="fq_bought_select_product_div col-md-12">

                                <div id="selected-fq-bought-products">

                                </div>

                                <button
                                    type="button"
                                    class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                    onclick="()">
                                    <i class="las la-plus"></i>
                                    <span class="ml-2">{{ translate('Add More') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
<!-- ====================Frequently Bought Products================= -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6 text-orange">
                            {{translate('Frequently Bought Products')}}
                        </h5>
                    </div>

                    <div class="card-body">
                        <p>{{translate('This is a customised section in product description page where you can select end category or selected products as More Products to Preorder.')}}</p>
                        
                        <div class="w-100">
                            <div class="d-flex mb-4">
                                <div class="radio mar-btm mr-5 d-flex align-items-center">
                                    <input id="fq_bought_select_products" type="radio" name="frequently_bought_selection_type" value="product" onchange="fq_bought_product_selection_type()" checked >
                                    <label for="fq_bought_select_products" class="fs-14 fw-700 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="fq_bought_select_category" type="radio" name="frequently_bought_selection_type" value="category" onchange="fq_bought_product_selection_type()">
                                    <label for="fq_bought_select_category" class="fs-14 fw-700 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>

                                    <div class="fq_bought_select_product_div">

                                        <div id="selected-fq-bought-products">

                                        </div>

                                        <button
                                            type="button"
                                            class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                            onclick="showFqBoughtProductModal()">
                                            <i class="las la-plus"></i>
                                            <span class="ml-2">{{ translate('Add More') }}</span>
                                        </button>
                                    </div>

                                    {{-- Select Category for Frequently Bought Product --}}
                                    <div class="fq_bought_select_category_div d-none">
                                        <div class="form-group row">
                                            <label class="col-md-2 col-from-label">{{translate('Category')}}</label>
                                            <div class="col-md-10">
                                                <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Select a Category')}}" name="fq_bought_product_category_id" data-live-search="true" required>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
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
            <div class="col-12">
                <div class="btn-toolbar float-right mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="Third group">
                        <button type="submit" name="button" value="unpublish" class="btn btn-primary action-btn">{{ translate('Save & Unpublish') }}</button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Second group">
                        <button type="submit" name="button" value="publish" class="btn btn-success action-btn">{{ translate('Save & Publish') }}</button>
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
@endsection
@section('script')

<script type="text/javascript">

$(document).ready(function() {

        var main_id = '{{ old("category_id") }}';
        var selected_ids = [];
        @if(old("category_ids"))
            selected_ids = @json(old("category_ids"));
        @endif
        for (let i = 0; i < selected_ids.length; i++) {
            const element = selected_ids[i];
            $('#treeview input:checkbox#'+element).prop('checked',true);
            $('#treeview input:checkbox#'+element).parents( "ul" ).css( "display", "block" );
            $('#treeview input:checkbox#'+element).parents( "li" ).children('.las').removeClass( "la-plus" ).addClass('la-minus');
        }

        if(main_id){
            $('#treeview input:radio[value='+main_id+']').prop('checked',true);
        }

        $('#treeview input:checkbox').on("click", function (){
            let $this = $(this);
            if ($this.prop('checked') && ($('#treeview input:radio:checked').length == 0)) {
                let val = $this.val();
                $('#treeview input:radio[value='+val+']').prop('checked',true);
            }
        });
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

    $("[name=shipping_type]").on("change", function (){
        $(".flat_rate_shipping_div").hide();

        if($(this).val() == 'flat_rate'){
            $(".flat_rate_shipping_div").show();
        }

    });

    function add_more_customer_choice_option(i, name){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:"POST",
            url:'{{ route('products.add-more-choice-option') }}',
            data:{
               attribute_id: i
            },
            success: function(data) {
                var obj = JSON.parse(data);
                $('#customer_choice_options').append('\
                <div class="form-group row">\
                    <div class="col-md-3">\
                        <input type="hidden" name="choice_no[]" value="'+i+'">\
                        <input type="text" class="form-control" name="choice[]" value="'+name+'" placeholder="{{ translate('Choice Title') }}" readonly>\
                    </div>\
                    <div class="col-md-8">\
                        <select class="form-control aiz-selectpicker attribute_choice" data-live-search="true" name="choice_options_'+ i +'[]" data-selected-text-format="count" multiple>\
                            '+obj+'\
                        </select>\
                    </div>\
                </div>');
                AIZ.plugins.bootstrapSelect('refresh');
           }
       });


    }

    $('input[name="colors_active"]').on('change', function() {
        if(!$('input[name="colors_active"]').is(':checked')) {
            $('#colors').prop('disabled', true);
            AIZ.plugins.bootstrapSelect('refresh');
        }
        else {
            $('#colors').prop('disabled', false);
            AIZ.plugins.bootstrapSelect('refresh');
        }
        update_sku();
    });

    $(document).on("change", ".attribute_choice",function() {
        update_sku();
    });

    $('#colors').on('change', function() {
        update_sku();
    });

    $('input[name="unit_price"]').on('keyup', function() {
        update_sku();
    });

    $('input[name="name"]').on('keyup', function() {
        update_sku();
    });

    function delete_row(em){
        $(em).closest('.form-group row').remove();
        update_sku();
    }

    function delete_variant(em){
        $(em).closest('.variant').remove();
    }

    function update_sku(){
        $.ajax({
           type:"POST",
           url:'{{ route('products.sku_combination') }}',
           data:$('#choice_form').serialize(),
           success: function(data) {
                $('#sku_combination').html(data);
                AIZ.uploader.previewGenerate();
                AIZ.plugins.sectionFooTable('#sku_combination');
                if (data.trim().length > 1) {
                   $('#show-hide-div').hide();
                }
                else {
                    $('#show-hide-div').show();
                }
           }
       });
    }

    $('#choice_attributes').on('change', function() {
        $('#customer_choice_options').html(null);
        $.each($("#choice_attributes option:selected"), function(){
            add_more_customer_choice_option($(this).val(), $(this).text());
        });

        update_sku();
    });

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
        var searchKey = $('input[name=search_keyword]').val();
        var fqBroughCategory = $('select[name=fq_brough_category]').val();
        $.post('{{ route('product.search') }}', { _token: AIZ.data.csrf, product_id: null, search_key:searchKey, category:fqBroughCategory, product_type:"physical" }, function(data){
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

        $.post('{{ route('get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#fq-bought-product-select-modal').modal('hide');
            $('#selected-fq-bought-products').html(data);
            AIZ.plugins.sectionFooTable('#selected-fq-bought-products');
        });
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

</script>

@endsection
