@extends('backend.layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        {{-- seller Commission --}}
        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">{{ translate('Preorder Seller Commission') }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('business_settings.update') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <label class="col-md-4 col-from-label">{{translate('Preorder Product for Seller')}}</label>
                        <div class="col-md-8">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input type="checkbox" onchange="updateSettings(this, 'seller_preorder_product')" @if( get_setting('seller_preorder_product') == 1) checked @endif>
                                <span></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-4 col-from-label">{{translate('Preorder Seller Commission')}}</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="preorder_seller_commission">
                            <div class="input-group">
                                <input type="number" lang="en" min="0" step="0.01" value="{{ get_setting('preorder_seller_commission') }}" placeholder="{{translate('Preorder Seller Commission')}}" name="preorder_seller_commission" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Update Button -->
                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">{{ translate('PreOrder Settings') }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('business_settings.update') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Image For Product Marketing') }}</label>
                        <div class="col-md-8">
                            <div class="input-group " data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="types[]" value="image_for_faq_advertisement">
                                <input type="hidden" name="image_for_faq_advertisement" value="{{ get_setting('image_for_faq_advertisement') }}" class="selected-files">
                            </div>
                            <div class="file-preview box"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Preorder Flat Rate Shipping') }}</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="preorder_flat_rate_shipping">
                            <input type="number" class="form-control" name="preorder_flat_rate_shipping" value="{{ get_setting('preorder_flat_rate_shipping') }}">
                        </div>
                    </div>
                    
                    <!-- Update Button -->
                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- Preorder Request --}}
        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">{{ translate('Preorder Instructions') }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('business_settings.update') }}" method="POST">
                    @csrf
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Preorder Request Instructions') }}</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="preorder_request_instruction">
                            <textarea name="preorder_request_instruction" rows="4" class="aiz-text-editor form-control" 
                                data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'>
                                {{ get_setting('preorder_request_instruction') }}
                            </textarea>
                        </div>
                    </div>
                    
                    <!-- Update Button -->
                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- payment info --}}
        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">{{ translate('Payment Instructions') }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('business_settings.update') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Image For Payment QR Code') }}</label>
                        <div class="col-md-8">
                            <div class="input-group " data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="types[]" value="image_for_payment_qrcode">
                                <input type="hidden" name="image_for_payment_qrcode" value="{{ get_setting('image_for_payment_qrcode') }}" class="selected-files">
                            </div>
                            <div class="file-preview box"></div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Payment Instructions') }}</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="pre_payment_instruction">
                            <textarea name="pre_payment_instruction" rows="4" class="aiz-text-editor form-control" 
                                data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'>
                                {{ get_setting('pre_payment_instruction') }}
                            </textarea>
                        </div>
                    </div>
                    
                    <!-- Update Button -->
                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')
    <script type="text/javascript">
        function updateSettings(el, type) {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }
            var value = ($(el).is(':checked')) ? 1 : 0;
            $.post('{{ route('business_settings.update.activation') }}', {
                _token: '{{ csrf_token() }}',
                type: type,
                value: value
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection