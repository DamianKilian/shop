@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-categories :categories-prop='@json($categories)' :filters='@json($filters)'
            admin-panel-save-categories-url="{{ route('admin-panel-save-categories') }}">
        </admin-panel-categories>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._categories';
    </script>
@endsection
