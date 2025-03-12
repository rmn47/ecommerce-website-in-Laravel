@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Edit Email Template') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Edit Email Template') }}</h5>
            </div>
            <div class="card-body">
                <form class="p-4" action="{{ route('email-templates.update', $emailTemplate->id) }}" method="POST"
                    enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="email_type">{{ translate('Email Type') }}</label>
                        <div class="col-md-9">
                            <input type="text" id="email_type" value="{{ translate($emailTemplate->email_type) }}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="subject">{{ translate('Subject') }}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('Email Subject') }}" id="subject"
                                name="subject" value="{{ $emailTemplate->subject }}"
                                class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="default_text">{{ translate('Default Text') }}</label>
                        <div class="col-md-9">
                            @if($emailTemplate->is_dafault_text_editable == 1)
                                <textarea type="text" 
                                    name="default_text" 
                                    id="default_text" 
                                    class="aiz-text-editor" 
                                    data-min-height="350px" 
                                    data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["insert", ["link"]],["view", ["undo","redo"]]]'
                                    placeholder="{{ translate('Default Text') }}" required>
                                    {{ $emailTemplate->default_text }}
                                </textarea>
                                <small class="form-text text-danger">{{ translate('N.B : Do Not Change The Variables Like') }} [[ ____ ]]</small>
                            @else
                                <small class="form-text fs-13 text-danger">{{ translate('Content is not editable for this Email.') }}</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
