@extends('layouts.account')

@section('content')
    <div class="container">
        <user update-user-url="{{ route('update-user') }}", user-name="{{ auth()->user()->name }}",
            user-email={{ auth()->user()->email }}>
        </user>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._user';
    </script>
@endsection
