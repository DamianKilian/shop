@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-orders admin-panel-get-orders-url="{{ route('admin-panel-get-orders') }}"
            admin-panel-add-order-url="{{ route('admin-panel-add-order') }}"
            admin-panel-get-order-data-url="{{ route('admin-panel-get-order-data') }}"></admin-panel-orders>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._orders';
    </script>
@endsection
