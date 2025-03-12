@extends('frontend.layouts.app')

@section('meta')


@endsection

@section('content')
<section class="mb-4 pt-3">
    <div class="container">
        <div class="bg-white py-3">
            
            <div class="row justify-content-center">
                <div class="col-md-8 col-sm-12">
                    <div class="rounded " style="background-color: #7A7A99">
                        <p class="my-4 p-4 text-white ps-16"><span class="ml-3">{{translate('HOW TO PREORDER ?')}}</span></p>
                    </div>
                
                        <div class="accordion mt-4" id="accordioncCheckoutInfo">
                            <!-- In Shipping -->
                            @foreach($faqs as $faq)
                            <div class="card rounded-0 border shadow-none">
                                <div class="card-header border-bottom-0" id="headingInShipping-{{$faq->id}}" type="button"
                                    data-toggle="collapse" data-target="#collapseInShipping-{{$faq->id}}" aria-expanded="true"
                                    aria-controls="headingInShipping-{{$faq->id}}">
                                    <div class="d-flex align-items-center">
                                        <span class="ml-2 fs-14 fw-700">{{$faq->question}}</span>
                                    </div>
                                    <p>
                                        <span class="mr-2"></span><i class="las la-angle-down fs-18 mt-2"></i>
                                    </p>
                                </div>
                                <div id="collapseInShipping-{{$faq->id}}" class="collapse " aria-labelledby="headingInShipping-{{$faq->id}}"
                                    data-parent="#accordioncCheckoutInfo">
                                    <div class="row p-4  d-flex ">
                                        <div class="col-12">
                                            <div class="form-group ">
                                                <label class="col-form-label" for="signinSrEmail">{{$faq->answer}}</label>
                                            
                                                <div class="file-preview box sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


@section('script')
<script type="text/javascript">
 
</script>

@endsection