@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-footer admin-panel-get-footer-url="{{ route('admin-panel-get-footer') }}"
            admin-panel-save-footer-url="{{ route('admin-panel-save-footer') }}"></admin-panel-settings>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._footer';
    </script>
@endsection
