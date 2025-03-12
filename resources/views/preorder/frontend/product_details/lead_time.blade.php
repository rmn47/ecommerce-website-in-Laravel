<div class="bg-white mb-4 border p-3 p-sm-4">
    <!-- Tabs -->
    <div class="nav aiz-nav-tabs">
        <a href="#tab_default_1" data-toggle="tab"
            class="mr-5 pb-2 fs-16 fw-700 text-reset">{{ translate('Lead Time') }}</a>
    </div>

    <!-- Description -->
    <div class="tab-content pt-0">
            <div class="py-2">
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th scope="col">Quantity (Pieces)</th>
                            @foreach($product->preorder_wholesale_prices as $price)
                            <th scope="col">{{$price->wholesale_min_qty}} - {{$price->wholesale_max_qty}}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Lead Time (days)</th>
                            @foreach($product->preorder_wholesale_prices as $price)
                            <td><strong>{{$price->wholesale_lead_time}}</strong></td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
                <div class="mw-100 overflow-hidden text-left aiz-editor-data d-flex">
                    <p class="opacity-60">
                        * {{translate('Lead time is subjected to change anytime')}}
                    </p>
                </div>
            </div>
    </div>
</div>