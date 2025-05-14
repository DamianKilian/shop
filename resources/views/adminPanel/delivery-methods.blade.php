@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        delivery methods
        <admin-panel-delivery-methods add-delivery-method-url="{{ route('admin-panel-add-delivery-method') }}"
            get-delivery-methods-url="{{ route('admin-panel-get-delivery-methods') }}"
            delete-delivery-methods-url="{{ route('admin-panel-delete-delivery-methods') }}">
        </admin-panel-delivery-methods>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._delivery-methods';
    </script>
@endsection
