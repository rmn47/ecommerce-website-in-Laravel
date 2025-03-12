@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Edit Seller Information')}}</h5>
</div>

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Seller Information')}}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sellers.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PATCH">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $shop->user->name }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="email">{{translate('Email Address')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control" value="{{ $shop->user->email }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="password">{{translate('Password')}}</label>
                    <div class="col-sm-9">
                        <input type="password" placeholder="{{translate('Password')}}" id="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="gst_no">{{translate('GST Number')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('GST Number')}}" id="gst_no" name="gst_no" class="form-control" value="{{ $shop->user->gst_no }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="drug_license_no">{{translate('Drug License No')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Drug License Number')}}" id="drug_license_no" name="drug_license_no" class="form-control" value="{{ $shop->user->drug_license_no }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="pan_card">{{translate('PAN Card')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('PAN Card')}}" id="pan_card" name="pan_card" class="form-control" value="{{ $shop->user->pan_card }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="phone">{{translate('Phone Number')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Phone Number')}}" id="phone" name="phone" class="form-control" value="{{ $shop->user->phone }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="avatar_original">{{translate('License')}}</label>
                    <div class="col-sm-9">
                        <input type="file" name="avatar_original" id="avatar_original" class="form-control" accept="image/jpeg,image/png,image/jpg">
                        @if($shop->user->avatar_original)
                            <div class="mt-2">
                                <img src="{{ asset($shop->user->avatar_original) }}" alt="License" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        @endif
                        @error('avatar_original')
                            <span class="invalid-feedback" role="alert" style="display: block;">
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