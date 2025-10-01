@extends('layouts.account')

@section('content')
    <div class="container">
        <password  update-password-url="{{ route('update-password') }}">
        </password>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._password';
    </script>
@endsection
