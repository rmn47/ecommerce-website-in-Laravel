<div class="row gutters-16">
    <!-- Product List -->
    <div class="col-md-6">
        <ul class="list-group list-group-flush mb-3">

                <li class="list-group-item pl-0 py-3 border-0">
                    <div class="d-flex align-items-center">
                        <span class="mr-2 mr-md-3">
                            <img src="{{ uploaded_asset($product?->thumbnail) }}"
                                class="img-fit size-60px break-word"
                                alt="{{  $product?->getTranslation('name')  }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </span>
                        <span class="fs-14 fw-400 text-dark">
                            <span class="text-truncate-2 break-word">{{ $product?->product_name }}</span>
                        </span>
                    </div>
                </li>

        </ul>
    </div>


        <!-- Choose Delivery Type -->
        <div class="col-md-6 mb-2">
            <h6 class="fs-14 fw-700 mt-3">{{ translate('Choose Delivery Type') }}</h6>
            <div class="row gutters-16">
                <!-- Home Delivery -->
                @if (get_setting('shipping_type') != 'carrier_wise_shipping')
                <div class="col-6">
                    <label class="aiz-megabox d-block bg-white mb-0">
                        <input
                            type="radio"
                            name="shipping_type"
                            value="home_delivery"
                            onchange="show_pickup_point(this, {{ $owner_id }})"
                            data-target=".pickup_point_id_{{ $owner_id }}"
                            @if($order->delivery_type == 'home_delivery')checked @endif required
                        >
                        <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                            <span class="flex-grow-1 pl-3 fw-600">{{  translate('Home Delivery') }}</span>
                        </span>
                    </label>
                </div>
                <!-- Carrier -->
                @else
                <div class="col-6">
                    <label class="aiz-megabox d-block bg-white mb-0">
                        <input
                            type="radio"
                            name="shipping_type"
                            value="carrier"
                            data-owner="{{ $owner_id }}"
                            onchange="show_pickup_point(this, {{ $owner_id }})"
                            data-target=".pickup_point_id_{{ $owner_id }}"
                            checked required
                        >
                        <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                            <span class="flex-grow-1 pl-3 fw-600">{{  translate('Carrier') }}</span>
                        </span>
                    </label>
                </div>
                @endif
                <!-- Local Pickup -->
                @if ($pickup_point_list)
                <div class="col-6">
                    <label class="aiz-megabox d-block bg-white mb-0">
                        <input
                            type="radio"
                            name="shipping_type"
                            value="pickup_point"
                            data-owner="{{ $owner_id }}"
                            onchange="show_pickup_point(this, {{ $owner_id }})"
                            data-target=".pickup_point_id_{{ $owner_id }}"
                            required
                            @if($order->delivery_type == 'pickup_point')checked @endif
                        >
                        <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                            <span class="flex-grow-1 pl-3 fw-600">{{  translate('Local Pickup') }}</span>
                        </span>
                    </label>
                </div>
                @endif
            </div>

            <!-- Pickup Point List -->
            @if ($pickup_point_list)
                <div class="mt-3 pickup_point_id_{{ $owner_id }}  {{$order->delivery_type == 'pickup_point'? '':'d-none'}}">
                    <select
                        class="form-control aiz-selectpicker rounded-0"
                        name="pickup_point_id"
                        data-live-search="true"
                        onchange="updateDeliveryInfo('pickup_point', this.value, {{ $owner_id }})"
                    >
                        <option value="">{{ translate('Select your nearest pickup point')}}</option>
                        @foreach ($pickup_point_list as $pick_up_point)
                            <option
                                value="{{ $pick_up_point->id }}"
                                data-content="<span class='d-block'>
                                                <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>
                                                <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>
                                                <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>
                                            </span>"
                                @selected($order->pickup_point_id == $pick_up_point->id)>
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Carrier Wise Shipping -->
            @if (get_setting('shipping_type') == 'carrier_wise_shipping')
                <div class="row pt-3 carrier_id_{{ $owner_id }}">
                    @foreach($carrier_list as $carrier_key => $carrier)
                        <div class="col-md-12 mb-2">
                            <label class="aiz-megabox d-block bg-white mb-0">
                                <input
                                    type="radio"
                                    name="carrier_id_{{ $owner_id }}"
                                    value="{{ $carrier->id }}"
                                    @if($carrier_key == 0) checked @endif
                                    onchange="updateDeliveryInfo('carrier', {{ $carrier->id }}, {{ $owner_id }})"
                                >
                                <span class="d-flex flex-wrap p-3 aiz-megabox-elem rounded-0">
                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                    <span class="flex-grow-1 pl-3 fw-600">
                                        <img src="{{ uploaded_asset($carrier->logo)}}" alt="Image" class="w-50px img-fit">
                                    </span>
                                    <span class="flex-grow-1 pl-3 fw-700">{{ $carrier->name }}</span>
                                    <span class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in').' '.$carrier->transit_time }}</span>
                                    {{-- <span class="flex-grow-1 pl-4 pl-sm-3 fw-600 mt-2 mt-sm-0 text-sm-right">{{ single_price(carrier_base_price($carts, $carrier->id, $owner_id, $shipping_info)) }}</span> --}}
                                </span>
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
</div>
