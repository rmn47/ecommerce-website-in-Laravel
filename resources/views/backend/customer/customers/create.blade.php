@extends('backend.layouts.app')

@section('content')

@if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
    <div class="alert alert-info d-flex align-items-center">
        {{ translate('You need to configure SMTP correctly to add Customer by email.') }}
        <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
    </div>
@endif

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">
                {{ isset($user) ? (isset($wholeseller) ? translate('Edit Wholeseller Information') : translate('Edit Customer Information')) : (isset($wholeseller) ? translate('Wholeseller Information') : translate('Customer Information')) }}
            </h5>
        </div>
        
        <form action="{{ isset($user) ? (isset($wholeseller) ? route('wholesellers.update', $user->id) : route('customers.update', $user->id)) : (isset($wholeseller) ? route('wholesellers.store') : route('customers.store')) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="user_type">
                        {{translate('User Type')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <select name="user_type" class="form-control @if($errors->has('user_type')) is-invalid @endif" required>
                            <option value="customer" {{ (isset($user) ? $user->user_type : old('user_type')) == 'customer' ? 'selected' : '' }}>{{ translate('Customer') }}</option>
                            @if(isset($wholeseller) || (isset($user) && $user->is_wholeseller === 1))
                                <option value="customer" {{ (isset($user) ? $user->user_type : old('user_type')) === 'customer' && (isset($wholeseller) || $user->is_wholeseller === 1) ? 'selected' : '' }}>{{ translate('Wholeseller') }}</option>
                            @endif
                            <option value="seller" {{ (isset($user) ? $user->user_type : old('user_type')) == 'seller' ? 'selected' : '' }}>{{ translate('Seller') }}</option>
                        </select>
                        @if ($errors->has('user_type'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('user_type') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="name">
                        {{translate('Name')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @if($errors->has('name')) is-invalid @endif" name="name" value="{{ isset($user) ? $user->name : old('name') }}" placeholder="{{ translate('Name') }}" required>
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="phone">
                        {{translate('Phone')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="tel" id="phone-code" class="form-control @if($errors->has('phone')) is-invalid @endif" value="{{ isset($user) ? $user->phone : old('phone') }}" placeholder="{{ translate('Phone') }}" name="phone" required>
                        @if ($errors->has('phone'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="email">
                        {{translate('Email')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @if($errors->has('email')) is-invalid @endif" value="{{ isset($user) ? $user->email : old('email') }}" placeholder="{{ translate('Email') }}" name="email" required>
                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="address">
                        {{translate('Address')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <textarea class="form-control @if($errors->has('address')) is-invalid @endif" name="address" placeholder="{{ translate('Address') }}" required>{{ isset($user) ? $user->address : old('address') }}</textarea>
                        @if ($errors->has('address'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                @if(isset($wholeseller) || (isset($user) && $user->is_wholeseller === 1))
                    <div class="form-group row">
                        <label class="col-sm-2 col-from-label" for="gst_no">
                            {{translate('GST Number')}}
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @if($errors->has('gst_no')) is-invalid @endif" name="gst_no" value="{{ isset($user) ? $user->gst_no : old('gst_no') }}" placeholder="{{ translate('GST Number') }}">
                            @if ($errors->has('gst_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('gst_no') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-from-label" for="drug_license_no">
                            {{translate('Drug License Number')}}
                        </label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control @if($errors->has('drug_license_no')) is-invalid @endif" name="drug_license_no" value="{{ isset($user) ? $user->drug_license_no : old('drug_license_no') }}" placeholder="{{ translate('Drug License Number') }}">
                            @if ($errors->has('drug_license_no'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('drug_license_no') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="is_wholeseller" value="1">
                @endif

                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="photo">
                        {{translate('Photo')}}
                    </label>
                    <div class="col-sm-10">
                        @if(isset($user) && $user->avatar_original) <!-- Changed from photo to avatar_original -->
                            <img src="{{ asset('storage/'.$user->avatar_original) }}" alt="User Photo" style="max-width: 100px; margin-bottom: 10px;">
                        @endif
                        <input type="file" class="form-control @if($errors->has('photo')) is-invalid @endif" name="photo" accept="image/*">
                        @if ($errors->has('photo'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('photo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group mb-3 text-right">
                    <button type="submit" class="btn btn-primary">{{ isset($user) ? translate('Update') : translate('Save') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        // Initialize phone input with intlTelInput
        var input = document.querySelector("#phone-code");
        
        var iti = intlTelInput(input, {
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            initialCountry: "in", // Set India as default
            separateDialCode: false, // Disable separate dial code display
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                return "Enter phone number"; // Custom placeholder
            },
            formatOnDisplay: false // Prevent auto-formatting
        });

        // Hardcode +91 prefix for display only
        function setPhonePrefix() {
            var rawNumber = input.value.trim();
            if (rawNumber && !rawNumber.startsWith('+91')) {
                input.value = '+91' + rawNumber;
            } else if (!rawNumber) {
                input.value = '+91';
            }
        }

        // Set initial value with +91
        @if(isset($user) && $user->phone)
            input.value = '+91{{ $user->phone }}';
        @else
            input.value = '+91';
        @endif

        // On input, ensure +91 stays but isn't part of submitted data
        input.addEventListener('input', function(e) {
            var value = e.target.value.replace('+91', '').trim();
            if (value) {
                e.target.value = '+91' + value;
            } else {
                e.target.value = '+91';
            }
        });

        // Before form submission, strip +91 from the value sent to the server
        document.querySelector('form').addEventListener('submit', function(e) {
            var rawValue = input.value.replace('+91', '').trim();
            input.value = rawValue; // Only the number without +91 goes to the server
        });
    </script> 
@endsection