<style>

    .form-check-inline {
        display: inline-flex !important; /* Override Bootstrap's block display on mobile */
        align-items: center;
        margin-right: 0.5rem; /* Reduced spacing between items */
        white-space: nowrap; /* Prevent text wrapping */
    }

    /* General Form Input Styling - Straight Underline Design */
    .reg-form .form-control-sm,
    #mobileNumber.form-control {
        border: none; /* Remove all borders */
        border-bottom: 1px solid #000; /* Straight black underline */
        border-radius: 0; /* Remove rounded corners */
        padding: 0.5rem 0; /* Consistent vertical padding for alignment */
        background: transparent; /* Transparent background */
        outline: none; /* Remove default outline */
        font-size: 0.875rem; /* Default font size for form controls */
        line-height: 1.5; /* Consistent line height */
        transition: border-color 0.2s ease; /* Smooth transition for focus */
    }

    .reg-form .form-control-sm:focus,
    #mobileNumber.form-control:focus {
        border-bottom: 1px solid #007bff; /* Blue underline on focus */
        box-shadow: none; /* Remove Bootstrap's default shadow */
    }

    .reg-form .form-control-sm::placeholder,
    #mobileNumber.form-control::placeholder {
        color: #999; /* Light gray placeholder text */
        opacity: 1; /* Ensure placeholder is fully visible */
    }

    /* Mobile Number Prefix Styling */
    .mobile-prefix {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #999; /* Light gray for visibility */
        pointer-events: none; /* Prevent interaction with prefix */
        font-size: 0.875rem; /* Match input font size */
    }

    .form-control.pl-40 {
        padding-left: 40px !important; /* Adjust padding to accommodate "+91" prefix */
    }

    /* File Input Styling */
    .reg-form input[type="file"].form-control-sm {
        border: none; /* Remove all borders */
        border-bottom: 1px solid #000; /* Straight black underline */
        padding: 0.5rem 0; /* Consistent padding */
        background: transparent; /* Transparent background */
        font-size: 0.875rem; /* Match other form controls */
    }

    .reg-form input[type="file"].form-control-sm::-webkit-file-upload-button {
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    /* OTP Input Styling */
    .otp-container {
        display: flex;
        justify-content: space-between;
        max-width: 200px;
        margin: 0 auto 1rem;
    }

    .otp-input {
        width: 40px;
        height: 40px;
        text-align: center;
        border: none;
        border-bottom: 1px solid #000; /* Straight black underline */
        border-radius: 0;
        padding: 0;
        font-size: 1.2rem;
        background: transparent;
        outline: none;
        transition: border-color 0.2s ease; /* Smooth transition for focus */
    }

    .otp-input:focus {
        border-bottom: 1px solid #007bff; /* Blue underline on focus */
        box-shadow: none; /* Remove Bootstrap's default shadow */
    }

    /* User Info Hover Menu */
    .user-info-wrapper {
        position: relative;
        display: inline-block;
    }

    .hover-user-top-menu {
        display: none;
        position: absolute;
        top: 100%; /* Position below the parent */
        right: 0; /* Align to the right of user info */
        z-index: 1000; /* Above other content */
        width: 220px; /* Fixed width for consistency */
        background-color: #fff;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border-radius: 0; /* Remove rounded corners for consistency */
    }

    .nav-user-info:hover + .hover-user-top-menu,
    .hover-user-top-menu:hover {
        display: block;
    }

    .user-top-nav-element {
        border-bottom: 1px solid #dee2e6;
    }

    .user-top-nav-element:last-child {
        border-bottom: none;
    }

    .user-top-nav-element a {
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        color: #333;
        text-decoration: none;
        font-size: 0.875rem; /* Consistent font size */
        transition: background-color 0.2s ease, color 0.2s ease; /* Smooth hover transitions */
    }

    .user-top-nav-element a:hover {
        background-color: #f5f5f5;
        color: #007bff;
    }

    /* Navbar Styling */
    .navbar {
        background-color: #fff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 0;
        height: 50px; /* Fixed navbar height */
    }

    .navbar-light .navbar-nav .nav-link {
        padding: 0.5rem 0.75rem;
        color: #333;
        font-weight: 700;
        font-size: 0.875rem;
        white-space: nowrap; /* Prevent text wrapping */
        transition: color 0.2s ease; /* Smooth hover transition */
    }

    .navbar-light .navbar-nav .nav-link:hover,
    .navbar-light .navbar-nav .nav-link:focus {
        color: #007bff;
        background-color: transparent;
    }

    .navbar-light .navbar-nav .nav-link.dropdown-toggle {
        padding: 0.5rem 0.75rem;
        color: #333;
        font-weight: 700;
        font-size: 0.875rem;
    }

    /* Dropdown Menu Styling */
    .dropdown-menu {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0;
        padding: 0.5rem 0;
        /*min-width: auto; */
        min-width: 300px;
        max-width: 90vw;
        /*max-width: none;*/
        max-height: none; /* Remove max-height to prevent scrolling */
        width: auto; /* Adjust width dynamically */
        overflow: visible; /* Ensure content is visible without scrolling */
        position: absolute;
        z-index: 1000;
        will-change: transform; /* Improve performance for animations */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    .dropdown-menu .p-3 {
        padding: 1rem;
    }

    .dropdown-menu .row > .col-3,
    .dropdown-menu .row > .col-4,
    .dropdown-menu .row > .col-6 {
        padding: 0 0.75rem;
        overflow: visible; /* Prevent scrolling in columns */
        max-height: none; /* Remove any height limit */
    }

    .dropdown-menu .row > .col-4:not(:last-child),
    .dropdown-menu .row > .col-6:not(:last-child),
    .dropdown-menu .row > .col-3:not(:last-child) {
        border-right: 1px solid #dee2e6;
    }

    .dropdown-menu .row .col-3,
    .dropdown-menu .row .col-4,
    .dropdown-menu .row .col-6 {
        transition: background-color 0.2s ease;
    }

    .dropdown-menu .row .col-3:hover,
    .dropdown-menu .row .col-4:hover,
    .dropdown-menu .row .col-6:hover {
        background-color: #f5f5f5;
    }

    .dropdown-menu .row .col-3 ul li,
    .dropdown-menu .row .col-4 ul li,
    .dropdown-menu .row .col-6 ul li {
        padding: 0.5rem 0.75rem;
        transition: background-color 0.2s ease;
    }

    .dropdown-menu .row .col-3 ul li:hover,
    .dropdown-menu .row .col-4 ul li:hover,
    .dropdown-menu .row .col-6 ul li:hover {
        background-color: #e9ecef;
    }

    .navbar-nav .dropdown-menu:has(.col-4),
    .navbar-nav .dropdown-menu:has(.col-3) {
        min-width: 780px; /* Minimum width for larger layouts */
        max-width: 150vw; /* Allow wide expansion if needed */
    }

    .dropdown-item {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        color: #333;
        display: block;
        white-space: nowrap; /* Prevent text wrapping */
        overflow: hidden;
        text-overflow: ellipsis;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .dropdown-item:hover,
    .dropdown-item:focus {
        background-color: #e9ecef;
        color: #007bff;
    }

    /* Cart Dropdown Styling */
    .nav-cart-box {
        position: relative;
    }

    .nav-cart-box .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 1000;
    }

    .nav-cart-box:hover .dropdown-menu {
        display: block;
    }

    /* Subcategory and Child Dropdown Styling */
    .subcategory-item {
        position: relative;
    }

    .child-dropdown {
        display: none;
        position: absolute;
        top: 100%; /* Position below the parent */
        left: 100%; /* Align to the right for natural expansion */
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0;
        padding: 0.5rem;
        min-width: 200px;
        z-index: 1001;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        max-height: none; /* Remove height limit to prevent scrolling */
        overflow: visible; /* Ensure content is visible without scrolling */
    }

    .subcategory-item:hover .child-dropdown {
        display: block;
    }

    .child-dropdown ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .child-dropdown ul li {
        padding: 0.25rem 0.75rem;
        transition: background-color 0.2s ease;
        white-space: nowrap; /* Prevent text wrapping */
    }

    .child-dropdown ul li:hover {
        background-color: #e9ecef;
    }

    /* Container and General Layout */
    .container {
        max-width: 100%;
        padding-left: 15px;
        padding-right: 15px;
    }

    /* Responsive Design */
    @media (max-width: 991px) {
        .nav-cart-box .dropdown-menu {
            display: none;
        }

        .nav-cart-box.show .dropdown-menu {
            display: block;
        }

        .navbar-nav {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.5rem;
        }

        .col-md-6.col-12 {
            padding: 1rem !important;
        }

        .form-check-inline {
            display: block;
            margin-bottom: 0.25rem;
        }

        .form-control-sm,
        .reg-form .form-check-label {
            font-size: 0.75rem; /* Smaller font size for mobile */
        }
    }

    @media (max-width: 991px) and (min-width: 576px) {
        .navbar-nav {
            justify-content: center;
        }

        .navbar-light .navbar-nav .nav-link {
            padding: 0.5rem 0.5rem;
            font-size: 0.85rem;
        }

        .dropdown-menu {
            min-width: 180px;
        }

        .navbar-nav .dropdown-menu:has(.col-4) {
            min-width: 250px;
        }
    }

    @media (min-width: 992px) {
        .navbar-nav {
            justify-content: flex-start;
            flex-wrap: nowrap;
        }

        .navbar-light .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
        }

        .dropdown-menu {
            position: absolute;
            will-change: transform;
        }

        .dropdown-menu.right-edge {
            transform: translateX(-30px);
            transition: transform 0.2s ease-in-out;
        }

        .navbar-nav .nav-item.dropdown .dropdown-menu {
            left: 0;
            transform: translateX(0);
        }

        .dropdown-menu[data-bs-boundary="viewport"] {
            z-index: 1000;
        }

        .navbar-nav .nav-item.dropdown:hover .dropdown-menu,
        .navbar-nav .nav-item.dropdown.show .dropdown-menu {
            position: absolute;
            right: auto;
            left: 0;
            transform: translateX(0);
        }

        .subcategory-item .child-dropdown {
            top: 100%; /* Ensure it appears below the parent */
            left: 100%; /* Align to the right for natural expansion */
        }
    }

    @media (min-width: 992px) {
        .navbar.navbar-expand-lg.navbar-light {
            padding-left: 15px; /* Adjust margin as needed */
        }
    }

    @media (min-width: 1280px) {
        .navbar.navbar-expand-lg.navbar-light {
            padding-left: 80px; /* Adjust margin as needed */
        }
    }

    @media (min-width: 1440px) {
        .navbar.navbar-expand-lg.navbar-light {
            padding-left: 100px; /* Adjust margin as needed */
        }
    }

    @media (min-width: 1600px) {
        .navbar.navbar-expand-lg.navbar-light {
            width: fit-content; /* Or a specific width like 80% */
            margin: 0 auto; /* Centers it horizontally */
        }
    }
</style>

<!-- Top Bar Banner -->
@php
    $topbar_banner = get_setting('topbar_banner');
    $topbar_banner_medium = get_setting('topbar_banner_medium');
    $topbar_banner_small = get_setting('topbar_banner_small');
    $topbar_banner_asset = uploaded_asset($topbar_banner);
@endphp
@if ($topbar_banner != null)
    <div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner" data-value="removed">
        <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset h-40px h-lg-60px">
            <img src="{{ $topbar_banner_asset }}" class="d-none d-xl-block img-fit h-100" alt="{{ translate('topbar_banner') }}">
            <img src="{{ $topbar_banner_medium != null ? uploaded_asset($topbar_banner_medium) : $topbar_banner_asset }}"
                class="d-none d-md-block d-xl-none img-fit h-100" alt="{{ translate('topbar_banner') }}">
            <img src="{{ $topbar_banner_small != null ? uploaded_asset($topbar_banner_small) : $topbar_banner_asset }}"
                class="d-md-none img-fit h-100" alt="{{ translate('topbar_banner') }}">
        </a>
        <button class="btn text-white h-100 absolute-top-right set-session" data-key="top-banner"
            data-value="removed" data-toggle="remove-parent" data-parent=".top-banner">
            <i class="la la-close la-2x"></i>
        </button>
    </div>
@endif

<!-- Top Bar -->
<div class="top-navbar bg-white z-1035 h-35px h-sm-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col">
                <ul class="list-inline d-flex justify-content-between justify-content-lg-start mb-0">
                    @if (get_setting('show_language_switcher') == 'on')
                        <li class="list-inline-item dropdown mr-4" id="lang-change">
                            <a href="javascript:void(0)" class="dropdown-toggle text-secondary fs-12 py-2" data-toggle="dropdown" data-display="static">
                                <span class="">{{ $system_language->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach (get_all_active_language() as $key => $language)
                                    <li>
                                        <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                            class="dropdown-item @if ($system_language->code == $language->code) active @endif">
                                            <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                data-src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                                                class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                            <span class="language">{{ $language->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    @if (get_setting('show_currency_switcher') == 'on')
                        <li class="list-inline-item dropdown ml-auto ml-lg-0 mr-0" id="currency-change">
                            @php
                                $system_currency = get_system_currency();
                            @endphp
                            <a href="javascript:void(0)" class="dropdown-toggle text-secondary fs-12 py-2" data-toggle="dropdown" data-display="static">
                                {{ $system_currency->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
                                @foreach (get_all_active_currency() as $key => $currency)
                                    <li>
                                        <a class="dropdown-item @if ($system_currency->code == $currency->code) active @endif"
                                            href="javascript:void(0)" data-currency="{{ $currency->code }}">{{ $currency->name }} ({{ $currency->symbol }})</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="col-6 text-right d-none d-lg-block">
                <ul class="list-inline mb-0 h-100 d-flex justify-content-end align-items-center">
                    @if (get_setting('vendor_system_activation') == 1)
                        <li class="list-inline-item mr-0 pl-0 py-2">
                            <a href="{{ route('shops.create') }}" class="text-secondary fs-12 pr-3 d-inline-block border-width-2 border-right">{{ translate('Become a Seller !') }}</a>
                        </li>
                        <li class="list-inline-item mr-0 pl-0 py-2">
                            <a href="{{ route('seller.login') }}" class="text-secondary fs-12 pl-3 d-inline-block">{{ translate('Login to Seller') }}</a>
                        </li>
                    @endif
                    @if (get_setting('helpline_number'))
                        <li class="list-inline-item ml-3 pl-3 mr-0 pr-0">
                            <a href="tel:{{ get_setting('helpline_number') }}" class="text-secondary fs-12 d-inline-block py-2">
                                <span>{{ translate('Helpline') }}</span>
                                <span>{{ get_setting('helpline_number') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<header class="@if (get_setting('header_stikcy') == 'on') sticky-top @endif sticky-top z-1020 bg-white">
    <!-- Search Bar -->
    <div class="position-relative logo-bar-area border-bottom border-md-nonea z-1025">
        <div class="container">
            <div class="d-flex align-items-center">
                <button type="button" class="btn d-lg-none mr-3 mr-sm-4 p-0 active" data-toggle="class-toggle" data-target=".aiz-top-menu-sidebar">
                    <svg id="Component_43_1" data-name="Component 43 – 1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                        <rect id="Rectangle_19062" data-name="Rectangle 19062" width="16" height="2" transform="translate(0 7)" fill="#919199" />
                        <rect id="Rectangle_19063" data-name="Rectangle 19063" width="16" height="2" fill="#919199" />
                        <rect id="Rectangle_19064" data-name="Rectangle 19064" width="16" height="2" transform="translate(0 14)" fill="#919199" />
                    </svg>
                </button>
                <div class="col-auto pl-0 pr-3 d-flex align-items-center">
                    <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                        @php
                            $header_logo = get_setting('header_logo');
                        @endphp
                        @if ($header_logo != null)
                            <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
                        @else
                            <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
                        @endif
                    </a>
                </div>
                <div class="d-lg-none ml-auto mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>
                <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white mx-xl-5">
                    <div class="position-relative flex-grow-1 px-3 px-lg-0">
                        <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                            <div class="d-flex position-relative align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="search-input-box">
                                    <input type="text" class="border border-soft-light form-control fs-14 hov-animate-outline" id="search" name="keyword"
                                        @isset($query) value="{{ $query }}" @endisset placeholder="{{ translate('I am shopping for...') }}" autocomplete="off">
                                    <svg id="Group_723" data-name="Group 723" xmlns="http://www.w3.org/2000/svg" width="20.001" height="20" viewBox="0 0 20.001 20">
                                        <path id="Path_3090" data-name="Path 3090" d="M9.847,17.839a7.993,7.993,0,1,1,7.993-7.993A8,8,0,0,1,9.847,17.839Zm0-14.387a6.394,6.394,0,1,0,6.394,6.394A6.4,6.4,0,0,0,9.847,3.453Z" transform="translate(-1.854 -1.854)" fill="#b5b5bf" />
                                        <path id="Path_3091" data-name="Path 3091" d="M24.4,25.2a.8.8,0,0,1-.565-.234l-6.15-6.15a.8.8,0,0,1,1.13-1.13l6.15,6.15A.8.8,0,0,1,24.4,25.2Z" transform="translate(-5.2 -5.2)" fill="#b5b5bf" />
                                    </svg>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16"></div>
                            <div id="search-content" class="text-left"></div>
                        </div>
                    </div>
                </div>
                @if (Auth::check() && auth()->user()->user_type == 'customer')
                    <div class="d-none d-lg-block ml-3 mr-0" id="compare">
                        @include('frontend.partials.compare')
                    </div>
                    <div class="d-none d-lg-block mr-3" style="margin-left: 36px;" id="wishlist">
                        @include('frontend.partials.wishlist')
                    </div>
                    <ul class="list-inline mb-0 h-100 d-none d-xl-flex justify-content-end align-items-center">
                        <li class="list-inline-item ml-3 mr-3 pr-3 pl-0 dropdown">
                            <a class="dropdown-toggle no-arrow text-secondary fs-12" data-toggle="dropdown" href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false" onclick="nonLinkableNotificationRead()">
                                <span class="position-relative d-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14.668" height="16" viewBox="0 0 14.668 16">
                                        <path id="_26._Notification" data-name="26. Notification" d="M8.333,16A3.34,3.34,0,0,0,11,14.667H5.666A3.34,3.34,0,0,0,8.333,16ZM15.06,9.78a2.457,2.457,0,0,1-.727-1.747V6a6,6,0,1,0-12,0V8.033A2.457,2.457,0,0,1,1.606,9.78,2.083,2.083,0,0,0,3.08,13.333H13.586A2.083,2.083,0,0,0,15.06,9.78Z" transform="translate(-0.999)" fill="#91919b" />
                                    </svg>
                                    @if (Auth::check() && count($user->unreadNotifications) > 0)
                                        <span class="badge badge-primary badge-inline badge-pill absolute-top-right--10px unread-notification-count">{{ count($user->unreadNotifications) }}</span>
                                    @endif
                                </span>
                            </a>
                            @auth
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg py-0 rounded-0">
                                    <div class="p-3 bg-light border-bottom">
                                        <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                                    </div>
                                    <div class="c-scrollbar-light overflow-auto" style="max-height:300px;">
                                        <ul class="list-group list-group-flush">
                                            @forelse($user->unreadNotifications as $notification)
                                                @php
                                                    $showNotification = true;
                                                    if (($notification->type == 'App\Notifications\PreorderNotification') && !addon_is_activated('preorder'))
                                                    {
                                                        $showNotification = false;
                                                    }
                                                @endphp
                                                @if($showNotification)
                                                    @php
                                                        $isLinkable = true;
                                                        $notificationType = get_notification_type($notification->notification_type_id, 'id');
                                                        $notifyContent = $notificationType->getTranslation('default_text');
                                                        $notificationShowDesign = get_setting('notification_show_type');
                                                        if($notification->type == 'App\Notifications\customNotification' && $notification->data['link'] == null){
                                                            $isLinkable = false;
                                                        }
                                                    @endphp
                                                    <li class="list-group-item">
                                                        <div class="d-flex">
                                                            @if($notificationShowDesign != 'only_text')
                                                                <div class="size-35px mr-2">
                                                                    @php
                                                                        $notifyImageDesign = '';
                                                                        if($notificationShowDesign == 'design_2'){
                                                                            $notifyImageDesign = 'rounded-1';
                                                                        }
                                                                        elseif($notificationShowDesign == 'design_3'){
                                                                            $notifyImageDesign = 'rounded-circle';
                                                                        }
                                                                    @endphp
                                                                    <img src="{{ uploaded_asset($notificationType->image) }}"
                                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/notification.png') }}';"
                                                                        class="img-fit h-100 {{ $notifyImageDesign }}" >
                                                                </div>
                                                            @endif
                                                            <div>
                                                                @if ($notification->type == 'App\Notifications\OrderNotification')
                                                                    @php
                                                                        $orderCode  = $notification->data['order_code'];
                                                                        $route = route('purchase_history.details', encrypt($notification->data['order_id']));
                                                                            $orderCode = "<span class='text-blue'>".$orderCode."</span>";
                                                                        $notifyContent = str_replace('[[order_code]]', $orderCode, $notifyContent);
                                                                    @endphp
                                                                @elseif($notification->type == 'App\Notifications\PreorderNotification')
                                                                    @php
                                                                        $orderCode  = $notification->data['order_code'];
                                                                        $route = route('preorder.order_details', encrypt($notification->data['preorder_id']));
                                                                            $orderCode = "<span class='text-blue'>".$orderCode."</span>";
                                                                        $notifyContent = str_replace('[[order_code]]', $orderCode, $notifyContent);
                                                                    @endphp
                                                                @endif
                                                                @if($isLinkable = true)
                                                                    <a href="{{ route('notification.read-and-redirect', encrypt($notification->id)) }}">
                                                                @endif
                                                                    <span class="fs-12 text-dark text-truncate-2">{!! $notifyContent !!}</span>
                                                                @if($isLinkable = true)
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="py-4 text-center fs-16">
                                                        {{ translate('No notification found') }}
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div class="text-center border-top">
                                        <a href="{{ route('customer.all-notifications') }}" class="text-secondary fs-12 d-block py-2">
                                            {{ translate('View All Notifications') }}
                                        </a>
                                    </div>
                                </div>
                            @endauth
                        </li>
                    </ul>
                @endif
                <div class="d-none d-xl-block align-self-center py-3 ml-5 mr-0 has-transition my-0" data-hover="dropdown">
                    <div class="nav-cart-box dropdown h-100" id="cart_items" style="width: max-content;">
                        @include('frontend.partials.cart.cart')
                    </div>
                </div>
                <div class="d-none d-xl-block ml-auto mr-0">
                    @auth
                        <div class="user-info-wrapper position-relative">
                            <span class="d-flex align-items-center nav-user-info py-20px @if (isAdmin()) ml-5 @endif" id="nav-user-info">
                                <span class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                                    @if ($user->avatar_original != null)
                                        <img src="{{ asset($user->avatar_original) }}" class="img-fit h-100" alt="{{ translate('avatar') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @else
                                        <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image" alt="{{ translate('avatar') }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                                    @endif
                                </span>
                                <h4 class="h5 fs-14 fw-700 text-dark ml-2 mb-0">{{ $user->name }}</h4>
                            </span>
                            <div class="hover-user-top-menu">
                                <div class="container">
                                    <div class="position-static float-right">
                                        <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm" style="width:220px;">
                                            <ul class="list-unstyled no-scrollbar mb-0 text-left">
                                                @if (isAdmin())
                                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                                        <a href="{{ route('admin.dashboard') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                                <path id="Path_2916" data-name="Path 2916" d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z" fill="#b5b5c0" />
                                                            </svg>
                                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                                        </a>
                                                    </li>
                                                @else
                                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                                        <a href="{{ route('dashboard') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                                <path id="Path_2916" data-name="Path 2916" d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z" fill="#b5b5c0" />
                                                            </svg>
                                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                @if (isCustomer())
                                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                                        <a href="{{ route('purchase_history.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                                <g id="Group_25261" data-name="Group 25261" transform="translate(-27.466 -542.963)">
                                                                    <path id="Path_2953" data-name="Path 2953" d="M14.5,5.963h-4a1.5,1.5,0,0,0,0,3h4a1.5,1.5,0,0,0,0-3m0,2h-4a.5.5,0,0,1,0-1h4a.5.5,0,0,1,0,1" transform="translate(22.966 537)" fill="#b5b5bf" />
                                                                    <path id="Path_2954" data-name="Path 2954" d="M12.991,8.963a.5.5,0,0,1,0-1H13.5a2.5,2.5,0,0,1,2.5,2.5v10a2.5,2.5,0,0,1-2.5,2.5H2.5a2.5,2.5,0,0,1-2.5-2.5v-10a2.5,2.5,0,0,1,2.5-2.5h.509a.5.5,0,0,1,0,1H2.5a1.5,1.5,0,0,0-1.5,1.5v10a1.5,1.5,0,0,0,1.5,1.5h11a1.5,1.5,0,0,0,1.5-1.5v-10a1.5,1.5,0,0,0-1.5-1.5Z" transform="translate(27.466 536)" fill="#b5b5bf" />
                                                                    <path id="Path_2955" data-name="Path 2955" d="M7.5,15.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 532)" fill="#b5b5bf" />
                                                                    <path id="Path_2956" data-name="Path 2956" d="M7.5,21.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 529)" fill="#b5b5bf" />
                                                                    <path id="Path_2957" data-name="Path 2957" d="M7.5,27.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 526)" fill="#b5b5bf" />
                                                                    <path id="Path_2958" data-name="Path 2958" d="M13.5,16.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 531.5)" fill="#b5b5bf" />
                                                                    <path id="Path_2959" data-name="Path 2959" d="M13.5,22.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 528.5)" fill="#b5b5bf" />
                                                                    <path id="Path_2960" data-name="Path 2960" d="M13.5,28.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 525.5)" fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Purchase History') }}</span>
                                                        </a>
                                                    </li>
                                                    @if (addon_is_activated('preorder'))
                                                        <li class="user-top-nav-element border border-top-0" data-id="1">
                                                            <a href="{{ route('preorder.order_list') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.002" viewBox="0 0 16 16.002">
                                                                    <path id="Union_63" data-name="Union 63" d="M14072,894a8,8,0,1,1,8,8A8.011,8.011,0,0,1,14072,894Zm1,0a7,7,0,1,0,7-7A7.007,7.007,0,0,0,14073,894Zm10.652,3.674-3.2-2.781a1,1,0,0,1-.953-1.756V889.5a.5.5,0,1,1,1,0v3.634a1,1,0,0,1,.5.863c0,.015,0,.029,0,.044l3.311,2.876a.5.5,0,0,1,.05.7.5.5,0,0,1-.708.049Z" transform="translate(-14072 -885.998)" fill="#b5b5bf"/>
                                                                </svg>
                                                                <span class="user-top-menu-name has-transition ml-3">{{ translate('Preorder List') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                                        <a href="{{ route('digital_purchase_history.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.001" height="16" viewBox="0 0 16.001 16">
                                                                <g id="Group_25262" data-name="Group 25262" transform="translate(-1388.154 -562.604)">
                                                                    <path id="Path_2963" data-name="Path 2963" d="M77.864,98.69V92.1a.5.5,0,1,0-1,0V98.69l-1.437-1.437a.5.5,0,0,0-.707.707l1.851,1.852a1,1,0,0,0,.707.293h.172a1,1,0,0,0,.707-.293l1.851-1.852a.5.5,0,0,0-.7-.713Z" transform="translate(1318.79 478.5)" fill="#b5b5bf" />
                                                                    <path id="Path_2964" data-name="Path 2964" d="M67.155,88.6a3,3,0,0,1-.474-5.963q-.009-.089-.015-.179a5.5,5.5,0,0,1,10.977-.718,3.5,3.5,0,0,1-.989,6.859h-1.5a.5.5,0,0,1,0-1l1.5,0a2.5,2.5,0,0,0,.417-4.967.5.5,0,0,1-.417-.5,4.5,4.5,0,1,0-8.908.866.512.512,0,0,1,.009.121.5.5,0,0,1-.52.479,2,2,0,1,0-.162,4l.081,0h2a.5.5,0,0,1,0,1Z" transform="translate(1324 486)" fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Downloads') }}</span>
                                                        </a>
                                                    </li>
                                                    @if (get_setting('conversation_system') == 1)
                                                        <li class="user-top-nav-element border border-top-0" data-id="1">
                                                            <a href="{{ route('conversations.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                                    <g id="Group_25263" data-name="Group 25263" transform="translate(1053.151 256.688)">
                                                                        <path id="Path_3012" data-name="Path 3012" d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z" transform="translate(-1178 -341)" fill="#b5b5bf" />
                                                                        <path id="Path_3013" data-name="Path 3013" d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1" transform="translate(-1182 -337)" fill="#b5b5bf" />
                                                                        <path id="Path_3014" data-name="Path 3014" d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)" fill="#b5b5bf" />
                                                                        <path id="Path_3015" data-name="Path 3015" d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)" fill="#b5b5bf" />
                                                                    </g>
                                                                </svg>
                                                                <span class="user-top-menu-name has-transition ml-3">{{ translate('Conversations') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (get_setting('wallet_system') == 1)
                                                        <li class="user-top-nav-element border border-top-0" data-id="1">
                                                            <a href="{{ route('wallet.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16">
                                                                    <defs>
                                                                        <clipPath id="clip-path1">
                                                                            <rect id="Rectangle_1386" data-name="Rectangle 1386" width="16" height="16" fill="#b5b5bf" />
                                                                        </clipPath>
                                                                    </defs>
                                                                    <g id="Group_8102" data-name="Group 8102" clip-path="url(#clip-path1)">
                                                                        <path id="Path_2936" data-name="Path 2936" d="M13.5,4H13V2.5A2.5,2.5,0,0,0,10.5,0h-8A2.5,2.5,0,0,0,0,2.5v11A2.5,2.5,0,0,0,2.5,16h11A2.5,2.5,0,0,0,16,13.5v-7A2.5,2.5,0,0,0,13.5,4M2.5,1h8A1.5,1.5,0,0,1,12,2.5V4H2.5a1.5,1.5,0,0,1,0-3M15,11H10a1,1,0,0,1,0-2h5Zm0-3H10a2,2,0,0,0,0,4h5v1.5A1.5,1.5,0,0,1,13.5,15H2.5A1.5,1.5,0,0,1,1,13.5v-9A2.5,2.5,0,0,0,2.5,5h11A1.5,1.5,0,0,1,15,6.5Z" fill="#b5b5bf" />
                                                                    </g>
                                                                </svg>
                                                                <span class="user-top-menu-name has-transition ml-3">{{ translate('My Wallet') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                                        <a href="{{ route('support_ticket.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.001" viewBox="0 0 16 16.001">
                                                                <g id="Group_25259" data-name="Group 25259" transform="translate(-316 -1066)">
                                                                    <path id="Subtraction_184" data-name="Subtraction 184" d="M16427.109,902H16420a8.015,8.015,0,1,1,8-8,8.278,8.278,0,0,1-1.422,4.535l1.244,2.132a.81.81,0,0,1,0,.891A.791.791,0,0,1,16427.109,902ZM16420,887a7,7,0,1,0,0,14h6.283c.275,0,.414,0,.549-.111s-.209-.574-.34-.748l0,0-.018-.022-1.064-1.6A6.829,6.829,0,0,0,16427,894a6.964,6.964,0,0,0-7-7Z" transform="translate(-16096 180)" fill="#b5b5bf" />
                                                                    <path id="Union_12" data-name="Union 12" d="M16414,895a1,1,0,1,1,1,1A1,1,0,0,1,16414,895Zm.5-2.5V891h.5a2,2,0,1,0-2-2h-1a3,3,0,1,1,3.5,2.958v.54a.5.5,0,1,1-1,0Zm-2.5-3.5h1a.5.5,0,1,1-1,0Z" transform="translate(-16090.998 183.001)" fill="#b5b5bf" />
                                                                </g>
                                                            </svg>
                                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Support Ticket') }}</span>
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                                    <a href="{{ route('logout') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.999" viewBox="0 0 16 15.999">
                                                            <g id="Group_25503" data-name="Group 25503" transform="translate(-24.002 -377)">
                                                                <g id="Group_25265" data-name="Group 25265" transform="translate(-216.534 -160)">
                                                                    <path id="Subtraction_192" data-name="Subtraction 192" d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z" transform="translate(-11803.999 -2367)" fill="#d43533" />
                                                                </g>
                                                                <rect id="Rectangle_19022" data-name="Rectangle 19022" width="1" height="8" rx="0.5" transform="translate(31.5 377)" fill="#d43533" />
                                                            </g>
                                                        </svg>
                                                        <span class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <span class="d-flex align-items-center nav-user-info pl-4">
                            <a href="javascript:void(0)" onclick="showLoginModal()" class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block border-right border-soft-light border-width-2 pr-2 ml-3">{{ translate('Login') }}</a>
                            <a href="javascript:void(0)" onclick="showLoginModal()" class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a>
                        </span>
                    @endauth
                </div>
                
            </div>
        </div>

        <!-- Logged in user Menus -->
        {{-- <div class="hover-user-top-menu position-absolute top-100 left-0 right-0 z-3">
            <div class="container">
                <div class="position-static float-right">
                    <div class="aiz-user-top-menu bg-white rounded-0 border-top shadow-sm" style="width:220px;">
                        <ul class="list-unstyled no-scrollbar mb-0 text-left">
                            @if (isAdmin())
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('admin.dashboard') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                            <path id="Path_2916" data-name="Path 2916" d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z" fill="#b5b5c0" />
                                        </svg>
                                        <span class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                    </a>
                                </li>
                            @else
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('dashboard') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                            <path id="Path_2916" data-name="Path 2916" d="M15.3,5.4,9.561.481A2,2,0,0,0,8.26,0H7.74a2,2,0,0,0-1.3.481L.7,5.4A2,2,0,0,0,0,6.92V14a2,2,0,0,0,2,2H14a2,2,0,0,0,2-2V6.92A2,2,0,0,0,15.3,5.4M10,15H6V9A1,1,0,0,1,7,8H9a1,1,0,0,1,1,1Zm5-1a1,1,0,0,1-1,1H11V9A2,2,0,0,0,9,7H7A2,2,0,0,0,5,9v6H2a1,1,0,0,1-1-1V6.92a1,1,0,0,1,.349-.76l5.74-4.92A1,1,0,0,1,7.74,1h.52a1,1,0,0,1,.651.24l5.74,4.92A1,1,0,0,1,15,6.92Z" fill="#b5b5c0" />
                                        </svg>
                                        <span class="user-top-menu-name has-transition ml-3">{{ translate('Dashboard') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if (isCustomer())
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('purchase_history.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                            <g id="Group_25261" data-name="Group 25261" transform="translate(-27.466 -542.963)">
                                                <path id="Path_2953" data-name="Path 2953" d="M14.5,5.963h-4a1.5,1.5,0,0,0,0,3h4a1.5,1.5,0,0,0,0-3m0,2h-4a.5.5,0,0,1,0-1h4a.5.5,0,0,1,0,1" transform="translate(22.966 537)" fill="#b5b5bf" />
                                                <path id="Path_2954" data-name="Path 2954" d="M12.991,8.963a.5.5,0,0,1,0-1H13.5a2.5,2.5,0,0,1,2.5,2.5v10a2.5,2.5,0,0,1-2.5,2.5H2.5a2.5,2.5,0,0,1-2.5-2.5v-10a2.5,2.5,0,0,1,2.5-2.5h.509a.5.5,0,0,1,0,1H2.5a1.5,1.5,0,0,0-1.5,1.5v10a1.5,1.5,0,0,0,1.5,1.5h11a1.5,1.5,0,0,0,1.5-1.5v-10a1.5,1.5,0,0,0-1.5-1.5Z" transform="translate(27.466 536)" fill="#b5b5bf" />
                                                <path id="Path_2955" data-name="Path 2955" d="M7.5,15.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 532)" fill="#b5b5bf" />
                                                <path id="Path_2956" data-name="Path 2956" d="M7.5,21.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 529)" fill="#b5b5bf" />
                                                <path id="Path_2957" data-name="Path 2957" d="M7.5,27.963h1a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-1a.5.5,0,0,1,.5-.5" transform="translate(23.966 526)" fill="#b5b5bf" />
                                                <path id="Path_2958" data-name="Path 2958" d="M13.5,16.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 531.5)" fill="#b5b5bf" />
                                                <path id="Path_2959" data-name="Path 2959" d="M13.5,22.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 528.5)" fill="#b5b5bf" />
                                                <path id="Path_2960" data-name="Path 2960" d="M13.5,28.963h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(20.966 525.5)" fill="#b5b5bf" />
                                            </g>
                                        </svg>
                                        <span class="user-top-menu-name has-transition ml-3">{{ translate('Purchase History') }}</span>
                                    </a>
                                </li>
                                @if (addon_is_activated('preorder'))
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('preorder.order_list') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.002" viewBox="0 0 16 16.002">
                                                <path id="Union_63" data-name="Union 63" d="M14072,894a8,8,0,1,1,8,8A8.011,8.011,0,0,1,14072,894Zm1,0a7,7,0,1,0,7-7A7.007,7.007,0,0,0,14073,894Zm10.652,3.674-3.2-2.781a1,1,0,0,1-.953-1.756V889.5a.5.5,0,1,1,1,0v3.634a1,1,0,0,1,.5.863c0,.015,0,.029,0,.044l3.311,2.876a.5.5,0,0,1,.05.7.5.5,0,0,1-.708.049Z" transform="translate(-14072 -885.998)" fill="#b5b5bf"/>
                                            </svg>
                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Preorder List') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('digital_purchase_history.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16.001" height="16" viewBox="0 0 16.001 16">
                                            <g id="Group_25262" data-name="Group 25262" transform="translate(-1388.154 -562.604)">
                                                <path id="Path_2963" data-name="Path 2963" d="M77.864,98.69V92.1a.5.5,0,1,0-1,0V98.69l-1.437-1.437a.5.5,0,0,0-.707.707l1.851,1.852a1,1,0,0,0,.707.293h.172a1,1,0,0,0,.707-.293l1.851-1.852a.5.5,0,0,0-.7-.713Z" transform="translate(1318.79 478.5)" fill="#b5b5bf" />
                                                <path id="Path_2964" data-name="Path 2964" d="M67.155,88.6a3,3,0,0,1-.474-5.963q-.009-.089-.015-.179a5.5,5.5,0,0,1,10.977-.718,3.5,3.5,0,0,1-.989,6.859h-1.5a.5.5,0,0,1,0-1l1.5,0a2.5,2.5,0,0,0,.417-4.967.5.5,0,0,1-.417-.5,4.5,4.5,0,1,0-8.908.866.512.512,0,0,1,.009.121.5.5,0,0,1-.52.479,2,2,0,1,0-.162,4l.081,0h2a.5.5,0,0,1,0,1Z" transform="translate(1324 486)" fill="#b5b5bf" />
                                            </g>
                                        </svg>
                                        <span class="user-top-menu-name has-transition ml-3">{{ translate('Downloads') }}</span>
                                    </a>
                                </li>
                                @if (get_setting('conversation_system') == 1)
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('conversations.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
                                                <g id="Group_25263" data-name="Group 25263" transform="translate(1053.151 256.688)">
                                                    <path id="Path_3012" data-name="Path 3012" d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z" transform="translate(-1178 -341)" fill="#b5b5bf" />
                                                    <path id="Path_3013" data-name="Path 3013" d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1" transform="translate(-1182 -337)" fill="#b5b5bf" />
                                                    <path id="Path_3014" data-name="Path 3014" d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)" fill="#b5b5bf" />
                                                    <path id="Path_3015" data-name="Path 3015" d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)" fill="#b5b5bf" />
                                                </g>
                                            </svg>
                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('Conversations') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (get_setting('wallet_system') == 1)
                                    <li class="user-top-nav-element border border-top-0" data-id="1">
                                        <a href="{{ route('wallet.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" viewBox="0 0 16 16">
                                                <defs>
                                                    <clipPath id="clip-path1">
                                                        <rect id="Rectangle_1386" data-name="Rectangle 1386" width="16" height="16" fill="#b5b5bf" />
                                                    </clipPath>
                                                </defs>
                                                <g id="Group_8102" data-name="Group 8102" clip-path="url(#clip-path1)">
                                                    <path id="Path_2936" data-name="Path 2936" d="M13.5,4H13V2.5A2.5,2.5,0,0,0,10.5,0h-8A2.5,2.5,0,0,0,0,2.5v11A2.5,2.5,0,0,0,2.5,16h11A2.5,2.5,0,0,0,16,13.5v-7A2.5,2.5,0,0,0,13.5,4M2.5,1h8A1.5,1.5,0,0,1,12,2.5V4H2.5a1.5,1.5,0,0,1,0-3M15,11H10a1,1,0,0,1,0-2h5Zm0-3H10a2,2,0,0,0,0,4h5v1.5A1.5,1.5,0,0,1,13.5,15H2.5A1.5,1.5,0,0,1,1,13.5v-9A2.5,2.5,0,0,0,2.5,5h11A1.5,1.5,0,0,1,15,6.5Z" fill="#b5b5bf" />
                                                </g>
                                            </svg>
                                            <span class="user-top-menu-name has-transition ml-3">{{ translate('My Wallet') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="user-top-nav-element border border-top-0" data-id="1">
                                    <a href="{{ route('support_ticket.index') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16.001" viewBox="0 0 16 16.001">
                                            <g id="Group_25259" data-name="Group 25259" transform="translate(-316 -1066)">
                                                <path id="Subtraction_184" data-name="Subtraction 184" d="M16427.109,902H16420a8.015,8.015,0,1,1,8-8,8.278,8.278,0,0,1-1.422,4.535l1.244,2.132a.81.81,0,0,1,0,.891A.791.791,0,0,1,16427.109,902ZM16420,887a7,7,0,1,0,0,14h6.283c.275,0,.414,0,.549-.111s-.209-.574-.34-.748l0,0-.018-.022-1.064-1.6A6.829,6.829,0,0,0,16427,894a6.964,6.964,0,0,0-7-7Z" transform="translate(-16096 180)" fill="#b5b5bf" />
                                                <path id="Union_12" data-name="Union 12" d="M16414,895a1,1,0,1,1,1,1A1,1,0,0,1,16414,895Zm.5-2.5V891h.5a2,2,0,1,0-2-2h-1a3,3,0,1,1,3.5,2.958v.54a.5.5,0,1,1-1,0Zm-2.5-3.5h1a.5.5,0,1,1-1,0Z" transform="translate(-16090.998 183.001)" fill="#b5b5bf" />
                                            </g>
                                        </svg>
                                        <span class="user-top-menu-name has-transition ml-3">{{ translate('Support Ticket') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="user-top-nav-element border border-top-0" data-id="1">
                                <a href="{{ route('logout') }}" class="text-truncate text-dark px-4 fs-14 d-flex align-items-center hov-column-gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15.999" viewBox="0 0 16 15.999">
                                        <g id="Group_25503" data-name="Group 25503" transform="translate(-24.002 -377)">
                                            <g id="Group_25265" data-name="Group 25265" transform="translate(-216.534 -160)">
                                                <path id="Subtraction_192" data-name="Subtraction 192" d="M12052.535,2920a8,8,0,0,1-4.569-14.567l.721.72a7,7,0,1,0,7.7,0l.721-.72a8,8,0,0,1-4.567,14.567Z" transform="translate(-11803.999 -2367)" fill="#d43533" />
                                            </g>
                                            <rect id="Rectangle_19022" data-name="Rectangle 19022" width="1" height="8" rx="0.5" transform="translate(31.5 377)" fill="#d43533" />
                                        </g>
                                    </svg>
                                    <span class="user-top-menu-name text-primary has-transition ml-3">{{ translate('Logout') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Menu Bar with Bootstrap Navbar -->
        <div class="d-none d-lg-block position-relative h-50px">
            <nav class="navbar navbar-expand-lg navbar-light bg-light h-100">
                <div class="container">
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav w-100 justify-content-center justify-content-xl-start">
                            @foreach (\App\Models\Category::with('subCategories')->where('parent_id', 0)->take(7)->get() as $category)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle fs-5 fw-700 text-dark" 
                                    href="{{ route('products.category', $category->slug) }}" 
                                    id="navbarDropdown_{{ $category->id }}" 
                                    role="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false">
                                        {{ $category->getTranslation('name') }}
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown_{{ $category->id }}" data-bs-boundary="viewport">
                                        @if ($category->subCategories->isEmpty())
                                            <a class="dropdown-item text-muted" href="{{ route('products.category', $category->slug) }}">
                                                {{ translate('No subcategories available') }}
                                            </a>
                                        @else
                                            <div class="p-3">
                                                <div class="row">
                                                    @if ($category->subCategories->count() > 30)
                                                        <!-- 4 Column Layout for > 30 subcategories -->
                                                        @for ($i = 0; $i < 4; $i++)
                                                            <div class="col-3">
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach ($category->subCategories->slice($i * ceil($category->subCategories->count() / 4), ceil($category->subCategories->count() / 4)) as $subcategory)
                                                                        <li class="mb-3 position-relative subcategory-item">
                                                                            <a class="text-reset fs-14 hov-text-primary d-block" href="{{ route('products.category', $subcategory->slug) }}">
                                                                                {{ $subcategory->getTranslation('name') }}
                                                                            </a>
                                                                            @if ($subcategory->subCategories->isNotEmpty())
                                                                                <div class="child-dropdown">
                                                                                    <ul class="list-unstyled mb-0">
                                                                                        @foreach ($subcategory->subCategories as $childCategory)
                                                                                            <li class="mb-2">
                                                                                                <a class="text-reset fs-14 hov-text-primary" href="{{ route('products.category', $childCategory->slug) }}">
                                                                                                    {{ $childCategory->getTranslation('name') }}
                                                                                                </a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endfor
                                                    @elseif ($category->subCategories->count() > 20)
                                                        <!-- 3 Column Layout for 21-30 subcategories -->
                                                        @for ($i = 0; $i < 3; $i++)
                                                            <div class="col-4">
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach ($category->subCategories->slice($i * ceil($category->subCategories->count() / 3), ceil($category->subCategories->count() / 3)) as $subcategory)
                                                                        <li class="mb-3 position-relative subcategory-item">
                                                                            <a class="text-reset fs-14 hov-text-primary d-block" href="{{ route('products.category', $subcategory->slug) }}">
                                                                                {{ $subcategory->getTranslation('name') }}
                                                                            </a>
                                                                            @if ($subcategory->subCategories->isNotEmpty())
                                                                                <div class="child-dropdown">
                                                                                    <ul class="list-unstyled mb-0">
                                                                                        @foreach ($subcategory->subCategories as $childCategory)
                                                                                            <li class="mb-2">
                                                                                                <a class="text-reset fs-14 hov-text-primary" href="{{ route('products.category', $childCategory->slug) }}">
                                                                                                    {{ $childCategory->getTranslation('name') }}
                                                                                                </a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endfor
                                                    @else
                                                        <!-- 2 Column Layout for <= 20 subcategories -->
                                                        @for ($i = 0; $i < 2; $i++)
                                                            <div class="col-6">
                                                                <ul class="list-unstyled mb-0">
                                                                    @foreach ($category->subCategories->slice($i * ceil($category->subCategories->count() / 2), ceil($category->subCategories->count() / 2)) as $subcategory)
                                                                        <li class="mb-3 position-relative subcategory-item">
                                                                            <a class="text-reset fs-14 hov-text-primary d-block" href="{{ route('products.category', $subcategory->slug) }}">
                                                                                {{ $subcategory->getTranslation('name') }}
                                                                            </a>
                                                                            @if ($subcategory->subCategories->isNotEmpty())
                                                                                <div class="child-dropdown">
                                                                                    <ul class="list-unstyled mb-0">
                                                                                        @foreach ($subcategory->subCategories as $childCategory)
                                                                                            <li class="mb-2">
                                                                                                <a class="text-reset fs-14 hov-text-primary" href="{{ route('products.category', $childCategory->slug) }}">
                                                                                                    {{ $childCategory->getTranslation('name') }}
                                                                                                </a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    </ul>
                                                                                </div>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endfor
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>

<!-- Top Menu Sidebar -->
<div class="aiz-top-menu-sidebar collapse-sidebar-wrap sidebar-xl sidebar-left d-lg-none z-1035">
    <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle" data-target=".aiz-top-menu-sidebar" data-same=".hide-top-menu-bar"></div>
    <div class="collapse-sidebar c-scrollbar-light text-left">
        <button type="button" class="btn btn-sm p-4 hide-top-menu-bar" data-toggle="class-toggle" data-target=".aiz-top-menu-sidebar">
            <i class="las la-times la-2x text-primary"></i>
        </button>
        @auth
            <span class="d-flex align-items-center nav-user-info pl-4">
                <span class="size-40px rounded-circle overflow-hidden border border-transparent nav-user-img">
                    @if ($user->avatar_original != null)
                        <img src="{{ asset($user->avatar_original) }}" class="img-fit h-100" alt="{{ translate('avatar') }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                    @else
                        <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image" alt="{{ translate('avatar') }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
                    @endif
                </span>
                <h4 class="h5 fs-14 fw-700 text-dark ml-2 mb-0">{{ $user->name }}</h4>
            </span>
        @else
            <span class="d-flex align-items-center nav-user-info pl-4">
                <span class="size-40px rounded-circle overflow-hidden border d-flex align-items-center justify-content-center nav-user-img">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19.902" height="20.012" viewBox="0 0 19.902 20.012">
                        <path id="fe2df171891038b33e9624c27e96e367" d="M15.71,12.71a6,6,0,1,0-7.42,0,10,10,0,0,0-6.22,8.18,1.006,1.006,0,1,0,2,.22,8,8,0,0,1,15.9,0,1,1,0,0,0,1,.89h.11a1,1,0,0,0,.88-1.1,10,10,0,0,0-6.25-8.19ZM12,12a4,4,0,1,1,4-4A4,4,0,0,1,12,12Z" transform="translate(-2.064 -1.995)" fill="#91919b" />
                    </svg>
                </span>
                <a href="javascript:void(0)" onclick="showLoginModal()" class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block border-right border-soft-light border-width-2 pr-2 ml-3">{{ translate('Login') }}</a>
                <a href="javascript:void(0)" onclick="showLoginModal()" class="text-reset opacity-60 hov-opacity-100 hov-text-primary fs-12 d-inline-block py-2 pl-2">{{ translate('Registration') }}</a>
            </span>
        @endauth
        <hr>
        <ul class="mb-0 pl-3 pb-3 h-100">
            @if (get_setting('header_menu_labels') != null)
                @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                    <li class="mr-0">
                        <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                            class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links
                            @if (url()->current() == json_decode(get_setting('header_menu_links'), true)[$key]) active @endif">
                            {{ translate($value) }}
                        </a>
                    </li>
                @endforeach
            @endif
            @auth
                @if (isAdmin())
                    <hr>
                    <li class="mr-0">
                        <a href="{{ route('admin.dashboard') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links">
                            {{ translate('My Account') }}
                        </a>
                    </li>
                @else
                    <hr>
                    <li class="mr-0">
                        <a href="{{ route('dashboard') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links {{ areActiveRoutes(['dashboard'], ' active') }}">
                            {{ translate('My Account') }}
                        </a>
                    </li>
                @endif
                @if (isCustomer())
                    <li class="mr-0">
                        <a href="{{ route('customer.all-notifications') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links {{ areActiveRoutes(['customer.all-notifications'], ' active') }}">
                            {{ translate('Notifications') }}
                        </a>
                    </li>
                    <li class="mr-0">
                        <a href="{{ route('wishlists.index') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links {{ areActiveRoutes(['wishlists.index'], ' active') }}">
                            {{ translate('Wishlist') }}
                        </a>
                    </li>
                    <li class="mr-0">
                        <a href="{{ route('compare') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-dark header_menu_links {{ areActiveRoutes(['compare'], ' active') }}">
                            {{ translate('Compare') }}
                        </a>
                    </li>
                @endif
                <hr>
                <li class="mr-0">
                    <a href="{{ route('logout') }}" class="fs-13 px-3 py-3 w-100 d-inline-block fw-700 text-primary header_menu_links">
                        {{ translate('Logout') }}
                    </a>
                </li>
            @endauth
        </ul>
        <br><br>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body" style="min-height: 380px">
                <!-- OTP Step -->
                <div class="row g-0" id="otpStep" style="display: none;">
                    <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-0 border-end">
                        <img src="https://drinfrarealtors.one/public/uploads/all/akshat-health.jpg" class="img-fluid h-100 w-100 object-fit-cover" alt="Login Image">
                    </div>
                    <div class="col-md-6 col-12 p-2 d-flex flex-column justify-content-center">
                        <div class="text-center mb-4">
                            <h4 class="mb-2 fw-bold">LOGIN / SIGN UP</h4>
                            <p class="text-muted fs-6">Get access to your orders, lab tests & doctor consultations</p>
                        </div>
                        <div class="w-100 mx-auto" style="max-width: 300px;">
                            <div id="sotp">
                                <form method="POST">
                                    <div class="mb-3 position-relative d-flex align-items-center">
                                        <span class="mobile-prefix" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #999; pointer-events: none;">+91</span>
                                        <input type="text" class="form-control pl-40" id="mobileNumber" name="mobileNumber" placeholder="Enter Mobile Number" maxlength="10" pattern="[0-9]{10}" required style="border: none; border-bottom: 1px solid #000; border-radius: 0; padding: 0.5rem 2.5rem 0.5rem 40px; background: transparent; outline: none; font-size: 0.875rem;">
                                        <button type="button" onclick="sendOtp()" class="btn p-0 position-absolute" style="right: 0; top: 50%; transform: translateY(-50%); background: #26a69a; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mb-2 text-muted fs-6">Accept <a href="#" class="text-decoration-underline">Terms & Conditions</a></p>
                                </form>
                            </div>
                            <div id="verOtp" style="display: none;">
                                <div class="text-center mb-3">
                                    <p>
                                        <span id="editOtpText" style="font-size: 1rem; color: #333;">Provide OTP sent to </span>
                                        <!--<span id="mobileDisplay" style="font-size: 1rem; color: #333; font-weight: bold;">8398030302</span>-->
                                        <!--<span style="display: inline-flex; align-items: center; background-color: #28a745; padding: 2px 5px; border-radius: 3px; color: white; font-size: 12px; margin-left: 5px;">-->
                                        <!--    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">-->
                                        <!--        <path d="M20 6L9 17l-5-5"></path>-->
                                        <!--    </svg>-->
                                        <!--</span>-->
                                        <a href="javascript:;" onClick="editOtpNumber()" style="margin-left: 10px;">
                                            <span style="display: inline-flex; align-items: center; background-color: #28a745; padding: 2px 5px; border-radius: 3px; color: white; font-size: 12px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                            </span>
                                        </a>
                                    </p>
                                </div>
                                <form method="POST">
                                    <div class="mb-3 position-relative d-flex justify-content-center align-items-center">
                                        <div class="otp-container d-flex align-items-center" style="gap: 0.5rem;"> <!-- Added gap for spacing -->
                                            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" style="width: 40px; height: 40px; text-align: center; border: none; border-bottom: 1px dashed #000; border-radius: 0; padding: 0; font-size: 1.2rem; background: transparent; outline: none; margin: 0;">
                                            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" style="width: 40px; height: 40px; text-align: center; border: none; border-bottom: 1px dashed #000; border-radius: 0; padding: 0; font-size: 1.2rem; background: transparent; outline: none; margin: 0;">
                                            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" style="width: 40px; height: 40px; text-align: center; border: none; border-bottom: 1px dashed #000; border-radius: 0; padding: 0; font-size: 1.2rem; background: transparent; outline: none; margin: 0;">
                                            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, event)" style="width: 40px; height: 40px; text-align: center; border: none; border-bottom: 1px dashed #000; border-radius: 0; padding: 0; font-size: 1.2rem; background: transparent; outline: none; margin: 0;">
                                        </div>
                                        <button type="button" onclick="verifyOtp()" class="btn p-0" style="background: #26a69a; border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; margin-left: 1rem;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"></path>
                                                <path d="M12 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="text-center mt-2">
                                        <span id="otpResend" style="display: none; font-size: 0.75rem; color: #999;">Resend OTP in <span id="otpTimer"></span> seconds</span>
                                    </div>
                                    <div id="otpResendBtn" style="display: none;" class="mt-2 text-center">
                                        <button type="button" class="btn btn-link text-info p-0" onclick="resendOtp()" style="font-size: 0.75rem; text-decoration: none;">Resend OTP</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="row g-0" id="registratioFormStep" style="display: none;">
                    <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center p-0 border-end">
                        <img src="https://www.1mg.com/images/login-signup/Lab-Tests-at-Home.png" class="img-fluid h-100 w-100 object-fit-cover" alt="Registration Image">
                    </div>
                    <div class="col-md-6 col-12 p-2 d-flex flex-column justify-content-center" style="min-height: 400px;">
                        <div class="text-center mb-2">
                            <h4 class="mb-1 fw-bold" style="font-size: 1rem;">CREATE ACCOUNT</h4>
                            <p class="text-muted fs-6" style="font-size: 0.75rem;">Complete your profile to get started</p>
                        </div>
                        <div class="w-100 mx-auto" style="max-width: 250px;">
                            <form class="reg-form needs-validation" novalidate>
                                <div class="mb-3 text-center">
                                    <div class="d-flex justify-content-center flex-wrap gap-1">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="user_type" id="wholeseller" value="wholeseller">
                                            <label class="form-check-label fs-6" for="wholeseller" style="font-size: 0.7rem;">Wholeseller</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="user_type" id="seller" value="seller">
                                            <label class="form-check-label fs-6" for="seller" style="font-size: 0.7rem;">Seller</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="user_type" id="customer" value="customer" checked>
                                            <label class="form-check-label fs-6" for="customer" style="font-size: 0.7rem;">Customer</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <input type="text" name="first_name" id="first_name" class="form-control form-control-sm" placeholder="Name" required style="font-size: 0.8rem; padding: 0.3rem 0.5rem;">
                                </div>
                                <div class="mb-2">
                                    <input type="tel" name="mobile_no" id="mobile_no" class="form-control form-control-sm rounded-3" placeholder="Mobile Number" hidden required style="font-size: 0.8rem; padding: 0.3rem 0.5rem;">
                                </div>
                                <div class="mb-2 position-relative">
                                    <input type="email" name="email_id" id="email_id" class="form-control form-control-sm" 
                                           placeholder="Email Address" required 
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" 
                                           title="Please enter a valid email address (e.g., example@domain.com)"
                                           style="font-size: 0.8rem; padding: 0.3rem 0.5rem;">
                                    <div class="invalid-feedback" style="font-size: 0.7rem; color: #dc3545; position: absolute; bottom: -1.5rem; left: 0;">
                                        Please enter a valid email address.
                                    </div>
                                </div>
                                <div class="mb-2 position-relative">
                                    <input type="text" name="billing_address" id="billing_address" class="form-control form-control-sm" 
                                           placeholder="Billing Address" required style="font-size: 0.8rem; padding: 0.3rem 2rem 0.3rem 0.5rem;">
                                    <button type="button" id="fetchLocationBtn" class="btn p-0 position-absolute" 
                                            style="right: 5px; top: 50%; transform: translateY(-50%); background: #26a69a; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center;"
                                            title="Fetch my location">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                    </button>
                                </div>
                                <div class="mb-2" id="org_seller" style="display: none;">
                                    <input type="text" name="gst_no" id="gst_no" 
                                           class="form-control form-control-sm" 
                                           placeholder="GST/Registration No*" 
                                           style="font-size: 0.8rem; padding: 0.3rem 0.5rem; text-transform: uppercase;"
                                           maxlength="15">
                                </div>
                                <div class="mb-2" id="org_seller2" style="display: none;">
                                    <input type="text" name="drug_license_no" id="drug_license_no" 
                                           class="form-control form-control-sm" 
                                           placeholder="Drug License No*" 
                                           style="font-size: 0.8rem; padding: 0.3rem 0.5rem; text-transform: uppercase;">
                                </div>
                                <div id="pan_card_group" class="form-group"  style="display: none;">
                                    <input type="text" class="form-control form-control-sm" id="pan_card" name="pan_card" placeholder="PAN Card No e.g., ABCDE1234F" maxlength="10">
                                    <div class="invalid-feedback">Please enter a valid 10-character PAN Card (e.g., ABCDE1234F).</div>
                                </div>
                                <div class="mb-2">
                                    <label for="photo" id="photo-label" class="form-label">Upload Photo</label>
                                    <input type="file" name="photo" id="photo" 
                                           class="form-control form-control-sm" 
                                           accept="image/*" 
                                           style="font-size: 0.8rem; padding: 0.3rem 0;">
                                </div>
                                <button type="submit" name="submit" id="submit" class="btn btn-info w-100 rounded-pill mb-2" style="font-size: 0.8rem; padding: 0.3rem 0.75rem;">Register Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection