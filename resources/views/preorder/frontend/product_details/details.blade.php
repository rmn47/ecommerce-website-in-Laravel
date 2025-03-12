<div class="text-left">
    <!-- Product Name -->
    <p class="mb-4 fs-16 fw-700 text-dark break-word"> {{ $product->product_name }}</p>

    <div class="row align-items-center">
        <div class="col mb-3">
            <div class="d-flex justify-content-between">
                <!-- left -->
                <div class="mr-3 fs-14 text-dark  has-transitiuon hov-opacity-100">
                    <p class="m-0 p-0">
                        <span class="opacity-60"> {{ translate('Category') }} </span>
                        <span class="ml-1 opacity-100">{{$product->category->name}}</span>
                    </p>
                    <p class="m-0 p-0">
                        <span class="opacity-60"> {{ translate('Preorder Received') }} </span>
                        <span class="ml-1 opacity-100"><b>{{$product->preorder->count()}}</b></span>
                    </p>
                </div>

                <!-- right -->
                @if(get_setting('product_query_activation') == 1)
                <div class="fs-14 text-dark  has-transitiuon hov-opacity-100">
                    <a href="#pre_product_queries">
                        <u class="preorder-text-secondary">{{ translate('Ask About This Product') }} </u>
                    </a>
                </div>
                @endif
                
            </div>
        </div>
    </div>
    <div class="row align-items-center">
        <div class="col-md-12 m-0 p-0">
            <div class="ml-2">
                <span class="badge badge-inline badge-cool-blue fs-12 fw-700 p-3 text-white m-1 rounded-2"
                    >{{$product->is_available ? translate( 'Available Now ')  : (strtotime($product->available_date) <= strtotime(date('Y-m-d')) ? translate( 'Available Now ') : translate('Available on ') .' '. $product->available_date .' '. (translate(' estimated')))}}</span>
                
                    @if($product->discount != null && $product->discount > 0 &&  $product->discount_start_date != null  && (strtotime(date('d-m-Y')) > $product->discount_start_date || strtotime(date('d-m-Y')) < $product->discount_end_date))
                    <span class="badge badge-inline badge-orange fs-12 fw-700 p-3 text-white m-1 rounded-2"
                    > {{ translate('Discount ')}} {{ $product->discount_type == 'flat' ? single_price($product->discount) : $product->discount.'%'}}</span>
                    @endif

                @if($product->is_prepayment)
                <span class="badge badge-inline badge-sea-green fs-12 fw-700 p-3 text-white m-1 rounded-2"
                    >{{ translate('Prepayment Needed') }}</span>
                @endif
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
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
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can ask questions.") }}');
            @else
                $('#login_modal').modal('show');
            @endif
        }
</script>