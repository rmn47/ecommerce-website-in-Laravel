@extends('frontend.layouts.app')

@section('content')
    <!-- Breadcrumb -->
    <section class="mb-4 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 text-lg-left text-center">
                    <h1 class="fw-700 fs-20 fs-md-24 text-dark">{{ translate('All Preorder Products') }}</h1>
                </div>
                <div class="col-lg-6">
                    <ul class="breadcrumb justify-content-center justify-content-lg-end bg-transparent p-0">
                        <li class="breadcrumb-item has-transition opacity-60 hov-opacity-100">
                            <a class="text-reset" href="{{ route('home') }}">{{ translate('Home') }}</a>
                        </li>
                        <li class="text-dark fw-600 breadcrumb-item">
                            "{{ translate('All Preorder Products') }}"
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- All Brands -->
    <section class="mb-4">
        <div class="container">
            <div class="bg-white px-3 pt-3">
                <div class="row row-cols-xxl-6 row-cols-xl-6 row-cols-lg-4 row-cols-md-4 row-cols-3 gutters-16 ">
                    @foreach ($products as $product)
                        <div class="col text-center  hov-scale-img  z-1 border m-2">
                            @include('preorder.frontend.product_box',['product' => $product])
                        </div>
                    @endforeach
                </div>
                <!-- Pagination -->
                <div class="aiz-pagination aiz-pagination-center mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
