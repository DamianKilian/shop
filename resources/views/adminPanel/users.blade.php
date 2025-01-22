@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-users user-id="{{ auth()->user()->id }}" admin-panel-get-users-url="{{ route('admin-panel-get-users') }}"
            admin-panel-search-users-url="{{ route('admin-panel-search-users') }}"
            admin-panel-set-admin-url="{{ route('admin-panel-set-admin') }}"></admin-panel-users>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._users';
    </script>
@endsection
