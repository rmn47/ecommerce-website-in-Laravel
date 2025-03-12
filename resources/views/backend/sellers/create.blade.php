@extends('backend.layouts.app')

@section('content')
@if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
    <div class="alert alert-info d-flex align-items-center">
        {{ translate('You need to configure SMTP correctly to add Seller.') }}
        <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
    </div>
@endif

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Seller')}}</h5>
</div>

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Seller Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sellers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="name">
                        {{translate('Name')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ translate('Name') }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="email">
                        {{ translate('Email') }} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="{{ translate('Email') }}" name="email" required>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="shop_name">{{ translate('Shop Name') }}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('shop_name') is-invalid @enderror" value="{{ old('shop_name') }}" placeholder="{{ translate('Shop Name') }}" name="shop_name">
                        @error('shop_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="address">{{ translate('Address') }}</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="{{ translate('Address') }}" name="address">
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="gst_no">
                        {{translate('GST Number')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('gst_no') is-invalid @enderror" name="gst_no" value="{{ old('gst_no') }}" placeholder="{{ translate('GST Number') }}" required>
                        @error('gst_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="drug_license_no">
                        {{translate('Drug License No')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('drug_license_no') is-invalid @enderror" name="drug_license_no" value="{{ old('drug_license_no') }}" placeholder="{{ translate('Drug License Number') }}" required>
                        @error('drug_license_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="pan_card">
                        {{translate('PAN Card')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('pan_card') is-invalid @enderror" name="pan_card" value="{{ old('pan_card') }}" placeholder="{{ translate('PAN Card') }}" required>
                        @error('pan_card')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="phone">
                        {{translate('Phone Number')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="{{ translate('Phone Number') }}" required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="avatar_original">
                        {{translate('License')}}
                    </label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control @error('avatar_original') is-invalid @enderror" name="avatar_original" accept="image/jpeg,image/png,image/jpg">
                        @error('avatar_original')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection