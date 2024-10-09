@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-pages admin-panel-get-pages-url="{{ route('admin-panel-get-pages') }}"
            admin-panel-get-page-url="{{ route('admin-panel-get-page') }}"
            admin-panel-add-page-url="{{ route('admin-panel-add-page') }}"
            admin-panel-delete-pages-url="{{ route('admin-panel-delete-pages') }}"></admin-panel-pages>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._pages';
    </script>
@endsection
