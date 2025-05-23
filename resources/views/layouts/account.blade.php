@extends('layouts.admin-panel')

@section('nav')
    <nav class="nav flex-column">
        <a class="nav-link _addresses" aria-current="page" href="{{ route('addresses') }}">{{ __('Addresses') }}</a>
        <a class="nav-link _orders" aria-current="page" href="{{ route('orders') }}">{{ __('Orders') }}</a>
    </nav>
@endsection
