@extends('layouts.admin-panel')

@section('content')
    <div class="container">
        <admin-panel-settings admin-panel-get-settings-url="{{ route('admin-panel-get-settings') }}"
            admin-panel-restore-settings-url="{{ route('admin-panel-restore-settings') }}"
            admin-panel-save-setting-url="{{ route('admin-panel-save-setting') }}"></admin-panel-settings>
    </div>
@endsection

@section('scriptsHead')
    <script>
        window.activeLinks = '._settings';
    </script>
@endsection
