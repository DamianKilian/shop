@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-pages admin-panel-get-pages-url="{{ route('admin-panel-get-pages') }}"
            admin-panel-get-page-url="{{ route('admin-panel-get-page') }}"
            admin-panel-add-page-url="{{ route('admin-panel-add-page') }}"
            admin-panel-apply-changes-url="{{ route('admin-panel-apply-changes') }}"
            admin-panel-toggle-active-url="{{ route('admin-panel-toggle-active') }}"
            admin-panel-delete-page-url="{{ route('admin-panel-delete-page') }}"
            admin-panel-upload-attachment-url="{{ route('admin-panel-upload-attachment') }}"
            admin-panel-fetch-url-url="{{ route('admin-panel-fetch-url') }}"
            admin-panel-upload-file-url="{{ route('admin-panel-upload-file') }}"></admin-panel-pages>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._pages';
    </script>
@endsection
