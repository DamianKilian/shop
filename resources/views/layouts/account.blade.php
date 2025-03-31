@extends('layouts.admin-panel')

@section('nav')
    <nav class="nav flex-column">
        <a class="nav-link _addresses" aria-current="page" href="{{ route('addresses') }}">{{ __('Addresses') }}</a>
    </nav>
@endsection
