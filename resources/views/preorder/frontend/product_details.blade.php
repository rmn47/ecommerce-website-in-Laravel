@extends('frontend.layouts.app')

@section('meta')

@endsection

@section('content')
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            <div class="row">
                <!-- Product Image Gallery -->
                <div class="col-md-8">
                    @include('preorder.frontend.product_details.image_gallery')

                </div>

                <!-- Product Details -->
                <div class="col-md-4">
                    @include('preorder.frontend.product_details.details')
                    <!-- Price    -->
                    @include('preorder.frontend.product_details.right.price_section')
                    {{-- preorder request --}}
                    @include('preorder.frontend.place_preorder')
                    <!-- Review    -->
                    @include('preorder.frontend.product_details.right.review')
                </div>
            </div>
            <div class="row">
                <!-- Product Image Gallery -->
                <div class="col-md-8 mb-4">
                    <!-- Description-->
                    @include('preorder.frontend.product_details.description')
                    <!-- more products-->
                    @if($more_products !== null && count($more_products) > 0)
                    @include('preorder.frontend.product_details.preorder_products')
                    @endif
                    <!-- Reviews & Ratings -->
                    <div id="pre_review_ratings">

                    </div>
                    @include('preorder.frontend.product_details.product_reviews')
                    <!-- Frequently Bought products -->
                    @if($fq_bought_products !== null && count($fq_bought_products) > 0)
                    @include('preorder.frontend.product_details.frequently_bought_products')
                    @endif
                    <!-- Seller Info -->
                    @include('preorder.frontend.product_details.seller_info')
                    <!-- Product Query -->
                    <div id="pre_product_queries">

                    </div>
                    @include('preorder.frontend.product_details.product_queries')
                    <!-- Related Products  -->
                    @if( count(\App\Models\Product::inRandomOrder()->get()) > 0)
                    @include('preorder.frontend.product_details.related_products')
                    @endif
                    <!-- Top Selling Products  -->
                    @if( count(\App\Models\Product::inRandomOrder()->get()) > 0)
                    @include('preorder.frontend.product_details.top_selling')
                    @endif
                </div>

                <!-- Product Details -->
                <div class="col-md-4">
                    <!-- Coupon    -->
                    @if($product->is_coupon)
                    @include('preorder.frontend.product_details.right.coupon')
                    @endif
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

<!-- Conversation Modal -->
<div class="modal fade" id="product-conversation-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size" role="document">
        <div class="modal-content" id="product-conversation-modal-content">
            
        </div>
    </div>
</div>

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
    $(document).ready(function() {
            getVariantPrice();
        });

        function CopyToClipboard(e) {
            var url = $(e).data('url');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(url).select();
            try {
                document.execCommand("copy");
                AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
            } catch (err) {
                AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
            }
            $temp.remove();
        }


        // Pagination using ajax
        $(window).on('hashchange', function() {
            if(window.history.pushState) {
                window.history.pushState('', '/', window.location.pathname);
            } else {
                window.location.hash = '';
            }
        });

        $(document).ready(function() {
            $(document).on('click', '.product-queries-pagination .pagination a', function(e) {
                getPaginateData($(this).attr('href').split('page=')[1], 'query', 'queries-area');
                e.preventDefault();
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.product-reviews-pagination .pagination a', function(e) {
                getPaginateData($(this).attr('href').split('page=')[1], 'review', 'reviews-area');
                e.preventDefault();
            });
        });

        function getPaginateData(page, type, section) {
            $.ajax({
                url: '?page=' + page,
                dataType: 'json',
                data: {type: type},
            }).done(function(data) {
                $('.'+section).html(data);
                location.hash = page;
            }).fail(function() {
                alert('Something went worng! Data could not be loaded.');
            });
        }
        // Pagination end

        function showImage(photo) {
            $('#image_modal img').attr('src', photo);
            $('#image_modal img').attr('data-src', photo);
            $('#image_modal').modal('show');
        }

        function bid_modal(){
            @if (isCustomer() || isSeller())
                $('#bid_for_detail_product').modal('show');
          	@elseif (isAdmin())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers & Sellers can Bid.") }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }


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

        // function showPlacePreorderModal(){
        // alert('ok');
        //     if(!$('#modal-size').hasClass('modal-lg')){
        //         $('#modal-size').addClass('modal-lg');
        //     }
        //     $('#placePreorder').modal();
        //     $('.c-preloader').show();
        // }


        function show_conversation_modal(product_id) {
            @if(isCustomer())
                $.post('{{ route('preorder.conversation_modal') }}', {
                    _token: '{{ @csrf_token() }}',
                    product_id: product_id
                }, function(data) {
                    $('#product-conversation-modal-content').html(data);
                    $('#product-conversation-modal').modal('show', {
                        backdrop: 'static'
                    });
                });
            @elseif (Auth::check() && !isCustomer())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }

</script>
@endsection