<style>
    .preorder-brand-image {
    width: 63px !important;
    height: 42px !important;
}
</style>
@if($product->brand->slug !== 'no_brand')
<div class="d-flex justify-content-between border mb-4 p-2 mt-2 rounded-2" >
    <div class="ml-3 d-flex" style="margin-bottom: -15px !important">
        <div class="mt-2">
            <a href="{{ route('products.brand', $product->brand->slug) }}" class=" avatar-md mr-2 overflow-hidden  float-left float-lg-none float-xl-left">
                <img class="lazyload h-100 w-100 preorder-brand-image"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->brand->logo) }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
            </a>
        </div>
        <div class="mt-2 ml-3">
            <div> {{translate('Brand')}}  <b>{{ $product->brand->name }}</b></div>
            <div class="preorder-text-secondary"><a href="{{route('products.brand', $product->brand->slug)}}">{{ translate('Products from this brand') }}</a></div>
        </div>
    </div>
</div>
@endif