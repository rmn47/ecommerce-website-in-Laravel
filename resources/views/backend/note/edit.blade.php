@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Note Information') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill language-bar">
                    @foreach (get_all_active_language() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3"
                                href="{{ route('note.edit', ['id' => $note->id, 'lang' => $language->code]) }}">
                                <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}" height="11" class="mr-1">
                                <span>{{ $language->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <form class="p-4" action="{{ route('note.update', $note->id) }}" method="POST"
                    enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">{{ translate('Type') }}</label>
                        <div class="col-md-10">
                            <select name="note_type" required class="form-control aiz-selectpicker mb-2 mb-md-0">
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" class="text-uppercase" @selected($type->value == $note->note_type)>
                                        {{ translate($type->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group row">
                        <label class="col-md-2 col-from-label">
                            {{ translate('Description') }} <i class="las la-language text-danger" title="{{ translate('Translatable') }}"></i>
                            <p class="fs-10">({{ translate('Max 900 Character') }})</p>
                        </label>
                        <div class="col-md-10">
                            <textarea name="description" rows="8" class="form-control">{{ $note->description }}</textarea>
                            @error('description')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
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
