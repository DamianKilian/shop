@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-products :categories-prop='@json($categories)'
            admin-panel-save-categories-url="{{ route('admin-panel-save-categories') }}">
        </admin-panel-products>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLink = '_products';
    </script>
@endsection
