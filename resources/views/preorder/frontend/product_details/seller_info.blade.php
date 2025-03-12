@if ($product->user->shop != null)
    <div class="border my-4 rounded-2" style="background: #fcfcfd;" id="sellerDetails">
        <div class="position-relative p-3 p-sm-4 text-left">
            <div class="fs-14 mb-3 text-uppercase">{{ translate('Seller Details') }}</div>
            <div class="d-flex justify-content-between">
                <div class="mt-1">
                    <!-- Shop Logo -->
                    @if ( get_setting('vendor_system_activation') == 1)
                    <a href="{{ route('shop.visit', $product->user->shop->slug) }}" class="avatar avatar-md mr-2 overflow-hidden border float-left float-lg-none float-xl-left">
                        <img class="lazyload"
                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                            data-src="{{ uploaded_asset($product->user->shop->logo) }}"
                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                    </a>
                    @endif
                    <!-- Shop Name & Verification status -->
                    <div>
                        <a href="{{ route('shop.visit', $product->user->shop->slug) }}"
                            class="text-reset hov-text-primary d-block fs-14 fw-700">
                            {{ $product->user->shop->name }}
                            @if ($product->user->shop->verification_status == 1)
                                <span class="ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
                                        <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
                                            <path id="Union_5" data-name="Union 5" d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z" transform="translate(537.249 1042.75)" fill="#3490f3"/>
                                        </g>
                                    </svg>
                                </span>
                            @else
                                <span class="ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
                                        <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
                                            <path id="Union_5" data-name="Union 5" d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z" transform="translate(537.249 1042.75)" fill="red"/>
                                        </g>
                                    </svg>
                                </span>
                            @endif
                        </a>
                        <div class="location opacity-70">{{ $product->user->shop->address }}</div>
                    </div>
                </div>
                <div class="float-right">
                    <div class="aiz-share"></div>
                </div>
            </div>
            <!-- Ratting -->
            <div class="mt-3">
                <div class="rating rating-mr-2">
                    {{ renderStarRating($product->user->shop->rating) }}
                </div>
                <div class="opacity-60 fs-12">
                    ({{ $product->user->shop->num_of_reviews }}
                    {{ translate('customer reviews') }})
                </div>
            </div>
            <hr>
            <!-- why choose us -->

            <div >
                <p><b>{{translate('Why Choos Us!')}}</b></p>
                <p class=" m-0 p-0"> <i class="las la-check "></i>  Verified seller at {{env('APP_NAME')}}</p>
                <p class=" m-0 p-0"> <i class="las la-check "></i>  {{100}} + delivery</p>
                <p class=" m-0 p-0"> <i class="las la-check "></i>  Capable of wholesale production</p>
                <p class=" m-0 p-0"> <i class="las la-check "></i>  24/7 Support</p>
            </div>

            <!-- Social Links -->
            @if ($product->user->shop->facebook || $product->user->shop->google || $product->user->shop->twitter || $product->user->shop->youtube)
                <div class="mt-3">
                    <ul class="social list-inline mb-0">
                        @if ($product->user->shop->facebook)
                        <li class="list-inline-item mr-2 mb-2">
                            <a href="{{ $product->user->shop->facebook }}" class="facebook"
                                target="_blank">
                                <i class="lab la-facebook-f opacity-60"></i>
                            </a>
                        </li>
                        @endif
                        @if ($product->user->shop->instagram)
                        <li class="list-inline-item mr-2 mb-2">
                            <a href="{{ $product->user->shop->instagram }}" class="instagram"
                                target="_blank">
                                <i class="lab la-instagram opacity-60"></i>
                            </a>
                        </li>
                        @endif
                        @if ($product->user->shop->google)
                        <li class="list-inline-item mr-2 mb-2">
                            <a href="{{ $product->user->shop->google }}" class="google"
                                target="_blank">
                                <i class="lab la-google opacity-60"></i>
                            </a>
                        </li>
                        @endif
                        @if ($product->user->shop->twitter)
                        <li class="list-inline-item mr-2 mb-2">
                            <a href="{{ $product->user->shop->twitter }}" class="twitter"
                                target="_blank">
                                <i class="lab la-twitter opacity-60"></i>
                            </a>
                        </li>
                        @endif
                        @if ($product->user->shop->youtube)
                        <li class="list-inline-item">
                            <a href="{{ $product->user->shop->youtube }}" class="youtube"
                                target="_blank">
                                <i class="lab la-youtube opacity-60"></i>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
            @endif
            <!-- shop link button -->
            <div class="button-section mt-3 d-flex row">
                <div class="col-6">
                    <a href="{{ route('shop.visit.type', ['slug'=>$product->user->shop->slug, 'type'=>'all-preorder-products'])  }}"
                        class="btn btn-block btn-outline-primary fs-14 fw-700 rounded-0">{{ translate('View More Products') }}</a>
                </div>
                <div class="col-6">
                    <a href="{{ route('shop.visit', $product->user->shop->slug) }}"
                        class="btn btn-block btn-primary fs-14 fw-700 rounded-0">{{ translate('Visit Store') }}</a>
                </div>
            </div>
        </div>
    </div>
@endif
