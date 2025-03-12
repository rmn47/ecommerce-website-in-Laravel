@extends('frontend.layouts.app')

@section('meta_title'){{ $detailedProduct->meta_title }}@stop

@section('meta_description'){{ $detailedProduct->meta_description }}@stop

@section('meta_keywords'){{ $detailedProduct->tags }}@stop

@section('meta')
    @php
        $availability = "out of stock";
        $qty = 0;
        if ($detailedProduct->variant_product) {
            foreach ($detailedProduct->stocks as $key => $stock) {
                $qty += $stock->qty;
            }
        } else {
            $qty = optional($detailedProduct->stocks->first())->qty;
        }
        if ($qty > 0) {
            $availability = "in stock";
        }
    @endphp
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $detailedProduct->meta_title }}">
    <meta itemprop="description" content="{{ $detailedProduct->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $detailedProduct->meta_title }}">
    <meta name="twitter:description" content="{{ $detailedProduct->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}">
    <meta name="twitter:data1" content="{{ single_price($detailedProduct->unit_price) }}">
    <meta name="twitter:label1" content="Price">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $detailedProduct->meta_title }}" />
    <meta property="og:type" content="og:product" />
    <meta property="og:url" content="{{ route('product', $detailedProduct->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($detailedProduct->meta_img) }}" />
    <meta property="og:description" content="{{ $detailedProduct->meta_description }}" />
    <meta property="og:site_name" content="{{ get_setting('meta_title') }}" />
    <meta property="og:price:amount" content="{{ single_price($detailedProduct->unit_price) }}" />
    <meta property="product:brand" content="{{ $detailedProduct->brand ? $detailedProduct->brand->name : env('APP_NAME') }}">
    <meta property="product:availability" content="{{ $availability }}">
    <meta property="product:condition" content="new">
    <meta property="product:price:amount" content="{{ number_format($detailedProduct->unit_price, 2) }}">
    <meta property="product:retailer_item_id" content="{{ $detailedProduct->slug }}">
    <meta property="product:price:currency" content="{{ get_system_default_currency()->code }}" />
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
@endsection

@section('content')
    <section class="mb-4 pt-3">
        <div class="container">
            <div class="bg-white py-3">
                <div class="row">
                    <!-- Product Image Gallery -->
                    <div class="col-xl-5 col-lg-6 mb-4">
                        @include('frontend.product_details.image_gallery')
                    </div>

                    <!-- Product Details -->
                    <div class="col-xl-7 col-lg-6">
                        @include('frontend.product_details.details')
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-4">
        <div class="container">
            @if ($detailedProduct->auction_product)
                <!-- Reviews & Ratings -->
                @include('frontend.product_details.review_section')
                
                <!-- Description, Video, Downloads -->
                @include('frontend.product_details.description')
                
                <!-- Product Query -->
                @include('frontend.product_details.product_queries')
            @else
                <div class="row gutters-16">
                    <!-- Left side -->
                    <div class="col-lg-3">
                        <!-- Seller Info -->
                        @include('frontend.product_details.seller_info')

                        <!-- Top Selling Products -->
                        <div class="d-none d-lg-block">
                            @include('frontend.product_details.top_selling_products')
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="col-lg-9">
                        <!-- Reviews & Ratings -->
                        @include('frontend.product_details.review_section')

                        <!-- Description, Video, Downloads -->
                        @include('frontend.product_details.description')
                        
                        <!-- Substitute products -->
                        @include('frontend.product_details.substitute_products')
                        
                        <!-- Frequently Bought products -->
                        @include('frontend.product_details.frequently_bought_products')
                        

                        <!-- Product Query -->
                        @include('frontend.product_details.product_queries')
                        
                        <!-- Top Selling Products -->
                        <div class="d-lg-none">
                            @include('frontend.product_details.top_selling_products')
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('modal')
    <!-- Image Modal -->
    <div class="modal fade" id="image_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="p-4">
                    <div class="size-300px size-lg-450px">
                        <img class="img-fit h-100 lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Modal -->
    <div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="modal-header">
                    <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="form-group">
                            <input type="text" class="form-control mb-3 rounded-0" name="title" value="{{ $detailedProduct->name }}" placeholder="{{ translate('Product Name') }}" required>
                        </div>
                        <div class="form-group">
                            <textarea class="form-control rounded-0" rows="8" name="message" required placeholder="{{ translate('Your Question') }}">{{ route('product', $detailedProduct->slug) }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary fw-600 rounded-0" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary fw-600 rounded-0 w-100px">{{ translate('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bid Modal -->
    @if($detailedProduct->auction_product == 1)
        @php 
            $highest_bid = $detailedProduct->bids->max('amount');
            $min_bid_amount = $highest_bid != null ? $highest_bid + 1 : $detailedProduct->starting_bid; 
        @endphp
        <div class="modal fade" id="bid_for_detail_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ translate('Bid For Product') }} <small>({{ translate('Min Bid Amount: ') . $min_bid_amount }})</small></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" action="{{ route('auction_product_bids.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $detailedProduct->id }}">
                            <div class="form-group">
                                <label class="form-label">{{ translate('Place Bid Price') }} <span class="text-danger">*</span></label>
                                <div class="form-group">
                                    <input type="number" step="0.01" class="form-control form-control-sm" name="amount" min="{{ $min_bid_amount }}" placeholder="{{ translate('Enter Amount') }}" required>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{ translate('Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content" id="product-review-modal-content"></div>
        </div>
    </div>

    <!-- Size Chart Show Modal -->
    @include('modals.size_chart_show_modal')

    <!-- Product Warranty Modal -->
    <div class="modal fade" id="warranty-note-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Warranty Note') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body c-scrollbar-light">
                    @if($detailedProduct->warranty_note_id != null)
                        <p>{{ $detailedProduct->warrantyNote->getTranslation('description') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Product Refund Modal -->
    <div class="modal fade" id="refund-note-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title h6">{{ translate('Refund Note') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body c-scrollbar-light">
                    @if($detailedProduct->refund_note_id != null)
                        <p>{{ $detailedProduct->refundNote->getTranslation('description') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <span id="respn">..</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="min-height: 380px">
                    <div class="row g-0" id="otpStep" style="display: none;">
                        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-0 border-end">
                            <img src="https://www.1mg.com/images/login-signup/Lab-Tests-at-Home.png" class="img-fluid h-100 w-100 object-fit-cover" alt="Login Image">
                        </div>
                        <div class="col-md-6 col-12 p-4 d-flex flex-column justify-content-center">
                            <div class="text-center mb-4">
                                <h4 class="mb-2 fw-bold">LOGIN / SIGN UP</h4>
                                <p class="text-muted fs-6">Get access to your orders, lab tests & doctor consultations</p>
                            </div>
                            <div class="w-100 mx-auto" style="max-width: 300px;">
                                <div id="sotp">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <input type="text" class="form-control rounded-3" id="mobileNumber" name="mobileNumber" placeholder="Enter Mobile Number">
                                        </div>
                                        <button type="button" onclick="sendOtp()" class="btn btn-info w-100 rounded-pill mb-3">Next</button>
                                        <p class="mt-3 mb-2 fs-6">New on Our Portal? <a href="#" class="text-decoration-underline">Sign Up</a></p>
                                        <p class="mb-2 text-muted fs-6">By logging in, you agree to our <a href="#" class="text-decoration-underline">Terms & Conditions</a> & <a href="#" class="text-decoration-underline">Privacy Policy</a></p>
                                        <p class="text-muted fs-6">Need Help? <a href="#" class="text-decoration-underline">Get In Touch</a></p>
                                    </form>
                                </div>
                                <div id="verOtp" style="display: none;">
                                    <p><span id="editOtpText"></span> <a href="javascript:;" onClick="editOtpNumber()">Edit</a></p>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <input type="text" class="form-control rounded-3" id="otp" placeholder="Enter OTP">
                                            <span id="otpResend" style="display: none;" class="d-block mt-2 text-muted fs-6">Resend OTP in <span id="otpTimer"></span></span>
                                            <div id="otpResendBtn" style="display: none;" class="mt-2">
                                                <button type="button" class="btn btn-outline-info w-100 rounded-pill" onclick="resendOtp()">Resend OTP</button>
                                            </div>
                                        </div>
                                        <button type="button" onclick="verifyOtp()" class="btn btn-info w-100 rounded-pill">Verify OTP</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0" id="registratioFormStep" style="display: none;">
                        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-0 border-end">
                            <img src="https://www.1mg.com/images/login-signup/Lab-Tests-at-Home.png" class="img-fluid h-100 w-100 object-fit-cover" alt="Registration Image">
                        </div>
                        <div class="col-md-6 col-12 p-4 d-flex flex-column justify-content-center">
                            <div class="text-center mb-4">
                                <h4 class="mb-2 fw-bold">CREATE ACCOUNT</h4>
                                <p class="text-muted fs-6">Complete your profile to get started</p>
                            </div>
                            <div class="w-100 mx-auto" style="max-width: 300px;">
                                <form class="reg-form needs-validation" novalidate>
                                    <div class="mb-3 text-center">
                                        <div class="d-flex justify-content-center flex-wrap gap-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="user_type" id="wholesaler" value="wholesaler">
                                                <label class="form-check-label fs-6" for="wholesaler">ORG/Wholesaler</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="user_type" id="seller" value="seller">
                                                <label class="form-check-label fs-6" for="seller">Seller</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="user_type" id="customer" value="customer" checked>
                                                <label class="form-check-label fs-6" for="customer">Customer</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="first_name" id="first_name" class="form-control form-control-sm rounded-3" placeholder="First Name" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="last_name" id="last_name" class="form-control form-control-sm rounded-3" placeholder="Last Name" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="tel" name="mobile_no" id="mobile_no" class="form-control form-control-sm rounded-3" placeholder="Mobile Number" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" name="email_id" id="email_id" class="form-control form-control-sm rounded-3" placeholder="Email Address" required>
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="billing_address" id="billing_address" class="form-control form-control-sm rounded-3" placeholder="Billing Address" required>
                                    </div>
                                    <div class="mb-3" id="org_seller" style="display: none;">
                                        <input type="text" name="gst_no" id="gst_no" class="form-control form-control-sm rounded-3" placeholder="GST/Registration No">
                                    </div>
                                    <div class="mb-3" id="org_seller2" style="display: none;">
                                        <input type="text" name="drug_license_no" id="drug_license_no" class="form-control form-control-sm rounded-3" placeholder="Drug License No">
                                    </div>
                                    <div class="mb-3">
                                        <input type="file" name="photo" id="photo" class="form-control form-control-sm rounded-3">
                                    </div>
                                    <button type="submit" name="submit" id="submit" class="btn btn-info w-100 rounded-pill mb-3">Register Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            getVariantPrice(); // Initial call to set price for default variant
            $('input[name^="attribute_id_"], input[name="color"]').on('change', function() {
                getVariantPrice();
            });
            $('input[name="quantity"]').on('change keyup', function() {
                getVariantPrice();
            });
        });

        function show_chat_modal() {
            @if (Auth::check())
                $('#chat_modal').modal('show');
            @else
                showLoginModal();
            @endif
        }

        // Pagination using AJAX
        $(window).on('hashchange', function() {
            if (window.history.pushState) {
                window.history.pushState('', '/', window.location.pathname);
            } else {
                window.location.hash = '';
            }
        });

        $(document).on('click', '.product-queries-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'query', 'queries-area');
            e.preventDefault();
        });

        $(document).on('click', '.product-reviews-pagination .pagination a', function(e) {
            getPaginateData($(this).attr('href').split('page=')[1], 'review', 'reviews-area');
            e.preventDefault();
        });

        function getPaginateData(page, type, section) {
            $.ajax({
                url: '?page=' + page,
                dataType: 'json',
                data: { type: type },
            }).done(function(data) {
                $('.' + section).html(data);
                location.hash = page;
            }).fail(function() {
                alert('Something went wrong! Data could not be loaded.');
            });
        }

        function showImage(photo) {
            $('#image_modal img').attr('src', photo);
            $('#image_modal img').attr('data-src', photo);
            $('#image_modal').modal('show');
        }

        function bid_modal() {
            @if (isCustomer() || isSeller())
                $('#bid_for_detail_product').modal('show');
            @elseif (isAdmin())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers & Sellers can Bid.") }}');
            @else
                showLoginModal();
            @endif
        }

        function product_review(product_id) {
            @if (isCustomer())
                @if ($review_status == 1)
                    $.post('{{ route('product_review_modal') }}', {
                        _token: '{{ @csrf_token() }}',
                        product_id: product_id
                    }, function(data) {
                        $('#product-review-modal-content').html(data);
                        $('#product-review-modal').modal('show', { backdrop: 'static' });
                        AIZ.extra.inputRating();
                    });
                @else
                    AIZ.plugins.notify('warning', '{{ translate("Sorry, You need to buy this product to give review.") }}');
                @endif
            @elseif (Auth::check() && !isCustomer())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
            @else
                showLoginModal();
            @endif
        }

        function showSizeChartDetail(id, name) {
            $('#size-chart-show-modal .modal-title').html('');
            $('#size-chart-show-modal .modal-body').html('');
            if (id == 0) {
                AIZ.plugins.notify('warning', '{{ translate("Sorry, There is no size guide found for this product.") }}');
                return false;
            }
            $.ajax({
                type: "GET",
                url: "{{ route('size-charts-show', '') }}/" + id,
                data: {},
                success: function(data) {
                    $('#size-chart-show-modal .modal-title').html(name);
                    $('#size-chart-show-modal .modal-body').html(data);
                    $('#size-chart-show-modal').modal('show');
                }
            });
        }

        function CopyToClipboard(e) {
            var url = $(e).data('url');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            try {
                document.execCommand("copy");
                AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
            } catch (err) {
                AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
            }
            $temp.remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('openLogin') === 'true') {
                showLoginModal();
            }
        });
    </script>
@endsection