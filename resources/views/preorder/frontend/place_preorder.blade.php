<div class="p-3">
    <div class="button-section btn preorder-request-btn d-flex row rounded-2 mt-2 py-2" >
        <a href="javascript:void(0)" class="btn btn-block rounded-pill fs-14 fw-700  m-0 py-2 text-white" @if (Auth::check()) onclick="showPlacePreorderModal()" @else onclick="showLoginModal()" @endif > {{ translate('Create Preorder') }}</a>
    </div>
</div>



<!-- Place Preorder Request -->
<div class="modal fade" id="placePreorder">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="preorder-modal-size" role="document">
        <div class="modal-content position-relative">

            <button type="button" class="close absolute-top-right btn-icon close z-1 btn-circle bg-gray mr-2 mt-2 d-flex justify-content-center align-items-center" data-dismiss="modal" aria-label="Close" style="background: #ededf2; width: calc(2rem + 2px); height: calc(2rem + 2px);">
                <span aria-hidden="true" class="fs-24 fw-700" style="margin-left: 2px;">&times;</span>
            </button>
            <div class="p-4">
                <div class="card rounded-0 border shadow-none mt-4" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingPreorderRequest" type="button" data-toggle="collapse" data-target="#collapsePreorderRequest" aria-expanded="true" aria-controls="collapsePreorderRequest">
                        <div class="d-flex align-items-center">
                         
                            <span class="ml-2 fs-19 fw-700">{{ translate('Request Preorder') }}</span>
                        </div>
                    </div>
                        <div class="card-body" id="preorder_request">
                            <form action="{{route('preorder.place_order')}}" method="POST">
                                @csrf

                                <input type="hidden" name="request_preorder" value="1">
                                <input type="hidden" name="preorder_product_id" value="{{$product->id}}">
                                <div>

                          
                                
                                    <p><i class="las la-arrow-right"></i> 
                                        <span class="ml-2">{{translate('Cash ondelivery available')}}</span>
                                    </p>
                                    @if($product->user->user_type == 'admin')
                                        <p> {!! get_setting('preorder_request_instruction') !!}</p>
                                    @else
                                        <p> {!! $product->user->shop->preorder_request_instruction !!} </p>
                                    @endif
                                </div>
                                <div>
                                        <span class="fw-700 mb-4">{{translate('Note')}}</span><span class="ml-2">{{translate(('(Upto 200 character)'))}}</span>
                                    <div class="mt-4">
                                        <textarea name="request_note" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>

                                <!-- Agree Box -->
                                <div class="pt-2rem fs-14 mb-4">
                                        <label class="aiz-checkbox">
                                            <input type="checkbox" required id="agree_checkbox" onchange="stepCompletionPaymentInfo()"> 
                                            <span class="aiz-square-check"></span>
                                            <span>{{ translate('I agree to the') }}</span>
                                        </label>
                                        <a href="{{ route('terms') }}" class="fw-700">{{ translate('terms and conditions') }}</a>,
                                        <a href="{{ route('returnpolicy') }}" class="fw-700">{{ translate('return policy') }}</a> &
                                        <a href="{{ route('privacypolicy') }}" class="fw-700">{{ translate('privacy policy') }}</a>
                                </div>

                                <div>
                                    <div class="col-12 m-0 p-0">
                                        <button type="submit" class="btn btn-block btn-soft-primary " >{{  translate('Place Preorder Request')  }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

function showPlacePreorderModal(){
            if(!$('#preorder-modal-size').hasClass('modal-lg')){
                $('#preorder-modal-size').addClass('modal-lg');
            }
            $('#placePreorder').modal('show');
            // $('.c-preloader').show();
        }
</script>