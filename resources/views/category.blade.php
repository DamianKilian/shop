@extends('layouts.app')

@section('content')
    <pagination-widget :get-products-view='getProductsView' :current-page='currentPage' :load-products='loadProducts'
        :last-page='lastPage'></pagination-widget>
    <h1>{{ $category->name }}</h1>
    <div class="products-view-container position-relative">
        <loading-overlay v-if='getingProductsView'></loading-overlay>
        <div id="products-view" ref="productsView">
            @include('_partials.products')
        </div>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = "{{ $activeLinks }}";
        window.categoryChildrenIds = "{{ $categoryChildrenIds }}";
        window.getProductsViewUrl = "{{ route('get-products-view') }}";
        window.lastPage = '{{ $products->lastPage() }}';
    </script>
@endsection
