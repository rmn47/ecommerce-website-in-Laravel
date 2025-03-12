<option value="">{{ translate('Select Product') }}</option>
@foreach ($products as $product)
    <option value="{{ $product->id }}"
        data-content="<img src='{{ uploaded_asset($product->thumbnail_img)}}' class='img-fit size-40px'> {{ $product->getTranslation('name') }} ">
    </option>
@endforeach
