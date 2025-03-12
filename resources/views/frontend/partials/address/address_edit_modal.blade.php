<form class="form-default" role="form" action="{{ route('addresses.update', $address_data->id) }}" method="POST">
    @csrf
    <!--@method('PUT')-->
    <div class="p-3">
        <!-- Address -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Address') }}</label>
            </div>
            <div class="col-md-10">
                <textarea class="form-control mb-3 rounded-0" placeholder="{{ translate('Your Address') }}" rows="2" name="address" required>{{ $address_data->address }}</textarea>
            </div>
        </div>

        <!-- Country -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Country') }}</label>
            </div>
            <div class="col-md-10">
                <div class="mb-3">
                    <select class="form-control aiz-selectpicker rounded-0" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" id="edit_country" required>
                        <option value="">{{ translate('Select your country') }}</option>
                        @foreach (get_active_countries() as $key => $country)
                            <option value="{{ $country->id }}" @if($address_data->country_id == $country->id) selected @endif>{{ $country->name }}</option>
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
                <select class="form-control mb-3 aiz-selectpicker rounded-0" name="state_id" id="edit_state" data-live-search="true" required>
                    <option value="">{{ translate('Select your state') }}</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" @if($address_data->state_id == $state->id) selected @endif>{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- City -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('City') }}</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="city_id" id="edit_city" required>
                    <option value="">{{ translate('Select your city') }}</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}" @if($address_data->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Pincode -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Pincode') }}</label>
            </div>
            <div class="col-md-10">
                <select class="form-control mb-3 aiz-selectpicker rounded-0" data-live-search="true" name="postal_code" id="edit_pincode" required>
                    <option value="">{{ translate('Select Pincode') }}</option>
                    @foreach ($pincodes as $pincode)
                        <option value="{{ $pincode->pincode }}" @if($address_data->postal_code == $pincode->pincode) selected @endif>{{ $pincode->pincode }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if (get_setting('google_map') == 1)
            <!-- Google Map -->
            <div class="row mt-3 mb-3">
                <input id="edit_searchInput" class="controls" type="text" placeholder="{{ translate('Enter a location') }}">
                <div id="edit_map"></div>
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
                    <input type="text" class="form-control mb-3 rounded-0" id="edit_longitude" name="longitude" value="{{ $address_data->longitude }}" readonly="">
                </div>
            </div>
            <!-- Latitude -->
            <div class="row">
                <div class="col-md-2">
                    <label>{{ translate('Latitude') }}</label>
                </div>
                <div class="col-md-10">
                    <input type="text" class="form-control mb-3 rounded-0" id="edit_latitude" name="latitude" value="{{ $address_data->latitude }}" readonly="">
                </div>
            </div>
        @endif

        <!-- Phone -->
        <div class="row">
            <div class="col-md-2">
                <label>{{ translate('Phone') }}</label>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control mb-3 rounded-0" placeholder="{{ translate('+880') }}" value="{{ $address_data->phone }}" name="phone" required>
            </div>
        </div>

        <!-- Save button -->
        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary rounded-0 w-150px">{{ translate('Save') }}</button>
        </div>
    </div>
</form>