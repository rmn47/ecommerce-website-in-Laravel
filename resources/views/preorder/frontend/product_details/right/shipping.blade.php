<div class="border mb-4 p-2 mt-4 rounded-2 ">

    <div class="px-3">

        <div class="div shipping-section">
            <div class="top d-flex justify-content-between mt-2">
                <div class="fw-16">
                    <p class="text-uppercase fs-16"><b>{{translate('Shipping')}}</b></p>
                </div>
                <div>
                    <i class="las la-info-circle fs-16 opacity-60" ></i>
                </div>
            </div>

            @if($product->preorder_shipping?->shipping_type == 'free')
            <div class="free-shipping bg-preorder-shipping-section p-2 rounded-2">
                <p class="m-0 p-0 preorder-text-green ml-1"><i class="las la-check fs-10 rounded-3 p-1" style="background-color: #32A687; color: white;"></i> <span class="ml-2">{{translate('Free Shipping')}}</span> </p>
            </div>
            @endif

            <div class="mt-3">
                @if($product->preorder_shipping?->show_shipping_time)
                <p>{{translate('Estimated Shipping Time :')}} <b> {{ $product->preorder_shipping->min_shipping_days }} -  {{ $product->preorder_shipping->max_shipping_days }}  {{translate('days')}}</b></p>
                @endif

                @if($product->preorder_shipping?->note?->description != null && $product->preorder_shipping->show_shipping_note)
                    <p class="preorder-text-light-grey fs-14">
                        <span id="short-text-{{ $product->preorder_shipping?->note?->id }}">
                            {{ Str::limit($product->preorder_shipping?->note?->description, 100) }} <!-- Show first 100 characters -->
                        </span>
                        <span class="d-none" id="full-text-{{ $product->preorder_shipping?->note?->id }}">
                            {{ $product->preorder_shipping?->note?->description }}
                        </span>
                        @if (strlen($product->preorder_cod?->note?->description) > 100)
                        <a href="javascript:void(0);" onclick="toggleText({{ $product->preorder_shipping?->note?->id }})" id="toggle-link-{{ $product->preorder_shipping?->note?->id }}">
                            {{ translate('See More') }}
                        </a>
                        @endif
                    </p>
                @endif
                @if(get_setting('product_query_activation') == 1)
                <p class="preorder-text-light-grey"><a href="#product_query" class="preorder-text-secondary">{{translate('Contact Us')}}</a> {{translate('for shipping time for larger orders.')}}</p>
                @endif
            </div>
        </div>


        <div class="cod mt-4">
            @if($product->is_cod)
            <div class="top d-flex justify-content-between mt-2">
                <div class="fw-16">
                    <p class="text-uppercase fs-16"><b>{{translate('Cash on Delivery')}}</b></p>
                </div>
                <div>
                    <i class="las la-info-circle fs-16 opacity-60" ></i>
                </div>
            </div>
            <div class="free-shipping bg-soft-warning p-2 rounded ">
                <p class="m-0 p-0 preorder-text-yellow ml-1"><i class="las la-check fs-10 rounded-3 p-1" style="background-color: #FFB503; color: white;"></i> <span class="ml-2">{{translate('Cash on Delivery
                    Available')}}</span> </p>
            </div>
            <div class="mt-1">
                @if($product->is_prepayment)
                <p class="preorder-text-light-grey mt-2 fs-14">{{translate('Prepayment needed for cash on delivery')}}</p>
                <p class="preorder-text-light-grey fs-14">{{translate('Pay only')}} {{format_price($product->preorder_prepayment?->prepayment_amount)}} {{translate('to avail Cash on Delivery')}}</p>
                @endif

                @if($product->preorder_cod?->note?->description != null && $product->preorder_cod?->show_cod_note)
                    <p class="preorder-text-light-grey fs-14">
                        <span id="short-text-{{ $product->preorder_cod?->note?->id }}">
                            {{ Str::limit($product->preorder_cod?->note?->description, 100) }} <!-- Show first 100 characters -->
                        </span>
                        <span class="d-none" id="full-text-{{ $product->preorder_cod?->note?->id }}">
                            {{ $product->preorder_cod?->note?->description }}
                        </span>
                        @if (strlen($product->preorder_cod?->note?->description) > 100)
                        <a href="javascript:void(0);" onclick="toggleText({{ $product->preorder_cod?->note?->id }})" id="toggle-link-{{ $product->preorder_cod?->note?->id }}">
                            {{ translate('See More') }}
                        </a>
                        @endif

                    </p>
                @endif

            </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleText(id) {
    const shortText = document.getElementById(`short-text-${id}`);
    const fullText = document.getElementById(`full-text-${id}`);
    const toggleLink = document.getElementById(`toggle-link-${id}`);

    if (fullText.classList.contains('d-none')) {
        shortText.classList.add('d-none'); 
        fullText.classList.remove('d-none'); 
        toggleLink.textContent = 'See Less'; 
    } else {
        shortText.classList.remove('d-none'); 
        fullText.classList.add('d-none'); 
        toggleLink.textContent = 'See More'; 
    }
}
    
</script>
