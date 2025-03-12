<div class="carousel-box m-4">
    <div class="aiz-card-box hov-shadow-md my-2 has-transition hov-scale-img ">

        <div class="position-relative">
            <!-- Make the container relative -->
            <a href="{{route('preorder-product.details', $product->product_slug)}}" class="d-block">
                <img class="img-fit lazyload mx-auto h-140px h-md-190px has-transition"
                    src="{{ uploaded_asset($product->thumbnail) }}"
                    data-src="{{ uploaded_asset($product->thumbnail) }}"
                    alt="{{ $product->product_name }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
            </a>

            <!-- Top-left label -->
            <div class="position-absolute top-0 left-0  text-white px-2 py-1 "
                style="background-color: #FF6002; color: white;">
                <small><i class="las la-clock fs-16"></i></small>
            </div>

            <!-- Bottom-left label -->
            <div class="position-absolute bottom-0 left-0  text-white px-2 py-1 "
                style="background-color: #85B567; color: white;">
                <small class="fs-10">{{$product->discount_type == 'flat' ? single_price($product->discount) : $product->discount.'%'}} {{translate('Discount ')}}</small>
            </div>
            
        </div>

    </div>
</div>
