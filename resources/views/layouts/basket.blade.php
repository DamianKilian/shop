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

<body class="">
    <div id="app" class="container-xxl">
        <div class="bg-light fixed-top ">
            <nav class="container-xxl d-flex flex-wrap flex-sm-nowrap" id="nav">
                <a class="navbar-brand order-1 p-2 align-self-center" href="{{ url('/') }}">
                    <img src="/logo.svg" width="48" height="24"
                        alt="{{ config('app.name', 'Laravel') }}">
                </a>
                <div class="menu-btn d-sm-none order-1 p-2 align-self-center user-select-none flex-grow-1">
                    <span class="display-6"><i class="fa-solid fa-bars"></i></span>
                </div>
                <div class="order-1 order-sm-2 align-self-center ms-auto nav-btn">
                    <a class="btn btn-light ms-2 position-relative" href="{{ route('basket-index') }}">
                        <i class="fa-solid fa-basket-shopping"></i>
                        <num-badge :products-in-basket='productsInBasket'></num-badge>
                    </a>
                </div>
                <div class="order-1 order-sm-2 align-self-center nav-dropdown nav-btn">
                    @php
                        $locales = [
                            'pl' => 'Pl',
                            'en' => 'En',
                        ];
                    @endphp
                    <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-language"></i> <b>{{ $locales[app()->getLocale()] }}</b>
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
                @include('_partials.layouts.nav-dropdown')
            </nav>
        </div>
        <div id="main" class="clearfix">
            <div id="basket-content">
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
    </script>
    @yield('scripts')
</body>

</html>
