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
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div id="app" class="container-xxl">
        <nav class="d-flex flex-wrap flex-sm-nowrap" id="nav">
            <div class="order-1 p-2 align-self-center">
                <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
            </div>
            <div id="menu-btn" class="order-1 p-2 align-self-center user-select-none" role="button">
                <span>{{ __('Menu') }}</span>
            </div>
            <div class="order-1 order-sm-2 ms-auto align-self-center">
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
        </nav>
        <main id="main" class="d-flex align-items-stretch">
            <div id="menu" class="p-2">
                <nav class="nav flex-column">
                    <a class="nav-link _products" aria-current="page"
                        href="{{ route('admin-panel-products') }}">Products</a>
                    <a class="nav-link _categories" href="{{ route('admin-panel-categories') }}">Categories</a>
                </nav>
            </div>
            <div id="content" class="flex-grow-1 pb-4">
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        if (window.activeLink) {
            var link = document.querySelector('#menu .' + window.activeLink);
            link.classList.add('active');
        }
    </script>
    <script type="module">
        var menuBtn = document.getElementById('menu-btn');
        var menu = document.getElementById('menu');
        menuBtn.addEventListener('click', function() {
            menu.classList.toggle('d-none');
        });
    </script>
    @yield('scripts')
</body>

</html>
