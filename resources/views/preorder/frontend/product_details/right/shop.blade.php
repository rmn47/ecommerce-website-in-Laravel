<div class="border mb-4 p-2 mt-4 rounded-2" style="background-color: #F5F7FD;">

    @if($product->user->user_type == 'seller')
        <div class="d-flex justify-content-between ">
            <div class="mt-1 d-flex">
                <!-- Shop Logo -->
                <div>
                    <a href="{{ route('shop.visit', $product->user->shop->slug) }}"
                        class=" avatar-md mr-2 overflow-hidden border float-left float-lg-none float-xl-left h-40px">
                        <img class="lazyload  ls-is-cached lazyloaded d-block mx-auto mh-100 border p-1" src="{{ static_asset('assets/img/placeholder.jpg') }}"
                            data-src="{{ uploaded_asset($product->user->shop->logo) }}"
                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                    </a>
                </div>
                <!-- Shop Name & Verification status -->
                <div class="mt-2">
                    <div class="ml-2"> <b>{{ $product->user->shop->name }}</b></div>

                    <div class=" opacity-70">
                            @if ($product->user->shop->verification_status == 1)
                            <span class="ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
                                    <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
                                        <path id="Union_5" data-name="Union 5"
                                            d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z"
                                            transform="translate(537.249 1042.75)" fill="#6bb300" />
                                    </g>
                                </svg>
                            </span>

                            <span >{{translate('verified seller')}}</span>
                            @else
                            <span class="ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17.5" height="17.5" viewBox="0 0 17.5 17.5">
                                    <g id="Group_25616" data-name="Group 25616" transform="translate(-537.249 -1042.75)">
                                        <path id="Union_5" data-name="Union 5"
                                            d="M0,8.75A8.75,8.75,0,1,1,8.75,17.5,8.75,8.75,0,0,1,0,8.75Zm.876,0A7.875,7.875,0,1,0,8.75.875,7.883,7.883,0,0,0,.876,8.75Zm.875,0a7,7,0,1,1,7,7A7.008,7.008,0,0,1,1.751,8.751Zm3.73-.907a.789.789,0,0,0,0,1.115l2.23,2.23a.788.788,0,0,0,1.115,0l3.717-3.717a.789.789,0,0,0,0-1.115.788.788,0,0,0-1.115,0l-3.16,3.16L6.6,7.844a.788.788,0,0,0-1.115,0Z"
                                            transform="translate(537.249 1042.75)" fill="red" />
                                    </g>
                                </svg>
                            </span>
                            <span >{{translate('Not verified')}}</span>
                            @endif
                        {{-- </span> --}}
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                @if(get_setting('product_query_activation') == 1)
                <button
                    class="btn btn-sm btn-soft-secondary-base btn-outline-secondary-base hov-svg-white hov-text-white rounded-4"
                    onclick="show_conversation_modal({{ $product->id }})">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                        class="mr-2 has-transition">
                        <g id="Group_23918" data-name="Group 23918" transform="translate(1053.151 256.688)">
                            <path id="Path_3012" data-name="Path 3012"
                                d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                                transform="translate(-1178 -341)"
                                fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                            <path id="Path_3013" data-name="Path 3013"
                                d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                                transform="translate(-1182 -337)"
                                fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                            <path id="Path_3014" data-name="Path 3014"
                                d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)"
                                fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                            <path id="Path_3015" data-name="Path 3015"
                                d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)"
                                fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                        </g>
                    </svg>

                    {{ translate('Message Seller') }}
                </button>
                @endif
            </div>
        </div>
        <hr>
        <div class="section-footer d-flex justify-content-between mb-0 ">
            <div>
                <p class="text-primary"><b><a href="#sellerDetails">{{translate('Seller Details')}}</a></b></p>
            </div>
            <div>
                <p class="text-primary"><b><a href="{{ route('shop.visit', $product->user->shop->slug) }}">{{translate('Visit Store')}}</a></b></p>
            </div>
        </div>
    @else
        <div class="d-flex align-items-center">
            <span class="px-3 fs-16">{{translate('In House Product')}}</span>

            @if(get_setting('product_query_activation') == 1)
            <button
                class="btn btn-sm btn-soft-secondary-base btn-outline-secondary-base hov-svg-white hov-text-white rounded-4"
                onclick="show_conversation_modal({{ $product->id }})">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                    class="mr-2 has-transition">
                    <g id="Group_23918" data-name="Group 23918" transform="translate(1053.151 256.688)">
                        <path id="Path_3012" data-name="Path 3012"
                            d="M134.849,88.312h-8a2,2,0,0,0-2,2v5a2,2,0,0,0,2,2v3l2.4-3h5.6a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2m1,7a1,1,0,0,1-1,1h-8a1,1,0,0,1-1-1v-5a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1Z"
                            transform="translate(-1178 -341)"
                            fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                        <path id="Path_3013" data-name="Path 3013"
                            d="M134.849,81.312h8a1,1,0,0,1,1,1v5a1,1,0,0,1-1,1h-.5a.5.5,0,0,0,0,1h.5a2,2,0,0,0,2-2v-5a2,2,0,0,0-2-2h-8a2,2,0,0,0-2,2v.5a.5.5,0,0,0,1,0v-.5a1,1,0,0,1,1-1"
                            transform="translate(-1182 -337)"
                            fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                        <path id="Path_3014" data-name="Path 3014"
                            d="M131.349,93.312h5a.5.5,0,0,1,0,1h-5a.5.5,0,0,1,0-1" transform="translate(-1181 -343.5)"
                            fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                        <path id="Path_3015" data-name="Path 3015"
                            d="M131.349,99.312h5a.5.5,0,1,1,0,1h-5a.5.5,0,1,1,0-1" transform="translate(-1181 -346.5)"
                            fill="{{ get_setting('secondary_base_color', '#ffc519') }}" />
                    </g>
                </svg>

                {{ translate('Message Seller') }}
            </button>
            @endif
        </div>
        
        <div class="row no-gutters mt-4 px-3">
            <div class="col-sm-2">
                <div class="text-secondary fs-14 fw-400 mt-2">{{ translate('Share') }}</div>
            </div>
            <div class="col-sm-10">
                <div class="aiz-share"></div>
            </div>
        </div>

        
    @endif
</div>