@extends('seller.layouts.app')
@section('panel_content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-600 mb-0">{{ translate('Preorder Instructions') }}</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('seller.preorder-instruction-update') }}" method="POST">
                    @csrf
                    @php $shop = auth()->user()->shop; @endphp

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Preorder Request Instructions') }}</label>
                        <div class="col-md-8">
                            <textarea name="preorder_request_instruction" rows="4" class="aiz-text-editor form-control" 
                                data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'>
                                {{ $shop->preorder_request_instruction }}
                            </textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Image For Payment QR Code') }}</label>
                        <div class="col-md-8">
                            <div class="input-group " data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="image_for_payment_qrcode" value="{{ $shop->image_for_payment_qrcode }}" class="selected-files">
                            </div>
                            <div class="file-preview box"></div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label">{{ translate('Payment Instructions') }}</label>
                        <div class="col-md-8">
                            <textarea name="pre_payment_instruction" rows="4" class="aiz-text-editor form-control" 
                                data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'>
                                {{ $shop->pre_payment_instruction }}
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