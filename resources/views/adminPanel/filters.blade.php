@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-filters admin-panel-get-filters-url="{{ route('admin-panel-get-filters') }}"
            admin-panel-add-filter-url="{{ route('admin-panel-add-filter') }}"
            admin-panel-delete-filters-url="{{ route('admin-panel-delete-filters') }}"></admin-panel-filters>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._filters';
    </script>
@endsection
