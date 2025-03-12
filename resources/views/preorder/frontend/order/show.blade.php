@extends('frontend.layouts.app')

@section('meta')

@endsection

@section('content')
@php 
    $seller = $order->preorder_product->user->shop; 
    $instruction_qr_code = $order->preorder_product->user->user_type == 'admin' ? get_setting('image_for_payment_qrcode') : $seller?->image_for_payment_qrcode;
@endphp 
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            <div class="row">

                <div class="col-md-8 mb-4">
                    <!-- Product Info -->
                    <div class="d-flex">
                        <div class="my-2">
                            <a href="{{ $order->preorder_product != null ? (route('preorder-product.details',$order->preorder_product?->product_slug)) : '#' }}">
                                <img src="{{uploaded_asset($order->preorder_product?->thumbnail)}}"
                                alt="product image" class="h-100px border rounded-2">
                            </a>
                        </div>
                        <div class="my-2 ml-4">
                            <a href="{{ $order->preorder_product != null ? (route('preorder-product.details',$order->preorder_product?->product_slug)) : '#' }}" 
                                class="text-black" style="color: initial;"><p class="fw-700 mt-2 break-word">{{$order->preorder_product?->product_name}}</p></a>
                            <div class="row mt-2">
                                <div class="col-auto m-0">
                                    <span class="badge badge-inline badge-info fs-12 fw-700 p-3 text-white m-1 rounded-2"
                                        >{{translate('Order Code : '. $order->order_code)}}</span>
                                        <span class="badge badge-inline badge-cool-blue fs-12 fw-700 p-3 text-white m-1 rounded-2"
                                        >{{$order->preorder_product?->is_available ? translate( 'Available Now ')  : (strtotime($order->preorder_product?->available_date) <= strtotime(date('Y-m-d')) ? translate( 'Available Now ') : translate('Available on ') .' '. $order->preorder_product?->available_date .' '. (translate(' estimated')))}}</span>
                                    
                                        @if($order->preorder_product?->discount != null && $order->preorder_product?->discount > 0 && $order->preorder_product?->discount_start_date != null && (strtotime(date('d-m-Y')) > $order->preorder_product->discount_start_date || strtotime(date('d-m-Y')) < $order->preorder_product->discount_end_date))
                                        <span class="badge badge-inline badge-orange fs-12 fw-700 p-3 text-white m-1 rounded-2"
                                        >{{ translate('Discount ')}} {{ $order->preorder_product?->discount_type == 'flat' ? single_price($order->preorder_product?->discount) : $order->preorder_product?->discount.'%'}}</span>
                                        @endif

                                    @if($order->preorder_product?->is_prepayment)
                                    <span class="badge badge-inline badge-sea-green fs-12 fw-700 p-3 text-white m-1 rounded-2"
                                        >{{ translate('Prepayment Needed') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--  accordion --}}
                    <div class="accordion mt-4" id="accordioncCheckoutInfo">
                        <!-- Request Preorder -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingPreorderRequest" type="button" data-toggle="collapse" data-target="#collapsePreorderRequest" aria-expanded="true" aria-controls="collapsePreorderRequest">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->request_preorder_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Request Preorder') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapsePreorderRequest" class="collapse show" aria-labelledby="headingPreorderRequest" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <form action="{{route('preorder.order_update',$order->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="request_preorder" value="1">
                                    <div>
                                        @if($order->preorder_product?->is_cod)
                                        <p><i class="las la-arrow-right"></i> 
                                            <span class="ml-2">{{translate('Cash ondelivery available')}}</span>
                                        </p>
                                        @endif
                                        @if($order->preorder_product->user->user_type == 'admin')
                                        <p> {!! get_setting('preorder_request_instruction') !!}</p>
                                        @else
                                        <p> {!! $seller?->preorder_request_instruction !!} </p>
                                        @endif

                                    </div>
                                    @if($order->request_note != null)
                                    <div>
                                        <div class="mt-4">
                                            <p class="border p-2" style="font-style: italic;"> {{ $order->request_note }}</p>
                                        </div>
                                    </div>
                                    @endif


                                    <div>
                                        @if($order->request_preorder_status == 2)
                                        <div class="col-12 m-0 p-0">
                                            <button type="submit" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                        </div>
                                        @elseif($order->request_preorder_status == 3)
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-danger"  disabled >{{  translate('Rejected') }}</button>
                                            </div>
                                        @elseif($order->request_preorder_status == 1)
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-secondary " disabled >{{  translate('Requested') }}</button>
                                            </div>
                                        @else
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-soft-primary " >{{  translate('Request')  }}</button>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                        <!-- Pre-payment & Confirmation -->
                        @if($order->preorder_product?->is_prepayment)
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingPrepaymentConfirmation" type="button" data-toggle="collapse" data-target="#collapsePrepaymentConfirmation" aria-expanded="true" aria-controls="collapsePrepaymentConfirmation">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->prepayment_confirm_status,  $order->request_preorder_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Pre-payment & Confirmation') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapsePrepaymentConfirmation" class="collapse " aria-labelledby="headingPrepaymentConfirmation" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <form  action="{{route('preorder.order_update',$order->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="prepayment_confirmation" value="1">

                                        <div class="d-flex justify-content-between">
                                            <div class="mr-4">
                                                @if($order->preorder_product->user->user_type == 'admin')
                                                <p> {!! get_setting('pre_payment_instruction') !!}</p>
                                                @else
                                                <p> {!! $seller?->pre_payment_instruction !!} </p>
                                                @endif
                                            </div>
                                            @if($instruction_qr_code)
                                            <div class="mb-3">
                                                <img class="w-120px h-120px" src="{{ uploaded_asset($instruction_qr_code) }}" alt="">
                                            </div>
                                            @endif
                                        </div>

                                        <div class="form-section preorder-border-dashed-grey px-4 py-2 rounded-2">

                                            @if($order->prepayment_confirm_status == 0)
                                            <div class="row" id="prepayment_fields">
                                                <div class="col-6">
                                                    <div class="form-group ">
                                                        <label class="col-form-label" for="signinSrEmail">{{translate('Proof
                                                            of payment')}}</label>
                                                        <div class="input-group" data-toggle="aizuploader"
                                                            data-type="image" >
                                                            @if($order->prepayment_confirm_status == 0)  
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium"> {{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose
                                                                File') }}</div>
                                                            @endif
                                                            <input type="hidden" name="payment_proof" 
                                                                class="selected-files" value="{{$order->payment_proof}}" >
                                                        </div>
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group ">
                                                        <label class="col-form-label"
                                                            for="signinSrEmail">{{translate('Reference No.')}}</label>
                                                        <input type="text"
                                                            class="form-control @error('reference_no') is-invalid @enderror"
                                                            name="reference_no" 
                                                            placeholder="{{ translate('Ref No') }}"
                                                            onchange="update_sku()"  value="{{$order->reference_no}}" @if($order->prepayment_confirm_status !== 0) readonly @endif>

                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                           
                                            @if($order->prepayment_confirm_status !== 0) 
                                            <div class="row px-2 py-4">
                                                <div class="col d-flex justify-content-between">
                                                    <div>
                                                        <p><b>{{translate('Proof of payment')}}</b></p>
                                                        <p><b>{{translate('Reference No.')}}</b></p>
                                                    </div>
                                                    <div class="ml-4">
                                                        <p> <img class="w-50px h-50px" src="{{ uploaded_asset($order->payment_proof) }}" alt=""> </p>
                                                        <p>{{$order->reference_no}}</p>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            @endif

                                            <div class="row">    
                                                <div class="col-12">
                                                    @if($order->prepayment_confirm_status == 0)
                                                        <div class="form-group ">
                                                            <label class="col-form-label"
                                                                for="signinSrEmail">{{translate('Note')}}</label>

                                                            <textarea name="confirm_note" rows="4"
                                                                class="form-control" ></textarea>
                                                        </div>
                                                    @endif
                                                    @if($order->confirm_note != null)
                                                        <div>
                                                            <div class="mt-4">
                                                                <p class="border p-2" style="font-style: italic;"> {{ $order->confirm_note }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                            </div>
                                        </div>
                                        @if($order->prepayment_note)
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="col-form-label fw-700"
                                                            for="signinSrEmail">{{translate('Note From Seller')}}</label>
                                                    <p>{{ $order->prepayment_note }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Agree Box -->
                                        @if($order->prepayment_confirm_status == 0 && $order->request_preorder_status == 2)
                                        <div class="pt-2rem fs-14 mb-3 ml-4">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" required id="agree_checkbox" onchange="stepCompletionPaymentInfo()">
                                                <span class="aiz-square-check"></span>
                                                <span>{{ translate('I agree to the') }}</span>
                                            </label>
                                            <a href="{{ route('terms') }}" class="fw-700">{{ translate('terms and conditions') }}</a>,
                                            <a href="{{ route('returnpolicy') }}" class="fw-700">{{ translate('return policy') }}</a> &
                                            <a href="{{ route('privacypolicy') }}" class="fw-700">{{ translate('privacy policy') }}</a>
                                        </div>
                                        @endif

                                        <div>
                                            {{-- button --}}
                                        @if($order->request_preorder_status == 2)    
                                            @if($order->prepayment_confirm_status == 2)
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                            </div>
                                            @elseif($order->prepayment_confirm_status == 3)
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-danger"  disabled >{{  translate('Rejected') }}</button>
                                                </div>
                                            @elseif($order->prepayment_confirm_status == 1)
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-secondary " disabled >{{  translate('Requested') }}</button>
                                                </div>
                                            @else
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-soft-primary " {{$order->request_preorder_status !== 2 ? 'disabled' : ''}}>{{  translate('Request')  }}</button>
                                            </div>
                                            @endif
                                        @endif
                                            {{-- <button class="btn btn-block text-orange btn-preorder-not-accepted" {{ in_array($order->prepayment_confirm_status, [2, 3]) || $order->request_preorder_status !== 2 ? 'disabled' : ''}}>{{$order->prepayment_confirm_status == 1 ? translate('Requested') : ($order->prepayment_confirm_status == 2 ? translate('Request Accepted') : translate('Request PrePayment')) }}</button> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                         <!-- Final Payment & Order Completion -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingFinalOrder" type="button" data-toggle="collapse" data-target="#collapseFinalOrder" aria-expanded="true" aria-controls="collapseFinalOrder">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->final_order_status, $order->prepayment_confirm_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Final Payment & Order Completion') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapseFinalOrder" class="collapse " aria-labelledby="headingFinalOrder" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <form action="{{route('preorder.order_update', $order->id)}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="final_order" value="1">

                                        {{-- Nested Accordion --}}
                                        <div class="accordion mt-4" id="nestedAccordion">
                                            {{-- Address --}}
                                            <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                                                <div class="card-header border-bottom-0 py-3 py-xl-4" id="nestedAccordionItem1"
                                                    type="button" data-toggle="collapse" data-target="#collapsenestedAccordionItem1"
                                                    aria-expanded="true" aria-controls="nestedAccordionItem1">
                                                    <div class="d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                            <path id="Path_42357" data-name="Path 42357"
                                                                d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                                                transform="translate(-48 -48)" fill="#9d9da6" />
                                                        </svg>
                                                        <span class="ml-2 fs-19 fw-700">{{ translate('Address') }}</span>
                                                    </div>
                                                    <i class="las la-angle-down fs-18"></i>
                                                </div>
                                                <div id="collapsenestedAccordionItem1" class="collapse p-4 rounded-2" aria-labelledby="nestedAccordionItem1" data-parent="#nestedAccordion">
                                                    @include('preorder.frontend.order.left.shipping_info', ['address_id' => 5, 'order'=> $order])
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delivery Info -->
                                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; overflow: visible !important; border-radius: 0.5rem !important;">
                                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingDeliveryInfo" type="button"
                                                data-toggle="collapse" data-target="#collapseDeliveryInfo" aria-expanded="true"
                                                aria-controls="collapseDeliveryInfo">
                                                <div class="d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                        <path id="Path_42357" data-name="Path 42357"
                                                            d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                                            transform="translate(-48 -48)" fill="#9d9da6" />
                                                    </svg>
                                                    <span class="ml-2 fs-19 fw-700">{{ translate('Delivery Info') }}</span>
                                                </div>
                                                <i class="las la-angle-down fs-18"></i>
                                            </div>
                                            <div id="collapseDeliveryInfo" class="collapse" aria-labelledby="headingDeliveryInfo"
                                                data-parent="#nestedAccordion">
                                                <div class="card-body" id="delivery_info">
                                                    @include('preorder.frontend.order.partials.delivery_info', ['carrier_list' => $carrier_list, 'shipping_info' => $shipping_info, 'order'=> $order])
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Info -->
                                        <div class="card rounded-0 border shadow-none"
                                            style="margin-bottom: 2rem; overflow: visible !important; border-radius: 0.5rem !important;">
                                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingPaymentInfo" type="button"
                                                data-toggle="collapse" data-target="#collapseheadingPaymentInfo" aria-expanded="true"
                                                aria-controls="headingPaymentInfo">
                                                <div class="d-flex align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                                        <path id="Path_42357" data-name="Path 42357"
                                                            d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                                            transform="translate(-48 -48)" fill="#9d9da6" />
                                                    </svg>
                                                    <span class="ml-2 fs-19 fw-700">{{ translate('Payment Info') }}</span>
                                                </div>
                                                <i class="las la-angle-down fs-18"></i>
                                            </div>
                                            <div id="collapseheadingPaymentInfo" class="collapse" aria-labelledby="headingPaymentInfo" data-parent="#nestedAccordion">

                                                <div class="card-body" id="delivery_info">
                                    
                                                        <input type="hidden" name="finalpayment_confirmation" value="1">

                                                        <div class="d-flex justify-content-between">
                                                            <div class="mr-4">
                
                                                                @if($order->preorder_product->user->user_type == 'admin')
                                                                <p> {!! get_setting('pre_payment_instruction') !!}</p>
                                                                @else
                                                                <p> {!! $seller?->pre_payment_instruction !!} </p>
                                                                @endif
                
                                                            </div>
                                                            @if($instruction_qr_code)
                                                            <div class="mb-3">
                                                                <img class="w-120px h-120px" src="{{ uploaded_asset($instruction_qr_code) }}" alt="">
                                                            </div>
                                                            @endif
                                                        </div>
                
                                                    <div class="form-section preorder-border-dashed-grey p-4 rounded-2">
                                                        @if($order->final_order_status == 0 && $order->preorder_product?->is_cod)
                                                        <div class="form-group row">
                                                            <label class="col-md-6 col-from-label">{{translate('Cash on delivery')}}</label>
                                                            <div class="col-md-6">
                                                                <label class="aiz-switch aiz-switch-success mb-0">
                                                                    <input type="checkbox" name="cod_for_final_order" value="1" id="final_payment_cash_on_delivery" onchange="toggleFinalpaymentFields()">
                                                                    <span></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($order->final_order_status == 0)
                                                        <div class="row" id="final_payment_fields">
                                                            <div class="col-6">
                                                                <div class="form-group ">
                                                                    <label class="col-form-label" for="signinSrEmail">{{translate('Proof
                                                                        of payment')}}</label>
                                                                    <div class="input-group" data-toggle="aizuploader"
                                                                        data-type="image">
                                                                        @if($order->final_order_status == 0)
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text bg-soft-secondary font-weight-medium"> {{ translate('Browse')}}</div>
                                                                        </div>
                                                                        <div class="form-control file-amount">{{ translate('Choose
                                                                            File') }}</div>
                                                                        @endif
                                                                        <input type="hidden" name="final_payment_proof"
                                                                            class="selected-files" value="{{$order->final_payment_proof}}">
                                                                    </div>
                                                                    <div class="file-preview box sm">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group ">
                                                                    <label class="col-form-label"
                                                                        for="signinSrEmail">{{translate('Reference No.')}}</label>
                                                                    <input type="text"
                                                                        class="form-control @error('reference_no') is-invalid @enderror"
                                                                        name="final_payment_reference_no" 
                                                                        placeholder="{{ translate('Ref No') }}"
                                                                        onchange="update_sku()"  value="{{$order->final_payment_reference_no}}" @if($order->final_order_status !== 0) readonly @endif>
                
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                        @if($order->final_order_status !== 0) 
                                                        <div class="row">
                                                            <div class="col d-flex justify-content-between">
                                                                <div>
                                                                    @if($order->cod_for_final_order)
                                                                    <p><b>{{translate('Payment Type')}}</b></p>
                                                                    @else
                                                                    <p><b>{{translate('Proof of payment')}}</b></p>
                                                                    <p><b>{{translate('Reference No.')}}</b></p>
                                                                    @endif
                                                                </div>
                                                                <div class="ml-4">
                                                                    @if($order->cod_for_final_order)
                                                                    <p><b>{{translate('Cash on delivery')}}</b></p>
                                                                    @else
                                                                    <p> <img class="w-50px h-50px" src="{{ uploaded_asset($order->final_payment_proof) }}" alt=""> </p>
                                                                    <p>{{$order->final_payment_reference_no}}</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                                                        @endif

                                                        <div class="row">
                                                            <div class="col-12">
                                                                @if($order->final_order_status == 0) 
                                                                <div class="form-group ">
                                                                    <label class="col-form-label"
                                                                        for="signinSrEmail">{{translate('Note')}}</label>
                
                                                                    <textarea name="final_payment_confirm_note" rows="4"
                                                                        class="form-control" ></textarea>
                                                                </div>
                                                                @endif
                                                            @if($order->final_payment_confirm_note != null)
                                                                <div>
                                                                    <div class="mt-4">
                                                                        <p class="border p-2" style="font-style: italic;"> {{ $order->final_payment_confirm_note }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($order->final_oder_note)
                                        <div class="col-12">
                                            <div class="form-group ">
                                                <label class="col-form-label fw-700"
                                                    for="signinSrEmail">{{translate('Note From Seller')}}:</label>
                                                <p>{{ $order->final_oder_note }}</p>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="">
                                            @php
                                             $condition = $order->preorder_product?->is_prepayment ? $order->prepayment_confirm_status == 2 : $order->request_preorder_status == 2;
                                            @endphp 
                                            @if($condition)
                                                @if($order->final_order_status == 2)
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                                </div>
                                                @elseif($order->final_order_status == 3)
                                                    <div class="col-12 m-0 p-0">
                                                        <button type="submit" class="btn btn-block btn-danger"  disabled >{{  translate('Rejected') }}</button>
                                                    </div>
                                                @elseif($order->final_order_status == 1)
                                                    <div class="col-12 m-0 p-0">
                                                        <button type="submit" class="btn btn-block btn-secondary " disabled >{{  translate('Requested') }}</button>
                                                    </div>
                                                @else
                                                    <div class="col-12 m-0 p-0">
                                                        <button type="submit" class="btn btn-block btn-soft-primary " {{$order->preorder_product?->is_prepayment && $order->prepayment_confirm_status == 2 ? '' : ( $order->request_preorder_status == 2 ? '' : 'disabled')}}>{{  translate('Request')  }}</button>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if($order->shipping_status ==2)
                        <!-- shipping Info  -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingShippingInfo" type="button" data-toggle="collapse" data-target="#collapseShippingInfo" aria-expanded="true" aria-controls="collapseShippingInfo">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->shipping_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Shipment Details') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapseShippingInfo" class="collapse " aria-labelledby="headingShippingInfo" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <div class="row preorder-border-dashed-grey p-4 m-4 rounded-2">
                                        <div class="col d-flex justify-content-between">
                                            <div>
                                                <p><b>{{translate('Proof of shipping')}}</b></p>
                                                <p><b>{{translate('Notes')}}</b></p>
                                            </div>
                                            <div class="ml-4">
                                                <p> <img class="w-50px h-50px"
                                                        src="{{ uploaded_asset($order->shipping_proof) }}" alt=""> </p>
                                                <p>{{$order->shipping_note}}</p>
                                            </div>
                                        </div>
    
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($order->delivery_status ==2)
                        <!-- Delivery Info  -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingDeliverInfo" type="button" data-toggle="collapse" data-target="#collapseDeliverInfo" aria-expanded="true" aria-controls="collapseDeliverInfo">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->delivery_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Delivery Notes') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapseDeliverInfo" class="collapse " aria-labelledby="headingDeliverInfo" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="delivery_info">
                                    <div class="">
                                        <div class="row preorder-border-dashed-grey p-4 m-4 rounded-2">
                                            <div class="col d-flex justify-content-between">
                                                <div>
                                                    <p><b>{{translate('Notes')}}</b></p>
                                                </div>
                                                <div class="ml-4">
                                                    <p>{{$order->delivery_note}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($order->preorder_product?->is_refundable && $order->delivery_status==2)
                        <!-- Refund Request  -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingRefundRequest" type="button" data-toggle="collapse" data-target="#collapseRefundRequest" aria-expanded="true" aria-controls="collapseRefundRequest">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->refund_status) }}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Refund Request') }}</span>

                                </div>

                                <div>
                                    @if($order->refund_status == 2)
                                    <span class="badge badge-inline badge-success m-2 p-2 rounded-3 mr-4">{{ translate('Accepted')}}</span>
                                    @elseif($order->refund_status == 3)
                                    <span class="badge badge-inline badge-danger m-2 p-2 rounded-3 mr-4">{{ translate('Rejected')}}</span>
                                    @endif
                                    <i class="las la-angle-down fs-18"></i>
                                </div>


                            </div>
                            <div id="collapseRefundRequest" class="collapse " aria-labelledby="headingRefundRequest" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <form action="{{route('preorder.order_update', $order->id)}}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="refund_request" value="1">
        
                                        <div class="preorder-border-dashed-grey mt-0">
                                            <div class="row px-4  d-flex ">
                                                <div class="col-12">
                                                    <div class="form-group ">
                                                        <label class="col-form-label" for="signinSrEmail">{{translate('Refund Image')}}</label>
                                                        <div class="input-group" data-toggle="aizuploader"
                                                            data-type="image">
                                                            @if($order->refund_status == 0)
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text bg-soft-secondary font-weight-medium"> {{ translate('Browse')}}</div>
                                                            </div>
                                                            <div class="form-control file-amount">{{ translate('Choose
                                                                File') }}</div>
                                                            @endif
                                                            <input type="hidden" name="refund_proof"
                                                                class="selected-files" value="{{$order->refund_proof}}">
                                                        </div>
                                                        <div class="file-preview box sm">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    @if($order->refund_status == 0)
                                                    <div class="form-group ">
                                                        <label class="col-form-label"
                                                            for="signinSrEmail">{{translate('Note')}}<span
                                                                class="ml-2">{{translate(('(Upto 200
                                                                character)'))}}</span></label>
            
                                                        <textarea name="refund_note" rows="4"
                                                            class="form-control"></textarea>
                                                    </div>
                                                    @endif
                                                    @if($order->refund_note != null)
                                                    <div>
                                                        <div class="mt-4">
                                                            <p class="border p-2" style="font-style: italic;"> {{ $order->refund_note }}</p>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                </div>
                                            </div>
                                        </div>

                                        @if($order->seller_refund_note)
                                        <div class="col-12">
                                            <div class="form-group ">
                                                <label class="col-form-label"
                                                    for="signinSrEmail">{{translate('Note From Seller')}}:</label>
                                                <p>{{ $order->seller_refund_note }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="mt-4">
                                            {{-- button --}}
                                            @if($order->refund_status == 2)
                                            <div class="col-12 m-0 p-0">
                                                <button type="submit" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                            </div>
                                            @elseif($order->refund_status == 3)
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-danger"  disabled >{{  translate('Rejected') }}</button>
                                                </div>
                                            @elseif($order->refund_status == 1)
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-secondary " disabled >{{  translate('Requested') }}</button>
                                                </div>
                                            @else
                                                <div class="col-12 m-0 p-0">
                                                    <button type="submit" class="btn btn-block btn-soft-primary " >{{  translate('Request')  }}</button>
                                                </div>
                                            @endif
                                            
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($order->delivery_status ==2)
                        <!-- Review  -->
                        <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem; border-radius: 0.5rem !important;">
                            <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingReviewRating" type="button" data-toggle="collapse" data-target="#collapseReviewRating" aria-expanded="true" aria-controls="collapseReviewRating">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                        <path id="Path_42357" data-name="Path 42357" d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z" transform="translate(-48 -48)" fill="{{is_review_given($order)}}"/>
                                    </svg>
                                    <span class="ml-2 fs-19 fw-700">{{ translate('Your Review') }}</span>
                                </div>
                                <i class="las la-angle-down fs-18"></i>
                            </div>
                            <div id="collapseReviewRating" class="collapse " aria-labelledby="headingReviewRating" data-parent="#accordioncCheckoutInfo">
                                <div class="card-body" id="shipping_info">
                                    <div class="bg-white border mb-4 rounded-2">
                                        <div class="p-3 p-sm-4">
                                            <h3 class="fs-16 fw-700 mb-0">
                                                <span class="mr-4 text-uppercase">{{ translate('Your Reviews & Ratings') }}</span>
                                            </h3>
                                        </div>
                                        <!-- Ratting -->
                                        <div class="px-3 px-sm-4 mb-4">
                                            <div class="border border-secondary-base  p-3 p-sm-4 rounded-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6 mb-3">
                                                        <div class=" align-items-center  justify-content-md-start">
                                    
                                                            <div class="w-100 ">
                                                                <span class="fs-48 fw-700 mr-3 d-block mb-0">{{ $order->preorder_product?->rating }}</span>
                                    
                                                                <span class="fs-14 mr-3 d-block mt-0">{{ translate('out of 5.0') }}</span>
                                                            </div>
                                    
                                                            <div class="mt-sm-1 w-100 w-sm-auto d-flex flex-wrap justify-content-end justify-content-md-start">
                                                                @php
                                                                    $total = 0;
                                                                    $total += $order->preorder_product?->preorderProductreviews->count();
                                                                @endphp
                                                                <span class="rating rating-mr-2">
                                                                    {{ renderStarRating($order->preorder_product?->rating) }}
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
                                    
                                                        <a  href="javascript:void(0);" onclick="preorder_product_review('{{ $order->preorder_product?->id }}')"
                                                            class="btn px-4 border-yellow hov-bg-yellow p1-3 rounded-4 mt-3">
                                                            <span class="d-md-inline-block "> {{ translate('Rate this Product') }}</span>
                                                        </a>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Reviews -->
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>

                </div>

                <!-- Product  -->
                <div class="col-md-4">
                    <!-- payment summary -->
                    @include('preorder.frontend.order.right.payment_summary')
                    @include('preorder.frontend.order.right.preorder_status')
                    @include('preorder.frontend.order.right.order_summary')

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('modal')
    <!-- Address Modal -->
    @if(Auth::check())
        @include('frontend.partials.address.address_modal')
    @endif

    <!-- Product Review Modal -->
    <div class="modal fade" id="preorder-product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content" id="preorder-product-review-modal-content">

            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    window.onload = function() {
        window.scrollTo(0, 0); // Scrolls to the top of the page
    };
</script>
<script type="text/javascript">

function stepCompletionShippingInfo() {
            var headColor = '#9d9da6';
            var btnDisable = true;
            var allOk = false;
            @if (Auth::check())
                var length = $('input[name="address_id"]:checked').length;
                if (length > 0) {
                    headColor = '#15a405';
                    btnDisable = false;
                    allOk = true;
                }
            @else
                var count = 0;
                var length = $('#shipping_info [required]').length;
                $('#shipping_info [required]').each(function (i, el) {
                    if ($(el).val() != '' && $(el).val() != undefined && $(el).val() != null) {
                        count += 1;
                    }
                });
                if (count == length) {
                    headColor = '#15a405';
                    btnDisable = false;
                    allOk = true;
                }
            @endif

            $('#headingShippingInfo svg *').css('fill', headColor);
            $("#submitOrderBtn").prop('disabled', btnDisable);
            return allOk;
        }

        $('#shipping_info [required]').each(function (i, el) {
            $(el).change(function(){
                if ($(el).attr('name') == 'address_id') {
                }
                @if (get_setting('shipping_type') == 'area_wise_shipping')
                    if ($(el).attr('name') == 'city_id') {
                        let country_id = $('select[name="country_id"]').val();
                        let city_id = $(this).val();
                    }
                @endif
                stepCompletionShippingInfo();
            });
        });

        function updateDeliveryAddress(id, city_id = 0) {
            $('.aiz-refresh').addClass('active');
            $.post('{{ route('checkout.updateDeliveryAddress') }}', {
                _token: AIZ.data.csrf,
                address_id: id,
                city_id: city_id
            }, function(data) {
                $('#delivery_info').html(data.delivery_info);
                $('#cart_summary').html(data.cart_summary);
                $('.aiz-refresh').removeClass('active');
            });
            AIZ.plugins.bootstrapSelect("refresh");
        }

        function stepCompletionDeliveryInfo() {
            var headColor = '#9d9da6';
            var btnDisable = true;
            var allOk = false;
            var content = $('#delivery_info [required]');
            if (content.length > 0) {
                var content_checked = $('#delivery_info [required]:checked');
                if (content_checked.length > 0) {
                    content_checked.each(function (i, el) {
                        allOk = false;
                        if($(el).val() == 'carrier'){
                            var owner = $(el).attr('data-owner');
                            if ($('input[name=carrier_id_'+owner+']:checked').length > 0) {
                                allOk = true;
                            }
                        }else if($(el).val() == 'pickup_point'){
                            var owner = $(el).attr('data-owner');
                            if ($('select[name="pickup_point_id_'+owner+'"]').val() != '') {
                                allOk = true;
                            }
                        }else{
                            allOk = true;
                        }

                        if(allOk == false) {
                            return false;
                        }
                    });

                    if (allOk) {
                        headColor = '#15a405';
                        btnDisable = false;
                    }
                }
            }else{
                allOk = true
                headColor = '#15a405';
                btnDisable = false;
            }

            $('#headingDeliveryInfo svg *').css('fill', headColor);
            $("#submitOrderBtn").prop('disabled', btnDisable);
            return allOk;
        }

        function show_pickup_point(el, user_id) {
        	var type = $(el).val();
        	var target = $(el).data('target');
            var type_id = null;

        	if(type == 'home_delivery' || type == 'carrier'){
                if(!$(target).hasClass('d-none')){
                    $(target).addClass('d-none');
                }
                $('.carrier_id_'+user_id).removeClass('d-none');
        	}else{
        		$(target).removeClass('d-none');
        		$('.carrier_id_'+user_id).addClass('d-none');
        	}

            if(type == 'carrier'){
                type_id = $('input[name=carrier_id_'+user_id+']:checked').val();
            }else if(type == 'pickup_point'){
                type_id = $('select[name=pickup_point_id_'+user_id+']').val();
            }
            updateDeliveryInfo(type, type_id, user_id);
        }



        // Preorder Product Review
        function preorder_product_review(product_id) {
            @if (isCustomer())
                $.post('{{ route('preorder.product_review_modal') }}', {
                    _token: '{{ @csrf_token() }}',
                    product_id: product_id
                }, function(data) {
                    $('#preorder-product-review-modal-content').html(data);
                    $('#preorder-product-review-modal').modal('show', {
                        backdrop: 'static'
                    });
                    AIZ.extra.inputRating();
                });

            @elseif (Auth::check() && !isCustomer())
                AIZ.plugins.notify('warning', '{{ translate("Sorry, Only customers can give review.") }}');
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

        // hide prepayment fields
        function togglePrepaymentFields() {
        const codCheckbox = document.getElementById('prepayment_cash_on_delivery');
        const paymentFields = document.getElementById('prepayment_fields');

        if (codCheckbox.checked) {
            paymentFields.style.display = 'none';
        } else {
            paymentFields.style.display = 'flex';  // or 'block' for vertical layout
        }
    }

    // Ensure the correct state is set on page load
    window.onload = function() {
        togglePrepaymentFields();
    };


        // hide final payment  fields
        function toggleFinalpaymentFields() {
        const codCheckbox = document.getElementById('final_payment_cash_on_delivery');
        const paymentFields = document.getElementById('final_payment_fields');

        if (codCheckbox.checked) {
            paymentFields.style.display = 'none';
        } else {
            paymentFields.style.display = 'flex';  // or 'block' for vertical layout
        }
    }

    // Ensure the correct state is set on page load
    window.onload = function() {
        toggleFinalpaymentFields();
    };
</script>
@include('frontend.partials.address.address_js')
@endsection
