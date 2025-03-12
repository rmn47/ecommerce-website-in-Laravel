@if (count($preorder_products) > 0)
    @php
        $featured_bg = get_setting('featured_section_bg_color');
    @endphp
    <section class="mb-2 mb-md-3 mt-2 mt-md-3">
        <div class="container">
            <div class="p-3 p-md-2rem rounded-2 @if(get_setting('featured_section_outline') == 1) border @endif" style="background: {{ $featured_bg != null ? $featured_bg : '#ffffff' }}; border-color: {{ get_setting('featured_section_outline_color') }} !important; padding-bottom: 0 !important;" id="section_preorder_featured_div">
                <!-- Top Section -->
                <div class="d-flex mb-2 mb-md-3 align-items-baseline justify-content-between">
                    <!-- Title -->
                    <h3 class="fs-16 fs-md-20 fw-700 mb-2 mb-sm-0">
                        <span class="">{{ translate('Preorder Featured Products') }}</span>
                    </h3>
                    <!-- Links -->
                    <div class="d-flex">
                        <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_preorder_featured_div')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                        <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_preorder_featured_div')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                    </div>
                </div>
                <!-- Products Section -->
                <div class="px-sm-3">
                    <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='false'>
                        @foreach ($preorder_products as $key => $product)
                            @include('preorder.frontend.product_box2',['product' => $product])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif