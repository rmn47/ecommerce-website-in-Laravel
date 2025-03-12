<!-- New Address Modal -->
<div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form class="form-default" role="form" action="{{ route('addresses.store') }}" method="POST">
                @csrf
                <div class="modal-body c-scrollbar-light">
                    <div class="p-3">
                        <!-- Address -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Address') }}</label>
                            </div>
                            <div class="col-md-10">
                                <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address') }}" rows="2" name="address" required></textarea>
                            </div>
                        </div>

                        <!-- Country -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Country') }}</label>
                            </div>
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" id="new_country" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (get_active_countries() as $key => $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- State -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('State') }}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="state_id" id="new_state" required>
                                    <option value="">{{ translate('Select your state') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- City -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('City') }}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="city_id" id="new_city" required>
                                    <option value="">{{ translate('Select your city') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pincode -->
                        <div class="row">
                            <div class="col-md-2">
                                <label>{{ translate('Pincode') }}</label>
                            </div>
                            <div class="col-md-10">
                                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="postal_code" id="new_pincode" required>
                                    <option value="">{{ translate('Select Pincode') }}</option>
                                </select>
                            </div>
                        </div>

                        @if (get_setting('google_map') == 1)
                            <!-- Google Map -->
                            <div class="row mt-3 mb-3">
                                <input id="searchInput" class="controls" type="text" placeholder="{{ translate('Enter a location') }}">
                                <div id="map"></div>
                                <ul id="geoData">
                                    <li style="display: none;">Full Address: <span id="location"></span></li>
                                    <li style="display: none;">Postal Code: <span id="postal_code"></span></li>
                                    <li style="display: none;">Country: <span id="country"></span></li>
                                    <li style="display: none;">Latitude: <span id="lat"></span></li>
                                    <li style="display: none;">Longitude: <span id="lon"></span></li>
                                </ul>
                            </div>
                            <!-- Longitude -->
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Longitude') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3 rounded-0" id="longitude" name="longitude" readonly="">
                                </div>
                            </div>
                            <!-- Latitude -->
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Latitude') }}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3 rounded-0" id="latitude" name="latitude" readonly="">
                                </div>
                            </div>
                        @endif

                        <!-- Phone -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label>{{ translate('Phone') }}</label>
                            </div>
                            <div class="col-md-10">
                                <input type="tel" id="phone-code" class="form-control rounded-0" placeholder="" name="phone" autocomplete="off" required>
                                <input type="hidden" name="country_code" value="">
                            </div>
                        </div>

                        <!-- Save button -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary rounded-0 w-150px">{{ translate('Save') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">{{ translate('Edit Address') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body c-scrollbar-light" id="edit_modal_body">
                <!-- Content will be loaded dynamically via AJAX -->
            </div>
        </div>
    </div>
</div>