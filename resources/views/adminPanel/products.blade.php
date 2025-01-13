@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-products :categories-prop='@json($categories)'
            category-options-prop='{{ $categoryOptions }}'
            admin-panel-upload-attachment-url="{{ route('admin-panel-upload-attachment') }}"
            admin-panel-fetch-url-url="{{ route('admin-panel-fetch-url') }}"
            admin-panel-upload-file-url="{{ route('admin-panel-upload-file') }}"
            admin-panel-delete-products-url="{{ route('admin-panel-delete-products') }}"
            admin-panel-get-products-url="{{ route('admin-panel-get-products') }}"
            admin-panel-get-product-filter-options-url="{{ route('admin-panel-get-product-filter-options') }}"
            admin-panel-get-product-desc-url="{{ route('admin-panel-get-product-desc') }}"
            admin-panel-add-options-to-selected-products-url="{{ route('admin-panel-add-options-to-selected-products') }}"
            admin-panel-add-product-url="{{ route('admin-panel-add-product') }}"
            admin-panel-apply-changes-product-url="{{ route('admin-panel-apply-changes-product') }}"
            admin-panel-toggle-active-product-url="{{ route('admin-panel-toggle-active-product') }}"
            admin-panel-delete-products-url="{{ route('admin-panel-delete-products') }}"
        ></admin-panel-products>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._products';
    </script>
@endsection
