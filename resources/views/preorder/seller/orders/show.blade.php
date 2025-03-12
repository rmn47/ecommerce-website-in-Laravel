@extends('seller.layouts.app')
@section('panel_content')

<div>

    <div class="row d-flex g-3">
        <div class="col-md-8 col-sm-12">
            <!--First element -->
            <div class="top-left-section border p-4">
                <div class="p-4 text-white d-flex justify-content-between bg-gunmetal-blue rounded-2">
                    <div>
                        <p class="fs-24 m-0 fw-700 pb-2">{{ translate('Order') }} #{{$order?->order_code}}</p>
                        <div class="d-flex">
                            <p class="text-white m-0">
                                <span class="fw-800">{{\Carbon\Carbon::parse($order->created_at)->format('d.m.Y')}}</span>
                                at
                                <span class="fw-800">{{\Carbon\Carbon::parse($order->created_at)->format(' h.i A')}}</span>
                            </p>
                            <p class="text-white m-0 pl-3">
                                {{ translate('Seller') }} 
                                <span class="fw-800">{{$order->preorder_product->user->shop != null ? $order->preorder_product->user->shop->name : translate('In-house Product')}}</span>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 5px;">
                        <a class="btn text-white p-1 border-0" style="background-color: #6C7A8B;" href="{{route('preorder.invoice_preview', $order->id)}}">
                            <i class="las la-print fs-16"></i>
                        </a>

                        <a class="btn text-white p-1 border-0" style="background-color: #6C7A8B;" href="{{route('preorder.invoice_download', $order->id)}}">
                            <i class="las la-download fs-16"></i>
                        </a>
                    </div>
                </div>

                <div class="row mt-4 g-3 p-3">
                    <div class="col-md-6 col-sm-12">
                        <div class="preorder-border-dashed p-4 rounded-2 d-flex justify-content-between row">
                            <div class="mb-3 col-sm-6">
                                @php
                                $removedXML = '
                                <?xml version="1.0" encoding="UTF-8"?>';
                                @endphp
                                {!! str_replace($removedXML, '', QrCode::size(100)->generate($order->code ??
                                "JKHKJHJHJG65")) !!}
                            </div>
                            <div class="ml-4">
                                <p>{{ translate('Order Status') }}</p>
                                <p>
                                    @if($order->delivery_status == 2)
                                        <span class="badge badge-inline badge-success p-2 rounded-3">{{ translate('Delivered')}}</span>
                                    @elseif($order->shipping_status == 2)
                                        <span class="badge badge-inline badge-info p-2 rounded-3">{{ translate('In Shipping')}}</span>
                                    @elseif($order->final_order_status == 1)
                                        <span class="badge badge-inline badge-warning p-2 rounded-3">{{ translate('Final Order Requested')}}</span>
                                    @elseif($order->final_order_status == 2)
                                        <span class="badge badge-inline badge-success p-2 rounded-3">{{ translate('Final Order Accepted')}}</span>
                                    @elseif($order->final_order_status == 3)
                                        <span class="badge badge-inline badge-danger p-2 rounded-3">{{ translate('Final Order Cancelled')}}</span>
                                    @elseif($order->prepayment_confirm_status == 1)
                                        <span class="badge badge-inline badge-primary p-2 rounded-3">{{ translate('Prepayment Requested')}}</span>
                                    @elseif($order->prepayment_confirm_status == 2)
                                        <span class="badge badge-inline badge-dodger-blue p-2 rounded-3">{{ translate('Prepayment Accepted')}}</span>
                                    @elseif($order->prepayment_confirm_status == 3)
                                        <span class="badge badge-inline badge-dodger-blue p-2 rounded-3">{{ translate('Prepayment Cancelled')}}</span>
                                    @elseif($order->request_preorder_status == 1)
                                        <span class="badge badge-inline badge-secondary p-2 rounded-3">{{ translate('Preorder Requested')}}</span>
                                    @elseif($order->request_preorder_status == 2)
                                        <span class="badge badge-inline badge-gray p-2 rounded-3">{{ translate('Preorder Request Accepted')}}</span>
                                    @elseif($order->request_preorder_status == 2)
                                        <span class="badge badge-inline badge-danger p-2 rounded-3">{{ translate('Preorder Request Cancelled')}}</span>
                                    @endif
                                </p>
                                <p>{{ translate('Amount') }}</p>
                                <p class="m-0 p-0"> {{format_price($order->subtotal)}}</p>
                                <p class="mb-1 p-0"><span>{{ translate('Prepay') }}</span> {{format_price($order->prepayment)}}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="preorder-border-dashed rounded-2 d-flex justify-content-between p-4">
                            <div>
                                <p><b>{{$order->user?->name}}</b></p>
                                <p>
                                <div>
                                    {{$order->user?->email}}
                                </div>
                                <div>
                                    {{$order->user?->phone}}
                                </div>
                                </p>
                                <p>{{$order->address?->address}}</p>
                            </div>
                            <div class="px-4 mt-4 mb-2">

                                    <a href="#">
                                        <span class="badge badge-dodger-blue w-100 text-blue p-3 rounded-3" 
                                            onclick="customer_history();">
                                            {{ translate("Customer’s History") }}
                                        </span>
                                    </a>


                                    <a href="#">
                                        <span class="mt-2 badge  w-100  p-3 rounded-3 {{$order->user->is_suspicious == 1 ? "badge-purple " : " badge-dodger-blue text-blue" }} " 
                                            onclick="confirm_suspicious('{{route('customers.suspicious', encrypt($order->user->id))}}');">
                                            {{ translate(" Mark as " . ($order->user->is_suspicious == 1 ? 'unsuspect' : 'suspicious') . " ") }}
                                        </span>
                                    </a>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 g-3">
                    <div class="col-6  p-4 d-flex justify-content-between">
                        <div>
                            <img src="{{ uploaded_asset($order->preorder_product->thumbnail) }}" alt="{{ translate('customer') }}" class="h-100 img-fit lazyload w-120px h-120px"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </div>
                        <div class="ml-4">
                            <p>{{ translate('Product') }}</p>
                            <p><b>{{$order->preorder_product->product_name}}</b></p>
                            <p>{{ translate('Quantity') }}</p>
                            <p><b>{{$order->quantity}} {{$order->preorder_product->unit}}</b></p>
                        </div>
                    </div>
                    <div class="col-6 d-flex justify-content-between p-4">
                        <div><p>{{ translate('Product Details') }}</p></div>
                        <div>
                            <p>{{ translate('UNIT Price') }}</p>
                            <p><b>{{format_price($order->preorder_product->unit_price)}}</b></p>
                            <p>{{ translate('Price') }}</p>
                            <p><b>{{format_price($order->subtotal)}}</b></p>
                        </div>
                    </div>
                </div>
            </div>

            <!--=====================================================================================================================================-->
            <!-- accordion -->
            <div class="accordion mt-4" id="accordioncCheckoutInfo">

                <!-- Request Preorder -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingRequestPreorder" type="button"
                        data-toggle="collapse" data-target="#collapsePreorderRequest" aria-expanded="true"
                        aria-controls="collapsePreorderRequest">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path id="Path_42357" data-name="Path 42357"
                                    d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                    transform="translate(-48 -48)" fill="{{preorder_fill_color($order->request_preorder_status)}}" />
                            </svg>
                            <span class="ml-2 fs-19 fw-700">{{ translate('Request Preorder') }}</span>
                        </div>
                        <p>
                            <span class="mr-4">{{$order->request_preorder_time != null ? \Carbon\Carbon::parse($order->request_preorder_time)->format('d.m.Y \a\t h.i A') : ''}}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapsePreorderRequest" class="collapse show" aria-labelledby="headingRequestPreorder"
                        data-parent="#accordioncCheckoutInfo">
                        <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="preorder_request_form">
                        
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="preorder_request_status" value="1">
                            <input type="hidden" name="status"  id="preorder-status" value="">
                            <div class="card-body" id="shipping_info">
                                <div class="px-4">
                                    <div class="w-100 my-2 border-bottom-dashed"></div>
                                </div>
                                <div class="row p-4">
                                    
                                    @if($order->request_note)
                                        <div class="col-3">
                                            <p><b>{{translate('Notes')}}</b></p>
                                        </div>
                                        <div class="col-9">
                                            <p>{{$order->request_note}}</p>
                                        </div>
                                    @endif


                                    @php $requestPreorderStatus = $order->request_preorder_status; @endphp
                                    @if(in_array($requestPreorderStatus, [2, 3]))
                                        <div class="col-md-12 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block fs-16 {{ $requestPreorderStatus == 2 ? 'btn-success' : 'btn-danger' }}" disabled >
                                               @if($requestPreorderStatus == 2) <i class="las la-check-circle"></i> @endif {{  $requestPreorderStatus == 2 ? translate(' Accepted') : translate('Rejected')  }}
                                            </button>
                                        </div>
                                    @elseif($requestPreorderStatus == 1 )
                                    <div class="col-md-3 col-sm-12 mt-1">
                                        <button type="button" class="btn btn-block btn-danger text-white "  onclick="preorderConfirmation(3, this, 'Preorder Request')">{{ translate('Reject')}}</button>
                                    </div>
                                    <div class="col-md-9 col-sm-12 mt-1">
                                        <button type="button" class="btn btn-block btn-soft-primary "  onclick="preorderConfirmation(2, this, 'Preorder Request')">{{  translate('Accept') }}</button>
                                    </div>
                                    @else
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white " disabled >{{ translate('Reject')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-secondary " disabled >{{  translate('Accept')  }}</button>
                                        </div>
                                    @endif
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if($order->preorder_product->is_prepayment)
                <!-- Prepayment Request -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingPrepaymentRequest" type="button"
                        data-toggle="collapse" data-target="#collapsePrepaymentRequest" aria-expanded="true"
                        aria-controls="collapsePrepaymentRequest">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path id="Path_42357" data-name="Path 42357"
                                    d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                    transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->prepayment_confirm_status) }}" />
                            </svg>
                            <span class="ml-2 fs-19 fw-700">{{ translate('Prepayment Request') }}</span>
                        </div>
                        <p>
                            <span class="mr-4">{{ $order->prepayment_confirmation_time != null ? \Carbon\Carbon::parse($order->prepayment_confirmation_time)->format('d.m.Y \a\t h.i A') : ''}}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapsePrepaymentRequest" class="collapse " aria-labelledby="headingPrepaymentRequest"
                        data-parent="#accordioncCheckoutInfo">
                        <div class="card-body" id="shipping_info">

                            <div class="">
                                <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="prepayment_confirm_form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="prepayment_confirm_status" value="1">
                                    <input type="hidden" name="status"  id="prepayment-status" value="">

                                    <div class="row preorder-border-dashed p-4 m-4">
                                        <div class="col d-flex justify-content-between">
                                            <div>

                                                <p><b>{{translate('Proof of payment')}}</b></p>
                                                <p><b>{{translate('Reference No.')}}</b></p>
                                                <p><b>{{translate('Notes')}}</b></p>
                                            </div>
                                            <div class="ml-4">
                                                <p> <img class="w-50px h-50px"
                                                        src="{{ uploaded_asset($order->payment_proof) }}" alt=""> </p>
                                                <p>{{$order->reference_no}}</p>
                                                <p>{{$order->confirm_note}}</p>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            @if($order->prepayment_confirm_status == 1) 
                                            <div class="form-group ">
                                                <label class="col-form-label"
                                                    for="signinSrEmail">{{translate('Note')}}<span
                                                        class="ml-2">{{translate(('(Upto 200
                                                        character)'))}}</span></label>
    
                                                <textarea name="prepayment_note" rows="4"
                                                    class="form-control"></textarea>
                                            </div>
                                            @endif
                                            @if($order->prepayment_note)
                                            <p class="border p-2" style="font-style: italic;"> {{ $order->prepayment_note }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="row px-4">
                                        {{-- button --}}
                                        @if($order->prepayment_confirm_status == 2 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                            </div>
                                        @elseif($order->prepayment_confirm_status == 3 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger" disabled >{{ translate('Rejected') }}</button>
                                            </div>
                                        @elseif($order->prepayment_confirm_status == 1 && $order->request_preorder_status == 2 )
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white" onclick="preorderConfirmation(3, this, 'Prepayment Request')">{{ translate('Reject')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-soft-primary" onclick="preorderConfirmation(2, this, 'Prepayment Request')">{{  translate('Accept') }}</button>
                                        </div>
                                            
                                        @else
                                            <div class="col-md-3 col-sm-12 mt-1" col-sm-12 mt-1>
                                                <button type="button" class="btn btn-block btn-danger text-white" disabled >{{ translate('Reject')}}</button>
                                            </div>
                                            <div class="col-md-9 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-secondary" disabled >{{ translate('Accept')  }}</button>
                                            </div>
                                        @endif

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Final Preorder -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingFinalPreorder" type="button"
                        data-toggle="collapse" data-target="#collapseFinalPreorder" aria-expanded="true"
                        aria-controls="collapseFinalPreorder">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path id="Path_42357" data-name="Path 42357"
                                    d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                    transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->final_order_status) }}" />
                            </svg>
                            <span class="ml-2 fs-19 fw-700">{{ translate('Final Preorder') }}</span>
                        </div>
                        <p>
                            <span class="mr-4">{{$order->final_order_time != null ? \Carbon\Carbon::parse($order->final_order_time)->format('d.m.Y \a\t h.i A') : ''}}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapseFinalPreorder" class="collapse " aria-labelledby="headingFinalPreorder"
                        data-parent="#accordioncCheckoutInfo">
                        <div class="card-body" id="shipping_info">

                            <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="final_order_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="final_order_status" value="1">
                                <input type="hidden" name="status"  id="final-order-status" value="">
                                <div class="">

                                    <div class="row preorder-border-dashed p-4 m-4">
                                        <div class="col d-flex justify-content-between">
                                            <div class="mr-2">
                                                @if($order->cod_for_final_order)
                                                <p><b>{{translate('Payment Type')}}</b></p>
                                                @else
                                                <p><b>{{translate('Proof of payment')}}</b></p>
                                                <p><b>{{translate('Reference No.')}}</b></p>
                                                @endif
                                                <p><b>{{translate('Notes')}}</b></p>
                                            </div>
                                            <div class="ml-4">
                                                @if($order->cod_for_final_order)
                                                <p><b>{{translate('Cash on delivery')}}</b></p>
                                                @else
                                                <p> <img class="w-50px h-50px" src="{{ uploaded_asset($order->final_payment_proof) }}" alt=""> </p>
                                                <p>{{$order->final_payment_reference_no}}</p>
                                                @endif
                                                
                                                <p>{{$order->final_payment_confirm_note}}</p>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            @if($order->final_order_status == 1) 
                                            
                                                <div class="form-group ">
                                                    <label class="col-form-label"
                                                        for="signinSrEmail">{{translate('Note')}}<span
                                                            class="ml-2">{{translate(('(Upto 200
                                                            character)'))}}</span></label>
        
                                                    <textarea name="final_oder_note" rows="4"
                                                        class="form-control" ></textarea>
                                                </div>
                                            
                                            @endif
                                            @if($order->final_oder_note)
                                                <p class="border p-2" style="font-style: italic;"> {{ $order->final_oder_note }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row px-4">
                                        {{-- button --}}
                                        @if($order->final_order_status == 2 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                            </div>
                                        @elseif($order->final_order_status == 3 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger"  disabled >{{  translate('Rejected') }}</button>
                                            </div>
                                        @elseif($order->final_order_status == 1 && ($order->prepayment_confirm_status == 2 ))
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white "  onclick="preorderConfirmation(3, this, 'Final Order Request')">{{ translate('Reject')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-soft-primary"  onclick="preorderConfirmation(2, this, 'Final Order Request')">{{  translate('Accept') }}</button>
                                        </div>
                                            
                                        @else
                                            <div class="col-md-3 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger text-white " disabled >{{ translate('Reject')}}</button>
                                            </div>
                                            <div class="col-md-9 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-secondary " disabled >{{ translate('Accept') }}</button>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- In Shipping -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingInShipping" type="button"
                        data-toggle="collapse" data-target="#collapseInShipping" aria-expanded="true"
                        aria-controls="collapseInShipping">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path id="Path_42357" data-name="Path 42357"
                                    d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                    transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->shipping_status) }}" />
                            </svg>
                            <span class="ml-2 fs-19 fw-700">{{ translate('In Shipping') }}</span>
                        </div>
                        <p>
                            <span class="mr-4">{{ $order->shipping_time != null ?  \Carbon\Carbon::parse($order->shipping_time)->format('d.m.Y \a\t h.i A') : ''}}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapseInShipping" class="collapse " aria-labelledby="headingInShipping"
                        data-parent="#accordioncCheckoutInfo">
                        <div class="card-body" id="shipping_info">
                            <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="shipping_status_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="shipping_status" value="2">
                                <input type="hidden" name="status"  id="preorder_shipping_status" value="">


                                <div class="row p-4  preorder-border-dashed p-4 m-4 d-flex ">
                                    <div class="col-12">
                                        <div class="form-group ">
                                            <label class="col-form-label" for="signinSrEmail">{{translate('Proof
                                                of shipping')}}</label>
                                            <div class="input-group" data-toggle="aizuploader"
                                                data-type="image">
                                                @if(!in_array($order->shipping_status, [2, 3]))
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text bg-soft-secondary font-weight-medium"> {{ translate('Browse')}}</div>
                                                </div>
                                                <div class="form-control file-amount">{{ translate('Choose
                                                    File') }}</div>
                                                @endif    
                                                <input type="hidden" name="shipping_proof"
                                                    class="selected-files" value="{{$order->shipping_proof}}">
                                            </div>
                                            <div class="file-preview box sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @if(!in_array($order->shipping_status, [2, 3])) 
                                        <div class="form-group ">
                                            <label class="col-form-label"
                                                for="signinSrEmail">{{translate('Note')}}<span
                                                    class="ml-2">{{translate(('(Upto 200
                                                    character)'))}}</span></label>

                                            <textarea name="shipping_note" rows="4"
                                                class="form-control" ></textarea>
                                        </div>
                                        @endif
                                        @if($order->shipping_note)
                                            <p class="border p-2" style="font-style: italic;"> {{ $order->shipping_note }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="row px-4">
                                    {{-- button --}}
                                    @if($order->shipping_status == 2 )
                                        <div class="col-md-12 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Marked as shipping')  }}</button>
                                        </div>
                                    @elseif($order->shipping_status == 3 )
                                        <div class="col-md-12 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger"  disabled >{{  translate('Cancelled') }}</button>
                                        </div>
                                    @elseif($order->shipping_status == 0 && ($order->final_order_status == 2) )
                                    <div class="col-md-3 col-sm-12 mt-1">
                                        <button type="button" class="btn btn-block btn-danger text-white "  onclick="preorderConfirmation(3, this, 'Shipping')">{{ translate('Cancel')}}</button>
                                    </div>
                                    <div class="col-md-9 col-sm-12 mt-1">
                                        <button type="button" class="btn btn-block btn-soft-primary"  onclick="preorderConfirmation(2, this, 'Shipping')">{{  translate('Mark as shipping') }}</button>
                                    </div>
                                        
                                    @else
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white " disabled >{{ translate('Reject')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-secondary " disabled >{{ translate('Accept') }}</button>
                                        </div>
                                        
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delivery -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingDelivery" type="button"
                        data-toggle="collapse" data-target="#collapseDelivery" aria-expanded="true"
                        aria-controls="collapseDelivery">
                        <div class="d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path id="Path_42357" data-name="Path 42357"
                                    d="M58,48A10,10,0,1,0,68,58,10,10,0,0,0,58,48ZM56.457,61.543a.663.663,0,0,1-.423.212.693.693,0,0,1-.428-.216l-2.692-2.692.856-.856,2.269,2.269,6-6.043.841.87Z"
                                    transform="translate(-48 -48)" fill="{{ preorder_fill_color($order->delivery_status) }}" />
                            </svg>
                            <span class="ml-2 fs-19 fw-700">{{ translate('Delivery') }}</span>
                        </div>
                        <p>
                            <span class="mr-4">{{ $order->delivery_time != null ?  \Carbon\Carbon::parse($order->delivery_time)->format('d.m.Y \a\t h.i A') : '' }}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapseDelivery" class="collapse" aria-labelledby="headingDelivery"
                        data-parent="#accordioncCheckoutInfo">
                        <div class="card-body" id="shipping_info">
                            <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="delivery_status_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="delivery_status" value="1">
                                <input type="hidden" name="status"  id="delivery-status" value="">

                                <div class="">
                                    <div class="row preorder-border-dashed p-4 m-4">
                                        <div class="col d-flex justify-content-between">
                                            <div>
                                                @if($order->cod_for_final_order)
                                                <p><b>{{translate('Payment Type')}}</b></p>
                                                @else
                                                <p><b>{{translate('Proof of payment')}}</b></p>
                                                <p><b>{{translate('Reference No.')}}</b></p>
                                                @endif
                                                <p><b>{{translate('Notes')}}</b></p>
                                            </div>
                                            <div class="ml-4">
                                                @if($order->cod_for_final_order)
                                                <p><b>{{translate('Cash on delivery')}}</b></p>
                                                @else
                                                <p> <img class="w-50px h-50px" src="{{ uploaded_asset($order->final_payment_proof) }}" alt=""> </p>
                                                <p>{{$order->final_payment_reference_no}}</p>
                                                @endif
                                                
                                                <p>{{$order->final_payment_confirm_note}}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 px-4">
                                        @if(!in_array($order->delivery_status, [2, 3]))  
                                        <div class="form-group ">
                                            <label class="col-form-label"
                                                for="signinSrEmail">{{translate('Note')}}<span
                                                    class="ml-2">{{translate(('(Upto 200
                                                    character)'))}}</span></label>

                                            <textarea name="delivery_note" rows="4"
                                                class="form-control" ></textarea>
                                        </div>
                                        @endif
                                        @if($order->delivery_note)
                                            <p class="border p-2" style="font-style: italic;"> {{ $order->delivery_note }}</p>
                                        @endif
                                    </div>

                                    <div class="row px-4">
                                        {{-- button --}}
                                        @if($order->delivery_status == 2)
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Delivered')  }}</button>
                                            </div>
                                        @elseif($order->delivery_status == 3)
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger" disabled >{{  translate('Cancelled') }}</button>
                                            </div>
                                        @elseif($order->shipping_status == 2)
                                            
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white" onclick="preorderConfirmation(3, this, 'Delivery')">{{ translate('Cancel')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-secondary" onclick="preorderConfirmation(2, this, 'Delivery')">{{ translate('Mark as deliver') }}</button>
                                        </div>
                                            
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if($order->preorder_product->is_refundable && $order->delivery_status==2)
                <!-- Refund -->
                <div class="card rounded-0 border shadow-none" style="margin-bottom: 2rem;">
                    <div class="card-header border-bottom-0 py-3 py-xl-4" id="headingRefund" type="button"
                        data-toggle="collapse" data-target="#collapseRefund" aria-expanded="true"
                        aria-controls="collapseRefund">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex justify-content-center align-items-center"
                                style="width: 20px; height: 20px; background-color: {{ preorder_fill_color($order->refund_status) }};">
                                <svg xmlns="http://www.w3.org/2000/svg" width="2" height="10" viewBox="0 0 2 10">
                                    <g id="Group_38901" data-name="Group 38901" transform="translate(-315 -540)">
                                      <rect id="Rectangle_23991" data-name="Rectangle 23991" width="2" height="7" rx="1" transform="translate(315 540)" fill="#fff"/>
                                      <rect id="Rectangle_23992" data-name="Rectangle 23992" width="2" height="2" rx="1" transform="translate(315 548)" fill="#fff"/>
                                    </g>
                                  </svg>
                            </div>
                            <span class="ml-2 fs-19 fw-700">{{ translate('Refund') }}</span>
                            @if($order->refund_status == 2)
                            <span class="badge badge-inline badge-success m-2 p-2 rounded-3">{{ translate('Accepted')}}</span>
                            @elseif($order->refund_status == 3)
                            <span class="badge badge-inline badge-danger m-2 p-2 rounded-3">{{ translate('Rejected')}}</span>
                            @endif
                        </div>
                        <p>
                            <span class="mr-4">{{ $order->refund_time != null ?  \Carbon\Carbon::parse($order->refund_time)->format('d.m.Y \a\t h.i A') : '' }}</span><i class="las la-angle-down fs-18"></i>
                        </p>
                    </div>

                    <div id="collapseRefund" class="collapse " aria-labelledby="headingRefund"
                        data-parent="#accordioncCheckoutInfo">
                        <div class="card-body" id="shipping_info">
                            <form action="{{route('seller.preorder-order.status_update', $order->id)}}" method="POST" id="refund_status_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="refund_status" value="1">
                                <input type="hidden" name="status"  id="refund-status" value="">

                                <div class="">
                                    <div class="row preorder-border-dashed p-4 m-4">
                                        <div class="col d-flex justify-content-between">
                                            <div>
                                                <p><b>{{translate('Proof of refund')}}</b></p>
                                                <p><b>{{translate('Notes')}}</b></p>
                                            </div>
                                            <div class="ml-4">
                                                <p> <img class="w-50px h-50px"
                                                        src="{{ uploaded_asset($order->refund_proof) }}" alt=""> </p>
                                                <p>{{$order->refund_note}}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 px-4">
                                        @if(!in_array($order->refund_status, [2, 3]))  
                                        <div class="form-group ">
                                            <label class="col-form-label"
                                                for="signinSrEmail">{{translate('Note')}}<span
                                                    class="ml-2">{{translate(('(Upto 200
                                                    character)'))}}</span></label>

                                            <textarea name="seller_refund_note" rows="4"
                                                class="form-control"></textarea>
                                        </div>
                                        @endif
                                        @if($order->seller_refund_note)
                                            <p class="border p-2" style="font-style: italic;"> {{ $order->seller_refund_note }}</p>
                                        @endif
                                    </div>
                                    <div class="row px-4">
                                        {{-- button --}}
                                        @if($order->refund_status == 2 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-success fs-16" disabled ><i class="las la-check-circle"></i> {{ translate(' Accepted')  }}</button>
                                            </div>
                                        @elseif($order->refund_status == 3 )
                                            <div class="col-md-12 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger"  disabled>{{  translate('Rejected') }}</button>
                                            </div>
                                        @elseif($order->refund_status == 1 && ($order->delivery_status == 2) )
                                        <div class="col-md-3 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-danger text-white" onclick="preorderConfirmation(3, this, 'Refund Request')">{{ translate('Reject')}}</button>
                                        </div>
                                        <div class="col-md-9 col-sm-12 mt-1">
                                            <button type="button" class="btn btn-block btn-soft-primary" onclick="preorderConfirmation(2, this, 'Refund Request')">{{  translate('Accept') }}</button>
                                        </div>
                                            
                                        @else
                                            <div class="col-md-3 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-danger text-white" disabled>{{ translate('Reject')}}</button>
                                            </div>
                                            <div class="col-md-9 col-sm-12 mt-1">
                                                <button type="button" class="btn btn-block btn-secondary" disabled>{{ translate('Accept') }}</button>
                                            </div>
                                            
                                        @endif

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Righ side start -->
        <div class="col-md-4 col-sm-12">
            <div class="order-summary preorder-border-dashed border-orange p-4">
                <p class="fs-16"><b>{{translate('Order Summary')}}</b>
                <div class="w-100 my-3 border-bottom-dashed "></div>
                </p>
                @include('preorder.common.order.order_summary')
            </div>
            <div class=" border p-4 mt-4">
                <p class="fs-16"><b>{{translate('Preorder Status')}}</b>
                </p>
                <div class="w-100 my-3 border-bottom-dashed "></div>
                <div class="order-summary">
                    
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->request_preorder_status !== 0 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Requested Pre order')}}</span>
                            <span class="opacity-60">{{ $order->request_preorder_time != null ? \Carbon\Carbon::parse($order->request_preorder_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>
                    
                    
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->request_preorder_status ==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Pre order request accepted')}}</span>
                            <span class="opacity-60">{{$order->request_preorder_time != null ?  \Carbon\Carbon::parse($order->request_preorder_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>

                    @if($order->preorder_product?->is_prepayment)
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->prepayment_confirm_status !== 0 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Pre-payment & confirmation')}}</span>
                            <span class="opacity-60">{{ $order->prepayment_confirmation_time != null ? \Carbon\Carbon::parse($order->prepayment_confirmation_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>
                    @endif


                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->prepayment_confirm_status==2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Pre-payment accepted & order confirmed')}}</span>
                            <span class="opacity-60">{{ $order->prepayment_confirmation_time != null ? \Carbon\Carbon::parse($order->prepayment_confirmation_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ (($order->preorder_product->available_date != null && (strtotime($order->preorder_product->available_date) < strtotime(date('d-m-Y')))) || $order->preorder_product->is_available) ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }}"></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Product is live')}}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->final_order_status ==2  ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Final order ')}}</span>
                            <span class="opacity-60">{{ $order->final_order_time != null ? \Carbon\Carbon::parse($order->final_order_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>
            
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->shipping_status ==2  ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Product In Shipping')}}</span>
                            <span class="opacity-60">{{ $order->shipping_time != null ? \Carbon\Carbon::parse($order->shipping_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>
            
                    <div class="d-flex align-items-center mb-4 p-0">
                        <div>
                            <i class="las {{ $order->delivery_status == 2 ? 'la-check bg-blue text-white fs-8 rounded-3 p-1' : 'la-circle fs-20 text-blue' }} "></i>
                        </div>
                        <div class="d-flex flex-column ml-4">
                            <span>{{translate('Product Delivered')}}</span>
                            <span class="opacity-60">{{ $order->delivery_time != null ?  \Carbon\Carbon::parse($order->delivery_time)->format('H:i \h\r\s, j F, Y') : ''}}</span>
                        </div>
                    </div>

                </div>



            </div>
        </div>
        <!-- Righ side End -->
    </div>
</div>

</div>
</div>

</div>
@endsection

@section('modal')

<!-- confirm payment Status Modal -->
<div id="confirm-payment-status" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
        <div class="modal-content p-2rem">
            <div class="modal-body text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                    <g id="Octicons" transform="translate(-0.14 -1.02)">
                        <g id="alert" transform="translate(0.14 1.02)">
                            <path id="Shape"
                                d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z"
                                transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd" />
                        </g>
                    </g>
                </svg>
                <p class="mt-3 mb-3 fs-16 fw-700">{{translate('Are you sure you want to change the payment status?')}}
                </p>
                <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{
                    translate('Cancel') }}</button>
                <button type="button" onclick="update_payment_status()"
                    class="btn btn-success rounded-2 mt-2 fs-13 fw-700 w-150px">{{translate('Confirm')}}</button>
            </div>
        </div>
    </div>
</div>

{{-- customer ban modal --}}
<div class="modal fade" id="confirm-ban">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ translate("Do you really want to " . ($order->user->banned == 1 ? 'Reinstate' : 'ban') . " this Customer?") }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="confirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>

{{-- customer suspicious modal --}}
<div class="modal fade" id="confirm-suspicious">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ translate("Do you really want to " . ($order->user->banned == 1 ? 'unsuspect' : 'suspect') . " this Customer?") }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a type="button" id="suspiciousConfirmation" class="btn btn-primary">{{translate('Proceed!')}}</a>
            </div>
        </div>
    </div>
</div>

{{-- customer History modal --}}
<div class="modal fade" id="customer-history">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card-body text-center">
                    <span class="avatar avatar-xxl mb-3">
                        @if ($order->user->avatar != null)
                            <img src="{{ uploaded_asset($order->user->avatar_original) }}">
                        @else
                            <img src="{{ my_asset('assets/frontend/default/img/avatar-place.png') }}0"
                                onerror="this.onerror=null;this.src='{{ static_asset('/assets/img/avatar-place.png') }}';">
                        @endif
                    </span>
                    <h1 class="h5 mb-1">{{ $order->user->name }}</h1>
                    <div class=" mt-5">
                        <h6 class="separator mb-4 "><span
                                class="bg-white pr-3">{{ translate('Account Information') }}</span></h6>
                        <p class="text-muted">
                            <strong>{{ translate('Full Name') }} :</strong>
                            <span class="ml-2">{{ $order->user->name }}</span>
                        </p>
                        <p class="text-muted"><strong>{{ translate('Email') }} :</strong>
                            <span class="ml-2">
                                {{ $order->user->email }}
                            </span>
                        </p>
                        <p class="text-muted"><strong>{{ translate('Phone') }} :</strong>
                            <span class="ml-2">
                                {{ $order->user->phone }}
                            </span>
                        </p>
                        <p class="text-muted"><strong>{{ translate('Registration Date') }} :</strong>
                            <span class="ml-2">
                                {{ $order->user->created_at }}
                            </span>
                        </p>
                    </div>
                    <div class=" mt-5">
                        <h6 class="separator mb-4 ">
                            <span class="bg-white pr-3">{{ translate('Preorder Information') }}
                            </span>
                        </h6>
                        <p class="text-muted">
                            <strong>{{ translate('Number of Orders') }} :</strong>
                            <span class="ml-2">{{ $order->user->preorders()->count() }}</span>
                        </p>
                        <p class="text-muted">
                            <strong>{{ translate('Ordered Amount') }} :</strong>
                            <span class="ml-2">{{ format_price($order->user->preorders()->sum('subtotal')) }}</span>
                        </p>
                        <p class="text-muted">
                            <strong>{{ translate('Number of orders in shipping') }} :</strong>
                            <span class="ml-2">{{ $order->user->preorders()->where('shipping_status',2)->where('delivery_status',0)->count() }}</span>
                        </p>
                        <p class="text-muted">
                            <strong>{{ translate('Number of items is delivered') }} :</strong>
                            <span class="ml-2">{{ $order->user->preorders()->where('delivery_status',2)->count() }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('preorder.common.models.seller_preorder_cormation')

@endsection


@section('script')
<script type="text/javascript">

        function confirm_ban(url)
        {
            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }
        function confirm_suspicious(url)
        {
            $('#confirm-suspicious').modal('show', {backdrop: 'static'});
            document.getElementById('suspiciousConfirmation').setAttribute('href' , url);
        }

        function customer_history()
        {
            $('#customer-history').modal('show', {backdrop: 'static'});
        }

        let currentFormId = null; 
        function preorderConfirmation(action, button, currentStatus)
        {
                // Find the parent form of the clicked button
                const form = button.closest('form');
                currentFormId = form.id; 

                // Dynamically update the "status" hidden input field within the form
                const statusField = form.querySelector('input[name="status"]');
                if (statusField) {
                    statusField.value = action;
                }

                const translateAcceptText = "{{ translate('Are you sure to accept :status?') }}";
                const translateRejectText = "{{ translate('Are you sure to reject :status?') }}";

                if (action === 2) { 
                    modalText = translateAcceptText.replace(':status', currentStatus); 
                } else if (action === 3) { 
                    modalText = translateRejectText.replace(':status', currentStatus); 
                } else {
                    modalText = "{{ translate('Are you sure to submit this change?') }}"; 
                }

                document.getElementById('modal-text').innerHTML = modalText;
            $('#preorderConfirmation').modal('show', {backdrop: 'static'});
        }

        function confirmPreorder() {
            if (currentFormId) {
                document.getElementById(currentFormId).submit(); // Submit the stored form by ID
            }
        }

        function setStatusAndSubmit(statusValue) {
            document.getElementById('final-order-status').value = statusValue;
            document.querySelector('form').submit();
        }

</script>
@endsection