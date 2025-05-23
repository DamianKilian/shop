<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @include('_partials.scripts-head')
    @yield('scriptsHead')
    @php
        $locale = app()->getLocale();
    @endphp
    @if (request()->getDefaultLocale() !== $locale)
        @vite("resources/js/locale/$locale.js")
    @endif
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('styles')
</head>

<body>
    <div id="app" class="container-xxl">
        <div class="bg-light fixed-top ">
            <nav class="container-xxl d-flex flex-wrap flex-sm-nowrap" id="nav">
                <a class="navbar-brand order-1 p-2 align-self-center" href="{{ url('/') }}">
                    <img src="{{ asset('storage/logo.svg') }}" width="48" height="24"
                        alt="{{ config('app.name', 'Laravel') }}">
                </a>
                <div class="menu-btn d-sm-none order-1 p-2 align-self-center user-select-none flex-grow-1">
                    <span class="display-6"><i class="fa-solid fa-bars"></i></span>
                </div>
                <div class="order-1 order-sm-2 ms-auto align-self-center nav-dropdown nav-btn">
                    @php
                        $locales = [
                            'pl' => 'Pl',
                            'en' => 'En',
                        ];
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-language"></i> {{ $locales[app()->getLocale()] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($locales as $locale => $lang)
                                <li>
                                    <a class="dropdown-item set-locale-link"
                                        data-locale='{{ $locale }}'>{{ $lang }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @include('_partials.layouts.nav-dropdown')
            </nav>
        </div>
        <div id="main" class="clearfix">
            <nav id="menu" class="bg-light d-none d-sm-block">
                <div class="position-absolute d-sm-none" style="top: 0;right: 45px;">
                    <div class="menu-btn btn-close position-fixed" style="padding: 15px;" type="button"
                        aria-label="Close">
                    </div>
                </div>
                @section('nav')
                    @can('admin')
                        <nav class="nav flex-column">
                            <a class="nav-link _categories"
                                href="{{ route('admin-panel-categories') }}">{{ __('Categories') }}</a>
                            <a class="nav-link _delivery-methods"
                                href="{{ route('admin-panel-delivery-methods') }}">{{ __('Delivery methods') }}</a>
                            <a class="nav-link _filters" href="{{ route('admin-panel-filters') }}">{{ __('Filters') }}</a>
                            <a class="nav-link _footer" href="{{ route('admin-panel-footer') }}">{{ __('Footer') }}</a>
                            <a class="nav-link _orders" href="{{ route('admin-panel-orders') }}">{{ __('Orders') }}</a>
                            <a class="nav-link _pages" href="{{ route('admin-panel-pages') }}">{{ __('Pages') }}</a>
                            <a class="nav-link _products" aria-current="page"
                                href="{{ route('admin-panel-products') }}">{{ __('Products') }}</a>
                            <a class="nav-link _settings" href="{{ route('admin-panel-settings') }}">{{ __('Settings') }}</a>
                            @can('usersManagement')
                                <a class="nav-link _users" href="{{ route('admin-panel-users') }}">{{ __('Users') }}</a>
                            @endcan
                        </nav>
                    @endcan
                @show
            </nav>
            <div id="content">
                @yield('content')
            </div>
        </div>
    </div>
    <div id="menu-overlay" class="overlay d-none d-sm-none menu-btn"></div>
    <script type="module">
        toggleMenu();
        localeSwitcher();
    </script>
    <script>
        function hideNavBarOnPageLoading() {
            document.getElementById("nav").style.opacity = "0";
            window.addEventListener("load", () => {
                document.getElementById("nav").style.opacity = "1";
            });
        }
        hideNavBarOnPageLoading();

        function selectActiveLinks() {
            if (!window.activeLinks) {
                return;
            }
            var links = Array.from(document.querySelectorAll(window.activeLinks));
            links.forEach(link => {
                link.classList.add('active');
            });
        }
        selectActiveLinks();
    </script>
    @yield('scripts')
</body>

</html>
