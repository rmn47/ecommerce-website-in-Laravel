<div class="bg-white mb-4 border p-3 p-sm-4">
    <!-- Tabs -->
    <div class="nav aiz-nav-tabs">
        <a href="#tab_default_1" data-toggle="tab" class="mr-5 pb-2 fs-16 fw-700 text-reset">{{
            translate('Customization') }}</a>
    </div>

    <!-- Description -->
    <div class="tab-content pt-0">
        <!-- Description -->

        <div class="py-2">
            <div class="mw-100 overflow-hidden text-left aiz-editor-data d-flex">
                <div class="mr-4">
                    <p class="text-primary"> {{translate('Customized packaging available')}}</p>
                    <p>Min Order: {{$product->min_qty}}</p>
                </div>

            </div>
            <div class="mw-100 overflow-hidden text-left aiz-editor-data d-flex">
                <p>
                    {{translate('For more customization details')}} <a href="#"><u>{{translate('contact
                            supplier')}}</u></a>
                </p>
            </div>
        </div>
    </div>
</div>