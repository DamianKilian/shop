@extends('layouts.app')

@section('titleAndDescription')
    <title>{{ App\Services\AppService::generateTitleAndDescription('TITLE_PRODUCT', $category, $product) }}</title>
    <meta name="description" content="{{ App\Services\AppService::generateTitleAndDescription('DESC_PRODUCT', $category, $product) }}">
@endsection

@section('content')
    <div id="home-page" class="page-wrapper">
        @include('_partials.product-content')
    </div>
@endsection

@include('_partials.settings')
