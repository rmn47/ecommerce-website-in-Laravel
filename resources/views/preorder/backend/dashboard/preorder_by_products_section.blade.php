<!-- seller products -->
<div class="position-relative">
    <div class="top_sellers_product_table  table-responsive c-scrollbar-light show" style="width: 100%;">
        <table class="table dashboard-table mb-0">
            <thead>
                <tr class="fs-11 fw-600 text-secondary">
                    <th class="pl-0 border-top-0 border-bottom-1">{{ translate('Product') }}</th>
                    <th class="border-top-0 border-bottom-1">{{ translate('Request') }}</th>
                    <th class="border-top-0 border-bottom-1">{{ translate('Prepayment') }}</th>
                    <th class="border-top-0 border-bottom-1">{{ translate('Final Order') }}</th>
                    <th class="border-top-0 border-bottom-1">{{ translate('Total Sold') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preorderProducts as $row)
                    @php
                        $product = \App\Models\PreorderProduct::where('id', $row['id'])->first();
                        $product_url = route('preorder-product.details', $product->product_slug);
                    @endphp
                    <tr>
                        <td class="pl-0" style="vertical-align: middle">
                            <div class="d-flex align-items-center">
                                <div class="rounded-2 overflow-hidden"
                                    style="min-height: 48px !important; min-width: 48px !important;max-height: 48px !important; max-width: 48px !important;">
                                    <a href="{{ $product_url }}" class="d-block" target="_blank">
                                        <img src="{{ uploaded_asset($product->thumbnail) }}" alt="{{ translate('category')}}" 
                                            class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                    </a>
                                </div>
                                <a href="{{ $product_url }}" target="_blank" class="d-block text-soft-dark fw-400 hov-text-primary ml-2 fs-13" title="{{ $product->getTranslation('product_name') }}">
                                    {{ Str::limit($product->getTranslation('product_name'), 50, ' ...') }}
                                </a>
                            </div>
                        </td>
                        <td style="vertical-align: middle" class="text-soft-dark fw-700">
                            {{ $product->preorder->where('request_preorder_status',1)->count() }}
                        </td>
                        <td style="vertical-align: middle" class="text-soft-dark fw-700">
                            {{ $product->preorder->where('prepayment_confirm_status',1)->count() }}
                        </td>
                        <td style="vertical-align: middle" class="text-soft-dark fw-700">
                            {{ $product->preorder->where('final_order_status',1)->count() }}
                        </td>
                        <td style="vertical-align: middle" class="text-soft-dark fw-700">
                            {{ $product->preorder->where('delivery_status',2)->where('refund_status','!=' ,2)->count() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>