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
    @include('_partials.scripts-head')
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
    @yield('styles')
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
                <div class="order-1 order-sm-2 align-self-center nav-btn">
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
                @php
                    $categoryName = isset($category) ? $category->name : '';
                    $searchUrl = $searchUrl ?? '';
                @endphp
                <search-app @search="searchProducts($event, '{{ $searchUrl }}')"
                    category-name='{{ $categoryName }}'
                    get-suggestions-url="{{ route('get-suggestions') }}"></search-app>
            </nav>
        </div>
        <div id="main" class="clearfix">
            @if (isset($categories))
                <nav id="menu" class="bg-light d-none d-sm-block">
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
                </nav>
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
        </div>
    </div>
    <footer class="bg-secondary">
        @include('_partials.footer')
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
