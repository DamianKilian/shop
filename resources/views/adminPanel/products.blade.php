@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-products :categories-prop='@json($categories)' suggestions-url="{{ route('suggestions') }}"
            category-options-prop='{{ $categoryOptions }}'
            admin-panel-delete-products-url="{{ route('admin-panel-delete-products') }}"
            admin-panel-get-products-url="{{ route('admin-panel-get-products') }}"
            admin-panel-add-product-url="{{ route('admin-panel-add-product') }}">
        </admin-panel-products>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._products';
    </script>
@endsection
