<script type="text/javascript">
    function submitShippingInfoForm(el) {
        var email = $("input[name='email']").val();
        var phone = $("input[name='country_code']").val() + $("input[name='phone']").val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('guest_customer_info_check') }}",
            type: 'POST',
            data: {
                email: email,
                phone: phone
            },
            success: function(response) {
                if (response == 1) {
                    $('#login_modal').modal();
                    AIZ.plugins.notify('warning', '{{ translate('You already have an account with this information. Please Login first.') }}');
                } else {
                    $('#shipping_info_form').submit();
                }
            }
        });
    }

    function add_new_address() {
        $('#new-address-modal').modal('show');
    }

    function edit_address(address) {
        var url = '{{ route("addresses.edit", ":id") }}';
        url = url.replace(':id', address);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            success: function(response) {
                $('#edit_modal_body').html(response.html);
                $('#edit-address-modal').modal('show');
                AIZ.plugins.bootstrapSelect('refresh');

                @if (get_setting('google_map') == 1)
                    var lat = -33.8688;
                    var long = 151.2195;

                    if (response.data.address_data.latitude && response.data.address_data.longitude) {
                        lat = parseFloat(response.data.address_data.latitude);
                        long = parseFloat(response.data.address_data.longitude);
                    }

                    initialize(lat, long, 'edit_');
                @endif
            },
            error: function(xhr, status, error) {
                console.log('Error loading edit modal:', error);
                AIZ.plugins.notify('danger', '{{ translate('Failed to load address data. Please try again.') }}');
            }
        });
    }

    // Event listeners for cascading dropdowns
    $(document).on('change', '[name=country_id]', function() {
        var country_id = $(this).val();
        var targetStateId = $(this).attr('id') === 'new_country' ? '#new_state' : '#edit_state';
        get_states(country_id, targetStateId);
    });

    $(document).on('change', '[name=state_id]', function() {
        var state_id = $(this).val();
        var targetCityId = $(this).attr('id') === 'new_state' ? '#new_city' : '#edit_city';
        var targetPincodeId = $(this).attr('id') === 'new_state' ? '#new_pincode' : '#edit_pincode';
        get_city(state_id, targetCityId, targetPincodeId);
    });

    $(document).on('change', '[name=city_id]', function() {
        var city_id = $(this).val();
        var targetPincodeId = $(this).attr('id') === 'new_city' ? '#new_pincode' : '#edit_pincode';
        get_pincodes(city_id, targetPincodeId);
    });

    // Fetch states for a country
    function get_states(country_id, targetStateId) {
        $(targetStateId).html('<option value="">{{ translate("Select State") }}</option>');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get-state') }}",
            type: 'POST',
            data: {
                country_id: country_id
            },
            success: function(response) {
                $(targetStateId).html(response);
                AIZ.plugins.bootstrapSelect('refresh');
                var targetCityId = targetStateId.replace('state', 'city');
                var targetPincodeId = targetStateId.replace('state', 'pincode');
                get_city($(targetStateId).val(), targetCityId, targetPincodeId);
            }
        });
    }

    // Fetch cities for a state
    function get_city(state_id, targetCityId, targetPincodeId) {
        $(targetCityId).html('<option value="">{{ translate("Select City") }}</option>');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get-city') }}",
            type: 'POST',
            data: {
                state_id: state_id
            },
            success: function(response) {
                $(targetCityId).html(response);
                AIZ.plugins.bootstrapSelect('refresh');
                get_pincodes($(targetCityId).val(), targetPincodeId);
            }
        });
    }

    // Fetch pincodes for a city
    function get_pincodes(city_id, targetPincodeId) {
        $(targetPincodeId).html('<option value="">{{ translate("Select Pincode") }}</option>');
        if (city_id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('get-pincodes') }}",
                type: 'POST',
                data: {
                    city_id: city_id
                },
                success: function(response) {
                    $(targetPincodeId).html(response);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }
    }

    $(document).ready(function() {
        AIZ.plugins.bootstrapSelect('refresh');
    });
</script>