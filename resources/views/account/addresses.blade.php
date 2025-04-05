@extends('layouts.account')

@section('content')
    <div class="container">
        <addresses get-area-codes-url="{{ route('get-area-codes') }}" get-addresses-url="{{ route('get-addresses') }}"
            delete-addresses-url="{{ route('delete-addresses') }}" add-address-url="{{ route('add-address') }}">
        </addresses>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._addresses';
    </script>
@endsection
