@if (0 === $nesting)
    <li>
        <b><a href="{{ route('category', ['slug' => $category->slug]) }}"
                class="link-dark p-1 d-block">{{ $category->name }}</a></b>
    </li>
@else
    @php
        $ml = $nesting * 15 . 'px';
    @endphp
    <li style="margin-left: {{ $ml }}">
        <a href="{{ route('category', ['slug' => $category->slug]) }}"
            class="link-dark p-1 d-block">{{ $category->name }}</a>
    </li>
@endif
@if (count($category->children) > 0)
    @php
        $nesting += 1;
    @endphp
    <ul>
        @foreach ($category->children as $category)
            @include('_partials.menu-category', $category)
        @endforeach
    </ul>
@endif
