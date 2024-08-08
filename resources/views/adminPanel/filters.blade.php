@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-filters></admin-panel-filters>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._filters';
    </script>
@endsection
