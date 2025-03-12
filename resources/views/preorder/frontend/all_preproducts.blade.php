@extends('frontend.layouts.app')

@if (isset($category_id))
    @php
        $meta_title = $category->meta_title;
        $meta_description = $category->meta_description;
    @endphp
@elseif (isset($brand_id))
    @php
        $meta_title = get_single_brand($brand_id)->meta_title;
        $meta_description = get_single_brand($brand_id)->meta_description;
    @endphp
@else
    @php
        $meta_title         = get_setting('meta_title');
        $meta_description   = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')

    <section class="mb-4 pt-4">
        <div class="container sm-px-0 pt-2">
            <form class="" id="search-form" action="" method="GET">
                <div class="row">

                    <!-- Sidebar Filters -->
                    <div class="col-xl-3">
                        <div class="aiz-filter-sidebar collapse-sidebar-wrap sidebar-xl sidebar-right z-1035">
                            <div class="overlay overlay-fixed dark c-pointer" data-toggle="class-toggle" data-target=".aiz-filter-sidebar" data-same=".filter-sidebar-thumb"></div>
                            <div class="collapse-sidebar c-scrollbar-light text-left">
                                <div class="d-flex d-xl-none justify-content-between align-items-center pl-3 border-bottom">
                                    <h3 class="h6 mb-0 fw-600">{{ translate('Filters') }}</h3>
                                    <button type="button" class="btn btn-sm p-2 filter-sidebar-thumb" data-toggle="class-toggle" data-target=".aiz-filter-sidebar" >
                                        <i class="las la-times la-2x"></i>
                                    </button>
                                </div>

                                <!-- Categories -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#collapse_1" class="dropdown-toggle filter-section text-dark d-flex align-items-center justify-content-between" data-toggle="collapse">
                                            {{ translate('Categories')}}
                                        </a>
                                    </div>
                                    <div class="collapse show" id="collapse_1">
                                        <ul class="p-3 mb-0 list-unstyled">
                                            @if (!isset($category_id))
                                                @foreach ($categories as $category)
                                                    <li class="mb-3 text-dark">
                                                        <a class="text-reset fs-14 hov-text-primary" href="{{ route('preorder.category', $category?->slug) }}">
                                                            {{ $category->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="mb-3">
                                                    <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('all_preorder_products') }}">
                                                        <i class="las la-angle-left"></i>
                                                        {{ translate('All Categories')}}
                                                    </a>
                                                </li>
                                                
                                                @if ($category->parent_id != 0)
                                                    <li class="mb-3">
                                                        <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('preorder.category', get_single_category($category->parent_id)->slug) }}">
                                                            <i class="las la-angle-left"></i>
                                                            {{ get_single_category($category->parent_id)->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="mb-3">
                                                    <a class="text-reset fs-14 fw-600 hov-text-primary" href="{{ route('preorder.category', $category?->slug) }}">
                                                        <i class="las la-angle-left"></i>
                                                        {{ $category->getTranslation('name') }}
                                                    </a>
                                                </li>
                                                @foreach ($category->childrenCategories as $key => $immediate_children_category)
                                                    <li class="ml-4 mb-3">
                                                        <a class="text-reset fs-14 hov-text-primary" href="{{ route('preorder.category', $immediate_children_category?->slug) }}">
                                                            {{ $immediate_children_category->getTranslation('name') }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                 <!-- Attributes -->
                                <div class="bg-white border mb-3">
                                    <div class="fs-16 fw-700 p-3">
                                        <a href="#" class="dropdown-toggle text-dark filter-section collapsed d-flex align-items-center justify-content-between" 
                                            data-toggle="collapse" data-target="#collapse_availability_filter" style="white-space: normal;">
                                            {{ translate('Filter by Availability')}}
                                        </a>
                                    </div>
                                    @php
                                    $show = $is_available == null ? '' : 'show';
                                    @endphp
                                    <div class="collapse {{ $show }}" id="collapse_availability_filter">
                                        <div class="p-3 aiz-checkbox-list">
                                                <label class="aiz-checkbox mb-3">
                                                    <input
                                                        type="checkbox"
                                                        name="is_available[]"
                                                        value="1" @if ($is_available == 1) checked @endif
                                                        onchange="filter()"
                                                    >
                                                    <span class="aiz-square-check"></span>
                                                    <span class="fs-14 fw-400 text-dark">{{translate('Available Now')}}</span>
                                                </label>
                                                <label class="aiz-checkbox mb-3">
                                                    <input
                                                        type="checkbox"
                                                        name="is_available[]"
                                                        value="0" @if ($is_available == 0) checked @endif
                                                        onchange="filter()"
                                                    >
                                                    <span class="aiz-square-check"></span>
                                                    <span class="fs-14 fw-400 text-dark">{{translate('Upcoming ')}}</span>
                                                </label>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contents -->
                    <div class="col-xl-9">
                        
                        <!-- Breadcrumb -->
                        <ul class="breadcrumb bg-transparent py-0 px-1">
                            <li class="breadcrumb-item has-transition opacity-50 hov-opacity-100">
                                <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                            </li>
                            @if(!isset($category_id))
                                <li class="breadcrumb-item fw-700  text-dark">
                                    "{{ translate('All Preorder Products')}}"
                                </li>
                            @else
                                <li class="breadcrumb-item opacity-50 hov-opacity-100">
                                    <a class="text-reset" href="{{ route('search') }}">{{ translate('All Preorder Products')}}</a>
                                </li>
                            @endif
                            @if(isset($category_id))
                                <li class="text-dark fw-600 breadcrumb-item">
                                    "{{ $category->getTranslation('name') }}"
                                </li>
                            @endif
                        </ul>
                        
                        <!-- Top Filters -->
                        <div class="text-left">
                            <div class="row gutters-5 flex-wrap align-items-center">
                                <div class="col-lg col-10">
                                    <h1 class="fs-20 fs-md-24 fw-700 text-dark">
                                        @if(isset($category_id))
                                            {{ $category->getTranslation('name') }}
                                        @elseif(isset($query))
                                            {{ translate('Search result for ') }}"{{ $query }}"
                                        @else
                                            {{ translate('Preorder Products') }}
                                        @endif
                                    </h1>
                                    <input type="hidden" name="keyword" value="{{ $query }}">
                                </div>
                                <div class="col-2 col-lg-auto d-xl-none mb-lg-3 text-right">
                                    <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle" data-target=".aiz-filter-sidebar">
                                        <i class="la la-filter la-2x"></i>
                                    </button>
                                </div>

                                <div class="col-6 col-lg-auto mb-3 w-lg-200px">
                                    <select class="form-control form-control-sm aiz-selectpicker rounded-0" name="sort_by" onchange="filter()">
                                        <option value="">{{ translate('Sort by')}}</option>
                                        <option value="newest" @isset($sort_by) @if ($sort_by == 'newest') selected @endif @endisset>{{ translate('Newest')}}</option>
                                        <option value="oldest" @isset($sort_by) @if ($sort_by == 'oldest') selected @endif @endisset>{{ translate('Oldest')}}</option>
                                        <option value="price-asc" @isset($sort_by) @if ($sort_by == 'price-asc') selected @endif @endisset>{{ translate('Price low to high')}}</option>
                                        <option value="price-desc" @isset($sort_by) @if ($sort_by == 'price-desc') selected @endif @endisset>{{ translate('Price high to low')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card p-4">
                            <div class="px-3">
                                <div class="row gutters-16 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">
                                    @foreach ($preorder_products as $key => $product)
                                    <div class="col z-1 mt-2">
                                        @include('preorder.frontend.product_box2',['product' => $product])
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="aiz-pagination mt-4">
                                {{ $preorder_products->appends(request()->input())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('script')
    <script type="text/javascript">
        function filter(){
            $('#search-form').submit();
        }
        function rangefilter(arg){
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
    </script>
@endsection
