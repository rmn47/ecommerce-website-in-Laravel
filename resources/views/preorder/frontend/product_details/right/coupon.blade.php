<div class=" mb-4 p-2 rounded-2 preorder-border-dashed-grey" >
    <div class="section-wrapper mx-3">
        <div class="row d-flex justify-content-between mx-1 mt-2">
            <div>
                <p class="text-uppercase fw-bold fs-16"><b>{{translate('coupon')}}</b></p>
            </div>
            @if($product->user->shop)
            <div>
                <p class="preorder-text-secondary"><a href="{{$product->user->shop?->slug != null ? route('shop.visit', $product->user->shop?->slug) : "#"}}" class="preorder-text-secondary"><u>{{translate('Seller Coupons')}}</u></a></p>
            </div>
            @endif
        </div>

        <div class="button-section row mb-2">

            @if(date('Y-m-d H:i:s', time()) < date('Y-m-d H:i:s', $product->preorder_coupon?->coupon_end_date))
            <div class="col-12">
                <span 
                    class="btn btn-block fs-12 fw-700 rounded-0 m-0 py-1 d-flex justify-content-between rounded-1" 
                    style="background-color: #FF6002; color: white;"
                >
                    <!-- Coupon Code -->
                    <span id="coupon-code">{{$product->preorder_coupon?->coupon_code}}</span>
            
                    <!-- Copy Icon -->
                    <span 
                        id="copy-btn" 
                        style="cursor: pointer;" 
                        onclick="copyCouponCode()"
                    >
                        <i class="las la-copy"></i>
                    </span>
                </span>
            </div>
            @else
            <div class="col-12">
                <span 
                    class="btn btn-block fs-12 fw-400 rounded-0 m-0 py-1 d-flex justify-content-between rounded-1" 
                    style="background-color: #818181; color: white;"
                >
                    <!-- Coupon Code -->
                    <span id="coupon-code">{{translate('No Coupon Available')}}</span>
            
                    
                </span>
            </div>
            @endif
        </div>

    </div>
</div>




<div class="mt-4">
    @if(date('Y-m-d H:i:s', time()) < date('Y-m-d H:i:s', $product->preorder_coupon?->coupon_end_date))
    <p class=" p-0 "><i class="las la-check fs-10 rounded-3 p-1" style="background-color: #FF6002; color: white;"></i>
        <span class="ml-2">{{$product->preorder_coupon?->coupon_type == 'flat' ? single_price($product->preorder_coupon?->coupon_amount) : $product->preorder_coupon?->coupon_amount.'% '}} <span class="ml-1">{{translate('Coupon discount on Preorder')}}</span> </span>
    </p>
    @endif

    @if($product->preorder_prepayment?->prepayment_amount != null)
    <p class=" p-0 "><i class="las la-check fs-10 rounded-3 p-1 " style="background-color: #FF6002; color: white;"></i>
        <span class="ml-2">
            {{translate('Pay only')}} {{single_price($product->preorder_prepayment?->prepayment_amount)}} {{translate('to ensure your order')}}
        </span>
    </p>
    @endif
    @if($product->is_cod)
        <p class=" p-0 "><i class="las la-check fs-10 rounded-3 p-1 " style="background-color: #FF6002; color: white;"></i>
            <span class="ml-2">{{translate('Cash on delivery available')}}</span>
        </p>
    @endif
</div>

@section('script')

<script>
    function copyCouponCode() {
        const couponCode = document.getElementById('coupon-code').textContent;
        navigator.clipboard.writeText(couponCode)
            .then(() => {
                AIZ.plugins.notify('success', "{{ translate('Coupon code copied to clipboard!') }}");
            })
            .catch(err => {
                AIZ.plugins.notify('error', "{{ translate('Failed to copy') }}");
            });
    }
</script>
@endsection
