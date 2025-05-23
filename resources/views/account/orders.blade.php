@extends('layouts.account')

@section('content')
    <div class="container">
        <orders get-orders-url="{{ route('get-orders') }}"
            admin-panel-get-order-data-url="{{ route('admin-panel-get-order-data') }}"></orders>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._orders';
    </script>
@endsection
