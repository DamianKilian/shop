<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ config('app.name') }}</a></li>
        @foreach ($category->categories as $category)
            @if ($loop->last && 'category' === $_type)
                <li class="breadcrumb-item">
                    <a>{{ $category->name }}</a>
                </li>
            @else
                <li class="breadcrumb-item">
                    <a href="{{ route('category', [$category->slug]) }}">{{ $category->name }}</a>
                </li>
            @endif
        @endforeach
        @if ('product' === $_type)
            <li class="breadcrumb-item">
                <a>{{ $product->title }}</a>
            </li>
        @endif
    </ol>
</nav>
