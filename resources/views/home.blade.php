@extends('layouts.app')

@section('content')
    <div id="products-app">
        <pagination-widget :get-products-view='getProductsView' :current-page='currentPage'
            :last-page='lastPage'></pagination-widget>
        <div class="products-view-container position-relative">
            <loading-overlay v-if='getingProductsView'></loading-overlay>
            <div id="products-view" ref="productsView">
                @if ($products)
                    @include('_partials.products')
                @endif
            </div>
        </div>
    </div>
    @if (!$products)
        <div v-if='!productsViewLoaded' id="home-page">
            <h1>Home page</h1>
        </div>
    @endif
@endsection

@section('scriptsHead')
    <script>
        window.getProductsViewAllCategoriesUrl = "{{ route('get-products-view-all-categories') }}";
    </script>
@endsection
