@extends('seller.layouts.app')

@section('panel_content')

    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Shop Settings')}}
                <span class="ml-3 fs-13">(<a href="{{ route('shop.visit', $shop->slug) }}" class="btn btn-link btn-sm px-0" target="_blank">{{ translate('Visit Shop')}} <i class="las la-arrow-right"></i></a>)</span>
            </h1>
        </div>
      </div>
    </div>

    <!-- Basic Info -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Basic Info') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('seller.shop.update') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                @csrf
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Name') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Shop Name')}}" name="name" value="{{ $shop->name }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Logo') }}</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="logo" value="{{ $shop->logo }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <label>{{ translate('Shop Phone') }} <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Phone')}}" name="phone" value="{{ $shop->phone }}" required>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Shop Address') }} <span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Address')}}" name="address" value="{{ $shop->address }}" required>
                    </div>
                </div>
                @if (get_setting('shipping_type') == 'seller_wise_shipping')
                    <div class="row">
                        <div class="col-md-2">
                            <label>{{ translate('Shipping Cost')}} <span class="text-danger">*</span></label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" lang="en" min="0" class="form-control mb-3" placeholder="{{ translate('Shipping Cost')}}" name="shipping_cost" value="{{ $shop->shipping_cost }}" required>
                        </div>
                    </div>
                @endif 
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Meta Title') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control mb-3" placeholder="{{ translate('Meta Title')}}" name="meta_title" value="{{ $shop->meta_title }}" required>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-2 col-form-label">{{ translate('Meta Description') }}<span class="text-danger text-danger">*</span></label>
                    <div class="col-md-10">
                        <textarea name="meta_description" rows="3" class="form-control mb-3" required>{{ $shop->meta_description }}</textarea>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delivery Boy Pickup Point -->
    @if (addon_is_activated('delivery_boy'))
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Delivery Boy Pickup Point') }}</h5>
            </div>
            <div class="card-body">
                <form class="" action="{{ route('seller.shop.update') }}" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                    @csrf

                    @if (get_setting('google_map') == 1)
                        <div class="row mb-3">
                            <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                            <div id="map"></div>
                            <ul id="geoData">
                                <li style="display: none;">{{ translate('Full Address') }}: <span id="location"></span></li>
                                <li style="display: none;">{{ translate('Postal Code') }}: <span id="postal_code"></span></li>
                                <li style="display: none;">{{ translate('Country') }}: <span id="country"></span></li>
                                <li style="display: none;">{{ translate('Latitude') }}: <span id="lat"></span></li>
                                <li style="display: none;">{{ translate('Longitude') }}: <span id="lon"></span></li>
                            </ul>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname">{{ translate('Longitude') }}</label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="longitude" name="delivery_pickup_longitude" readonly="" value="{{ $shop->delivery_pickup_longitude }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname">{{ translate('Latitude') }}</label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="latitude" name="delivery_pickup_latitude" readonly="" value="{{ $shop->delivery_pickup_latitude }}">
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname">{{ translate('Longitude') }}</label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="longitude" name="delivery_pickup_longitude" value="{{ $shop->delivery_pickup_longitude }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2" id="">
                                <label for="exampleInputuname">{{ translate('Latitude') }}</label>
                            </div>
                            <div class="col-md-10" id="">
                                <input type="text" class="form-control mb-3" id="latitude" name="delivery_pickup_latitude" value="{{ $shop->delivery_pickup_latitude }}">
                            </div>
                        </div>
                    @endif

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Banner Settings -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Banner Settings') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('seller.shop.banner.update') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                @csrf
                <!-- Top Banner -->
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Top Banner') }} (1920x360)</label>
                    <div class="col-md-10">
                        <div class="row p-3 p-md-4 mb-3 mb-md-2rem mx-0" style="border: 1px dashed #e4e5eb;">
                            <div class="col-md-6">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="top_banner_image" value="{{ $shop->top_banner_image }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{ translate('We had to limit height to maintian consistancy. In some device both side of the banner might be cropped for height limitation.') }}</small>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control mb-3" placeholder="{{ translate('Top banner Link')}}" name="top_banner_link" value="{{ $shop->top_banner_link }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Banners -->
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Slider Banners') }} (1500x450)</label>
                    <div class="col-md-10">
                        <div class="shop-slider-target">
                            @php $slider_images = $shop->slider_images; @endphp
                            @if ($slider_images != null)
                                @foreach (json_decode($slider_images, true) as $key => $value)
                                    <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                        <div class="row gutters-5">
                                            <!-- Image -->
                                            <div class="col-md-5">
                                                <div class="form-group mb-md-0">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden" name="slider_images[]" class="selected-files" value="{{ json_decode($slider_images, true)[$key] }}">
                                                    </div>
                                                    <div class="file-preview box sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- link -->
                                            <div class="col-md">
                                                <div class="form-group mb-md-0">
                                                    <input type="text" class="form-control" placeholder="http://" name="slider_links[]" value="{{ isset(json_decode($shop->slider_links, true)[$key]) ? json_decode($shop->slider_links, true)[$key] : '' }}">
                                                </div>
                                            </div>
                                            <!-- remove parent button -->
                                            <div class="col-md-auto">
                                                <div class="form-group mb-md-0">
                                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Add button -->
                        <div class="">
                            <button
                                type="button"
                                class="btn btn-block border hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center" style="background: #fcfcfc;"
                                data-toggle="add-more"
                                data-content='
                                <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                    <div class="row gutters-5">
                                        <!-- Image -->
                                        <div class="col-md-5">
                                            <div class="form-group mb-md-0">
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="slider_images[]" class="selected-files" value="">
                                                </div>
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- link -->
                                        <div class="col-md">
                                            <div class="form-group mb-md-0">
                                                <input type="text" class="form-control" placeholder="http://" name="slider_links[]" value="">
                                            </div>
                                        </div>
                                        <!-- remove parent button -->
                                        <div class="col-md-auto">
                                            <div class="form-group mb-md-0">
                                                <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>'
                                data-target=".shop-slider-target">
                                <i class="las la-2x text-success la-plus-circle"></i>
                                <span class="ml-2">{{ translate('Add New') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banner Full width 1 -->
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Banner Full width 1') }}</label>
                    <div class="col-md-10">
                        <div class="shop-banner-full-width-1">
                            @php $banner_full_width_1_images =  $shop->banner_full_width_1_images; @endphp
                            @if ($banner_full_width_1_images != null)
                                @foreach (json_decode($banner_full_width_1_images, true) as $key => $value)
                                    <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                        <div class="row gutters-5">
                                            <!-- Image -->
                                            <div class="col-md-5">
                                                <div class="form-group mb-md-0">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden" name="banner_full_width_1_images[]" class="selected-files" value="{{ json_decode($banner_full_width_1_images, true)[$key] }}">
                                                    </div>
                                                    <div class="file-preview box sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- link -->
                                            <div class="col-md">
                                                <div class="form-group mb-md-0">
                                                    <input type="text" class="form-control" placeholder="http://" name="banner_full_width_1_links[]" value="{{ isset(json_decode($shop->banner_full_width_1_links, true)[$key]) ? json_decode($shop->banner_full_width_1_links, true)[$key] : '' }}">
                                                </div>
                                            </div>
                                            <!-- remove parent button -->
                                            <div class="col-md-auto">
                                                <div class="form-group mb-md-0">
                                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Add button -->
                        <div class="">
                            <button
                                type="button"
                                class="btn btn-block border hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center" style="background: #fcfcfc;"
                                data-toggle="add-more"
                                data-content='
                                <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                    <div class="row gutters-5">
                                        <!-- Image -->
                                        <div class="col-md-5">
                                            <div class="form-group mb-md-0">
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="banner_full_width_1_images[]" class="selected-files" value="">
                                                </div>
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- link -->
                                        <div class="col-md">
                                            <div class="form-group mb-md-0">
                                                <input type="text" class="form-control" placeholder="http://" name="banner_full_width_1_links[]" value="">
                                            </div>
                                        </div>
                                        <!-- remove parent button -->
                                        <div class="col-md-auto">
                                            <div class="form-group mb-md-0">
                                                <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>'
                                data-target=".shop-banner-full-width-1">
                                <i class="las la-2x text-success la-plus-circle"></i>
                                <span class="ml-2">{{ translate('Add New') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banners half width -->
                @php $banners_half_width_images = $shop->banners_half_width_images; @endphp
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Banners half width') }} ({{ translate('2 Equal Banners') }})</label>
                    <div class="col-md-10">
                        <div class="shop-banners-half-width">
                            @php $banner_full_width_1_images =  $shop->banner_full_width_1_images; @endphp
                            @if ($banners_half_width_images != null)
                                @foreach (json_decode($banners_half_width_images, true) as $key => $value)
                                    <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                        <div class="row gutters-5">
                                            <!-- Image -->
                                            <div class="col-md-5">
                                                <div class="form-group mb-md-0">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden" name="banners_half_width_images[]" class="selected-files" value="{{ json_decode($banners_half_width_images, true)[$key] }}">
                                                    </div>
                                                    <div class="file-preview box sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- link -->
                                            <div class="col-md">
                                                <div class="form-group mb-md-0">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        placeholder="http://" 
                                                        name="banners_half_width_links[]" 
                                                        value="{{ isset(json_decode($shop->banners_half_width_links, true)[$key]) ? json_decode($shop->banners_half_width_links, true)[$key] : '' }}">
                                                </div>
                                            </div>
                                            <!-- remove parent button -->
                                            <div class="col-md-auto">
                                                <div class="form-group mb-md-0">
                                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Add button -->
                        <div class="">
                            <button
                                type="button"
                                class="btn btn-block border hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center" style="background: #fcfcfc;"
                                data-toggle="add-more"
                                data-content='
                                <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                    <div class="row gutters-5">
                                        <!-- Image -->
                                        <div class="col-md-5">
                                            <div class="form-group mb-md-0">
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="banners_half_width_images[]" class="selected-files" value="">
                                                </div>
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- link -->
                                        <div class="col-md">
                                            <div class="form-group mb-md-0">
                                                <input type="text" class="form-control" placeholder="http://" name="banners_half_width_links[]" value="">
                                            </div>
                                        </div>
                                        <!-- remove parent button -->
                                        <div class="col-md-auto">
                                            <div class="form-group mb-md-0">
                                                <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>'
                                data-target=".shop-banners-half-width">
                                <i class="las la-2x text-success la-plus-circle"></i>
                                <span class="ml-2">{{ translate('Add New') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Banner Full width 2 -->
                @php $banner_full_width_2_images = $shop->banner_full_width_2_images; @endphp
                <div class="row mb-3">
                    <label class="col-md-2 col-form-label">{{ translate('Banner Full width 2') }}</label>
                    <div class="col-md-10">
                        <div class="shop-banner-full-width-2">
                            @php $banner_full_width_1_images =  $shop->banner_full_width_1_images; @endphp
                            @if ($banner_full_width_2_images != null)
                                @foreach (json_decode($banner_full_width_2_images, true) as $key => $value)
                                    <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                        <div class="row gutters-5">
                                            <!-- Image -->
                                            <div class="col-md-5">
                                                <div class="form-group mb-md-0">
                                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                        </div>
                                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                        <input type="hidden" name="banner_full_width_2_images[]" class="selected-files" value="{{ json_decode($banner_full_width_2_images, true)[$key] }}">
                                                    </div>
                                                    <div class="file-preview box sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- link -->
                                            <div class="col-md">
                                                <div class="form-group mb-md-0">
                                                    <input type="text" 
                                                        class="form-control" 
                                                        placeholder="http://" 
                                                        name="banner_full_width_2_links[]" 
                                                        value="{{ isset(json_decode($shop->banner_full_width_2_links, true)[$key]) ? json_decode($shop->banner_full_width_2_links, true)[$key] : '' }}">
                                                </div>
                                            </div>
                                            <!-- remove parent button -->
                                            <div class="col-md-auto">
                                                <div class="form-group mb-md-0">
                                                    <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                        <i class="las la-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Add button -->
                        <div class="">
                            <button
                                type="button"
                                class="btn btn-block border hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center" style="background: #fcfcfc;"
                                data-toggle="add-more"
                                data-content='
                                <div class="p-3 p-md-4 mb-3 mb-md-2rem remove-parent" style="border: 1px dashed #e4e5eb;">
                                    <div class="row gutters-5">
                                        <!-- Image -->
                                        <div class="col-md-5">
                                            <div class="form-group mb-md-0">
                                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                                    </div>
                                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                                    <input type="hidden" name="banner_full_width_2_images[]" class="selected-files" value="">
                                                </div>
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- link -->
                                        <div class="col-md">
                                            <div class="form-group mb-md-0">
                                                <input type="text" class="form-control" placeholder="http://" name="banner_full_width_2_links[]" value="">
                                            </div>
                                        </div>
                                        <!-- remove parent button -->
                                        <div class="col-md-auto">
                                            <div class="form-group mb-md-0">
                                                <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>'
                                data-target=".shop-banner-full-width-2">
                                <i class="las la-2x text-success la-plus-circle"></i>
                                <span class="ml-2">{{ translate('Add New') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Social Media Link -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Social Media Link') }}</h5>
        </div>
        <div class="card-body">
            <form class="" action="{{ route('seller.shop.update') }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="shop_id" value="{{ $shop->id }}">
                @csrf
                <div class="form-box-content p-3">
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Facebook') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Facebook')}}" name="facebook" value="{{ $shop->facebook }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Instagram') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Instagram')}}" name="instagram" value="{{ $shop->instagram }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Twitter') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Twitter')}}" name="twitter" value="{{ $shop->twitter }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Google') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Google')}}" name="google" value="{{ $shop->google }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-2 col-form-label">{{ translate('Youtube') }}</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" placeholder="{{ translate('Youtube')}}" name="youtube" value="{{ $shop->youtube }}">
                            <small class="text-muted">{{ translate('Insert link with https ') }}</small>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('script')

    @if (addon_is_activated('delivery_boy') && get_setting('google_map') == 1)
            
    <script>
        function initialize(id_format = '') {
            let default_longtitude = '';
            let default_latitude = '';
            @if (get_setting('google_map_longtitude') != '' && get_setting('google_map_longtitude') != '')
                default_longtitude = {{ get_setting('google_map_longtitude') }};
                default_latitude = {{ get_setting('google_map_latitude') }};
            @endif

            var lat = -33.8688;
            var long = 151.2195;

            if (document.getElementById('latitude').value != '' &&
                document.getElementById('longitude').value != '') {
                lat = parseFloat(document.getElementById('latitude').value);
                long = parseFloat(document.getElementById('longitude').value);
            } else if (default_longtitude != '' &&
                default_latitude != '') {
                lat = default_latitude;
                long = default_longtitude;
            }


            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: lat,
                    lng: long
                },
                zoom: 13
            });

            var myLatlng = new google.maps.LatLng(lat, long);

            var input = document.getElementById(id_format + 'searchInput');
            // console.log(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            var autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.bindTo('bounds', map);

            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                position: myLatlng,
                anchorPoint: new google.maps.Point(0, -29),
                draggable: true,
            });

            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                document.getElementById(id_format + 'latitude').value = event.latLng.lat();
                document.getElementById(id_format + 'longitude').value = event.latLng.lng();
                infowindow.setContent('Latitude: ' + event.latLng.lat() + '<br>Longitude: ' + event.latLng.lng());
                infowindow.open(map, marker);
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                document.getElementById(id_format + 'latitude').value = event.latLng.lat();
                document.getElementById(id_format + 'longitude').value = event.latLng.lng();
                infowindow.setContent('Latitude: ' + event.latLng.lat() + '<br>Longitude: ' + event.latLng.lng());
                infowindow.open(map, marker);
            });

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();

                if (!place.geometry) {
                    window.alert("Autocomplete's returned place contains no geometry");
                    return;
                }

                // If the place has a geometry, then present it on a map.
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                /*
                marker.setIcon(({
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
                */
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);

                var address = '';
                if (place.address_components) {
                    address = [
                        (place.address_components[0] && place.address_components[0].short_name || ''),
                        (place.address_components[1] && place.address_components[1].short_name || ''),
                        (place.address_components[2] && place.address_components[2].short_name || '')
                    ].join(' ');
                }

                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
                infowindow.open(map, marker);

                //Location details
                for (var i = 0; i < place.address_components.length; i++) {
                    if (place.address_components[i].types[0] == 'postal_code') {
                        document.getElementById('postal_code').innerHTML = place.address_components[i].long_name;
                    }
                    if (place.address_components[i].types[0] == 'country') {
                        document.getElementById('country').innerHTML = place.address_components[i].long_name;
                    }
                }
                document.getElementById('location').innerHTML = place.formatted_address;
                document.getElementById(id_format + 'latitude').value = place.geometry.location.lat();
                document.getElementById(id_format + 'longitude').value = place.geometry.location.lng();
            });

        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('MAP_API_KEY') }}&libraries=places&language=en&callback=initialize" async defer></script>

    @endif

@endsection
