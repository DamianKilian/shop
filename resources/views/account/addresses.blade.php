@extends('layouts.account')

@section('content')
    <div class="container">
        <addresses set-default-address-url="{{ route('set-default-address') }}" get-area-codes-url="{{ route('get-area-codes') }}" get-addresses-url="{{ route('get-addresses') }}"
            delete-addresses-url="{{ route('delete-addresses') }}" add-address-url="{{ route('add-address') }}"
            init-default-address-id="{{ auth()->user()->default_address_id }}"
            init-default-address-invoice-id="{{ auth()->user()->default_address_invoice_id }}">
        </addresses>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._addresses';
    </script>
@endsection
