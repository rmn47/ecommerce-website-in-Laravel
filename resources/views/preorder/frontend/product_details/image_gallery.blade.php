<div class="z-3 row gutters-10">
    @php
        $photos = $product->images != null ? explode(',', $product->images) : [];
    @endphp
        <!-- Thumbnail Images -->
        <div class="col-2 d-none d-lg-block m-0 p-0">
            <div class="aiz-carousel half-outside-arrow product-gallery-thumb m-0 p-0" data-items='4' data-nav-for='.product-gallery'
                data-focus-select='true' data-arrows='true' data-vertical='true' data-auto-height='true'>
                @foreach ($photos as $key => $photo)
                    <div class="carousel-box c-pointer rounded-0">
                        <img class="lazyload mx-auto ml-2 p-1"
                            src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($photo) }}"
                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';" style="height: 120px; width:180px;">
                    </div>
                @endforeach
            </div>
        </div>
    <!-- Gallery Images -->
    <div class="col-10">
        <div class="aiz-carousel product-gallery arrow-inactive-transparent arrow-lg-none m-0 p-0"
            data-nav-for='.product-gallery-thumb' data-fade='true' data-auto-height='true' data-arrows='true'>
            @foreach ($photos as $key => $photo)
                <div class="carousel-box img-zoom rounded-0">
                    <img class="img-fluid h-auto lazyload mx-auto"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($photo) }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            @endforeach
        </div>
    </div>
</div>
