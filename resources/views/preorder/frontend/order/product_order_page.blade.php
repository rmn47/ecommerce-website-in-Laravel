@extends('frontend.layouts.app')

@section('meta')


@endsection

@section('content')
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            <div class="row">
                <!-- Product Image Gallery -->
                <div class="col-md-8 mb-4">
                    @include('preorder.frontend.product_details.image_gallery')
                    <!-- Description-->
                    @include('preorder.frontend.product_details.description')
                    <!-- more products-->
                    @include('preorder.frontend.product_details.preorder_products')
                    <!-- lead_time-->
                    @include('preorder.frontend.product_details.lead_time')
                    <!-- customization-->
                    @include('preorder.frontend.product_details.customization')
                    <!-- Sample-->
                    @include('preorder.frontend.product_details.sample')
                    <!-- Reviews & Ratings -->
                    @include('preorder.frontend.product_details.review_section')
                    <!-- Frequently Bought products -->
                    @include('preorder.frontend.product_details.frequently_bought_products')
                    <!-- Seller Info -->
                    @include('preorder.frontend.product_details.seller_info')
                    <!-- Product Query -->
                    @include('preorder.frontend.product_details.product_queries')
                    <!-- Related Products  -->
                    @include('preorder.frontend.product_details.related_products')
                    <!-- Top Selling Products  -->
                    @include('preorder.frontend.product_details.top_selling')
                </div>

                <!-- Product Details -->
                <div class="col-md-4">
                    @include('preorder.frontend.product_details.details')
                    <!-- Price    -->
                    @include('preorder.frontend.product_details.right.price_section')
                    <!-- Review    -->
                    @include('preorder.frontend.product_details.right.review')
                    <!-- Coupon    -->
                    @include('preorder.frontend.product_details.right.coupon')
                    <!-- brand   -->
                    @include('preorder.frontend.product_details.right.brand')
                    <!-- shop   -->
                    @include('preorder.frontend.product_details.right.shop')
                    <!-- Shipping   -->
                    @include('preorder.frontend.product_details.right.shipping')
                    <!-- Refund   -->
                    @if($product->is_refundable)
                    @include('preorder.frontend.product_details.right.refund')
                    @endif
                    <!-- icon-section   -->
                    @include('preorder.frontend.product_details.right.icon_section')
                    <!-- faq   -->
                    @include('preorder.frontend.product_details.right.faq')
                </div>
            </div>
        </div>

    </div>
</section>

<section class="mb-4">
    <div class="container">

        <div class="row gutters-16">
            <!-- Left side -->
            <div class="col-lg-3">
                <div class="d-none d-lg-block">
                </div>
            </div>

            <!-- Right side -->
            <div class="col-lg-9">

                <div class="d-lg-none">
                </div>

            </div>
        </div>
    </div>
</section>



@endsection

@section('modal')
<!-- Image Modal -->
<div class="modal fade" id="image_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-4">
                <div class="size-300px size-lg-450px">
                    <img class="img-fit h-100 lazyload" src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src=""
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chat Modal -->
<div class="modal fade" id="chat_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title fw-600 h5">{{ translate('Any query about this product') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form class="" action="{{ route('conversations.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <div class="modal-body gry-bg px-3 pt-3">
                    <div class="form-group">
                        <input type="text" class="form-control mb-3 rounded-0" name="title"
                            value="{{ $product->product_name }}" placeholder="{{ translate('Product Name') }}" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control rounded-0" rows="8" name="message" required
                            placeholder="{{ translate('Your Question') }}">{{ route('product', $product->product_slug) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary fw-600 rounded-0" data-dismiss="modal">{{
                        translate('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-600 rounded-0 w-100px">{{ translate('Send')
                        }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bid Modal -->
@if($product->auction_product == 1)
@php
$highest_bid = $product->bids->max('amount');
$min_bid_amount = $highest_bid != null ? $highest_bid+1 : $product->starting_bid;
@endphp
<div class="modal fade" id="bid_for_detail_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ translate('Bid For Product') }} <small>({{
                        translate('Min Bid Amount: ').$min_bid_amount }})</small> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" action="{{ route('auction_product_bids.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="form-group">
                        <label class="form-label">
                            {{translate('Place Bid Price')}}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-group">
                            <input type="number" step="0.01" class="form-control form-control-sm" name="amount"
                                min="{{ $min_bid_amount }}" placeholder="{{ translate('Enter Amount') }}" required>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-sm btn-primary transition-3d-hover mr-1">{{
                            translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Product Review Modal -->
<div class="modal fade" id="product-review-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="product-review-modal-content">

        </div>
    </div>
</div>

<!-- Size chart show Modal -->
@include('modals.size_chart_show_modal')
@endsection

@section('script')
<script type="text/javascript">
    window.onload = function() {
        window.scrollTo(0, 0); // Scrolls to the top of the page
    };
</script>
<script type="text/javascript">


</script>
@endsection