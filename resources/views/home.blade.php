@extends('layouts.app')

@section('content')
    <div id="products-app">
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
        window.getProductNumsUrl = "{{ route('get-product-nums') }}";
        window.lastPage = '{{ $products ? $products->lastPage() : 1 }}';
    </script>
@endsection
