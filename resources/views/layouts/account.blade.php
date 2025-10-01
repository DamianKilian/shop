@extends('layouts.admin-panel')

@section('nav')
    <nav class="nav flex-column">
        <a class="nav-link _addresses"  href="{{ route('addresses') }}">{{ __('Addresses') }}</a>
        <a class="nav-link _orders"  href="{{ route('orders') }}">{{ __('Orders') }}</a>
        <a class="nav-link _user"  href="{{ route('user') }}">{{ __('User') }}</a>
        <a class="nav-link _password"  href="{{ route('password') }}">{{ __('Change password') }}</a>
    </nav>
@endsection
