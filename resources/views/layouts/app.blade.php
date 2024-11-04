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
    <script>
        window.pageType = '';
        window.maxProductsPrice = {{ $maxProductsPrice ?? 'null' }};
    </script>
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
        <div class="bg-light fixed-top ">
            <nav class="container-xxl d-flex flex-wrap flex-sm-nowrap" id="nav">
                <a class="navbar-brand order-1 p-2 align-self-center" href="{{ url('/') }}">
                    <img src="{{ asset('storage/logo.svg') }}" width="48" height="24"
                        alt="{{ config('app.name', 'Laravel') }}">
                </a>
                <div
                    class="menu-btn d-sm-none order-1 p-2 align-self-center user-select-none flex-grow-1 flex-sm-grow-0">
                    <span class="display-6"><i class="fa-solid fa-bars"></i></span>
                </div>
                <div class="order-1 order-sm-2 align-self-center nav-dropdown">
                    @php
                        $locales = [
                            'pl' => 'Polski',
                            'en' => 'English',
                        ];
                    @endphp
                    <button class="btn btn-light dropdown-toggle ms-2" data-bs-toggle="dropdown">
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
                <div class="order-1 order-sm-2 align-self-center nav-dropdown">
                    <button id="navbarDropdown" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fa-regular fa-user"></i>
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
                            <a class="dropdown-item"
                                href="{{ route('admin-panel-products') }}">{{ __('Admin panel') }}</a>
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
                @php
                    $categoryName = isset($category) ? $category->name : '';
                    $searchUrl = $searchUrl ?? '';
                @endphp
                <search-app @search="searchProducts($event, '{{ $searchUrl }}')"
                    category-name='{{ $categoryName }}'
                    get-suggestions-url="{{ route('get-suggestions') }}"></search-app>
            </nav>
        </div>
        <main id="main" class="clearfix">
            @if (isset($categories))
                <div id="menu" class="bg-light d-none d-sm-block">
                    <div class="position-absolute d-sm-none" style="top: 0;right: 45px;">
                        <div class="menu-btn btn-close position-fixed" style="padding: 15px;" type="button"
                            aria-label="Close">
                        </div>
                    </div>
                    @if (count($categories) > 0)
                        <ul>
                            @foreach ($categories as $category)
                                @if (!$category->parent_id)
                                    @include('_partials.menu-category')
                                @endif
                            @endforeach
                        </ul>
                        @include('_partials.filters')
                    @else
                        No categories
                    @endif
                </div>
            @endif
            <div id="content">
                <div class="mb-2">
                    @php
                        $filters = isset($filters) ? $filters : null;
                    @endphp
                    <search-filters-app @remove-search-value-submitted="searchProducts('')"
                        @remove-max-price-submitted="applyFilters({maxPrice: maxProductsPriceCeil})"
                        @remove-min-price-submitted="applyFilters({minPrice: 0})"
                        @remove-filter-submitted="removeFilterSubmitted" :filters='@json($filters)'
                        v-if='Object.keys(queryStrParamsInitialVals).length'
                        :max-products-price-ceil='maxProductsPriceCeil'
                        :search-value-submitted="queryStrParamsInitialVals.searchValue"
                        :query-str-params-initial-vals="queryStrParamsInitialVals"></search-filters-app>
                </div>
                @yield('content')
            </div>
            <pagination-widget :get-products-view='getProductsView' :current-page='currentPage'
                :geting-products-view='getingProductsView' :last-page='lastPage'></pagination-widget>
        </main>
    </div>
    <footer class="bg-secondary">
        @include('_partials.footer')




        menuss1<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>menuss<br>
    </footer>
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
