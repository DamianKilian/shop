@extends('layouts.app')

@section('content')
    {{ $category->name }}
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = "{{ $activeLinks }}";
    </script>
@endsection
