@extends('layouts.app')

@section('titleAndDescription')
    <title>{{ App\Services\AppService::generateTitleAndDescription('TITLE_CATEGORY', $category) }}</title>
    <meta name="description" content="{{ App\Services\AppService::generateTitleAndDescription('DESC_CATEGORY', $category) }}">
@endsection

@section('content')
    <div id="products-app">
        <h1>{{ $category->name }}</h1>
        <div class="products-view-container position-relative">
            <loading-overlay v-if='getingProductsView'></loading-overlay>
            <div id="products-top-panel">
                @include('_partials.sorting')
            </div>
            <div id="products-view" ref="productsView" v-on:click="addToBasket">
                @include('_partials.products')
            </div>
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
