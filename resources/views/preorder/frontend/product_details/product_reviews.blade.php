<div class="bg-white border mb-4 rounded-2" id="review_ratings">
    <div class="p-3 p-sm-4">
        <h3 class="fs-16 fw-700 mb-0">
            <span class="mr-4 text-uppercase">{{ translate('Reviews & Ratings') }}</span>
        </h3>
    </div>
    <!-- Ratting -->
    <div class="px-3 px-sm-4 mb-4">
        <div class="border border-secondary-base  p-3 p-sm-4 rounded-2">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3">
                    <div class=" align-items-center  justify-content-md-start">

                        <div class="w-100 ">
                            <span class="fs-48 fw-700 mr-3 d-block mb-0">{{ $product->rating }}</span>

                            <span class="fs-14 mr-3 d-block mt-0">{{ translate('out of 5.0') }}</span>
                        </div>

                        <div class="mt-sm-1 w-100 w-sm-auto d-flex flex-wrap justify-content-end justify-content-md-start">
                            @php
                                $total = 0;
                                $total += $product->preorderProductreviews->count();
                            @endphp
                            <span class="rating rating-mr-2">
                                {{ renderStarRating($product->rating) }}
                            </span>
                            <span class="ml-1 fs-14">({{ $total }}
                                {{ translate('reviews') }})</span>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 text-left">

                    <div>
                        <span class="d-block fs-14 fw-700">{{translate('Review this product')}}</span>
                        <span class="d-block fs-14">{{translate('Share your experience with others')}}</span>
                    </div>

                    <a  href="javascript:void(0);" onclick="product_review('{{ $product->id }}')"
                        class="btn px-4 border-yellow hov-bg-yellow p1-3 rounded-4 mt-3">
                        <span class="d-md-inline-block "> {{ translate('Rate this Product') }}</span>
                    </a>
     
                </div>
            </div>
        </div>
    </div>
    <!-- Reviews -->
    @include('preorder.frontend.product_details.product_review_pagination')
</div>



<script type="text/javascript">
        // Preorder Product Review
        function product_review(product_id) {
            @if (isCustomer())
                @if ($review_status == 1)
                    $.post('{{ route('preorder.product_review_modal') }}', {
                        _token: '{{ @csrf_token() }}',
                        product_id: product_id
                    }, function(data) {
                        $('#product-review-modal-content').html(data);
                        $('#product-review-modal').modal('show', {
                            backdrop: 'static'
                        });
                        AIZ.extra.inputRating();
                    });
                @else
                    AIZ.plugins.notify('warning', '{{ translate("Sorry, You need to buy this product to give review.") }}');
                @endif
            @elseif (Auth::check() && !isCustomer())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }
</script>
