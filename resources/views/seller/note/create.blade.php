@extends('seller.layouts.app')
@section('panel_content')
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Note Information') }}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('seller.note.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Type') }}</label>
                            <div class="col-md-9">
                                <select name="note_type" class="form-control aiz-selectpicker mb-2 mb-md-0" required>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}" class="text-uppercase">{{ translate($type->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">
                                {{ translate('Description') }}
                                <p class="fs-10">({{ translate('Max 900 Character') }})</p>
                            </label>
                            <div class="col-md-9">
                                <textarea name="description" rows="8" class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
