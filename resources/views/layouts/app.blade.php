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
            <div class="order-1 p-2 align-self-center">
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
            </div>
            <div id="menu-btn" class="order-1 p-2 align-self-center user-select-none flex-grow-1 flex-sm-grow-0">
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
                    <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
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
                <a id="navbarDropdown" class="nav-link dropdown-toggle p-2" href="#" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    @guest {{ __('Guest') }}
                    @else
                    {{ Auth::user()->name }} @endguest
                </a>
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
            <form class="order-1 p-2 flex-grow-1 d-flex" role="search" style="min-width: 250px" id="search">
                <input class="form-control me-2" type="search" placeholder="{{ __('Search') }}" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">{{ __('Search') }}</button>
            </form>
        </nav>
        <main id="main" class="d-flex align-items-stretch">
            <div id="menu" class="d-none p-2">
                menuss<br>
            </div>
            <div id="content" class="flex-grow-1 pb-4">
                @yield('content')
            </div>
        </main>
    </div>
    <script type="module">
        toggleMenu();
        localeSwitcher();
    </script>
    @yield('scripts')
</body>

</html>
