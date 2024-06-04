<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @yield('scriptsHead')
    @php
        $locale = app()->getLocale();
    @endphp
    @if (request()->getDefaultLocale() !== $locale)
        @vite("resources/js/locale/$locale.js")
    @endif
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="shop">
    <div id="app" class="container-xxl">
        <nav class="d-flex flex-wrap flex-sm-nowrap" id="nav">
            <a class="navbar-brand order-1 p-2 align-self-center" href="{{ url('/') }}">
                <img src="{{ asset('storage/logo.svg') }}" width="48" height="24"
                    alt="{{ config('app.name', 'Laravel') }}">
            </a>
            <div class="menu-btn d-sm-none order-1 p-2 align-self-center user-select-none flex-grow-1 flex-sm-grow-0">
                <span>{{ __('Menu') }}</span>
            </div>
            <div class="order-1 order-sm-2 ms-auto align-self-center">
                @php
                    $locales = [
                        'pl' => 'Polski',
                        'en' => 'English',
                    ];
                @endphp
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle ms-2" data-bs-toggle="dropdown">
                        {{ $locales[app()->getLocale()] }}
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
            <div class="order-1 order-sm-2 align-self-center">
                <button id="navbarDropdown" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    @guest {{ __('Guest') }}
                    @else
                    {{ Auth::user()->name }} @endguest
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    @guest
                        @if (Route::has('login'))
                            <li><a class="dropdown-item" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                        @endif

                        @if (Route::has('register'))
                            <li><a class="dropdown-item" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                        @endif
                    @else
                        <a class="dropdown-item" href="{{ route('admin-panel-products') }}">{{ __('Admin panel') }}</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endguest
                </div>
            </div>
            <form class="order-1 pt-2 pb-2 flex-grow-1 d-flex justify-content-center" role="search"
                style="min-width: 250px" id="search">
                <div class="d-flex align-items-center search-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <input v-model='searchValue' class="form-control" type="search" placeholder="{{ __('Search') }}"
                    aria-label="Search">
                <button v-show='searchValue' @click='searchValue = ""' ref="clear" type="button"
                    class="btn btn-danger ms-1 d-none"><i class="fa-solid fa-xmark"></i></button>
            </form>
        </nav>
        <main id="main" class="d-flex align-items-stretch">
            <div id="menu" class="p-2 d-none d-sm-block">
                menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>
            </div>
            <div id="content" class="flex-grow-1">
                @yield('content')
                menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>
            </div>
        </main>
    </div>
    <div id="menu-overlay" class="overlay d-none menu-btn"></div>
    <script type="module">
        toggleMenu();
        localeSwitcher();
    </script>
    @yield('scripts')
</body>

</html>
