<div class="order-1 order-sm-2 align-self-center nav-dropdown">
    <button id="navbarDropdown" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fa-regular fa-user"></i>
        <span class="d-none d-sm-inline">
            @guest {{ __('Guest') }}
            @else
            {{ Auth::user()->name }} @endguest
        </span>
    </button>
    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        @guest
            @if (Route::has('login'))
                <li><a class="dropdown-item" href="{{ route('login') }}">{{ __('Login') }}</a></li>
            @endif
            @if (Route::has('register'))
                <li><a class="dropdown-item" href="{{ route('register') }}">{{ __('Register') }}</a></li>
            @endif
            <li><a class="dropdown-item" id="open_preferences_center">{{ __('Cookies') }}</a></li>
        @else
            @can('admin')
                <a class="dropdown-item" href="{{ route('admin-panel-products') }}">{{ __('Admin panel') }}</a>
            @endcan
            <a class="dropdown-item" href="{{ route('addresses') }}">{{ __('Account') }}</a>
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
