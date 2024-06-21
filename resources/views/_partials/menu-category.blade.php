@php
    $show = isset($parentIds[$category->id]) && $selectedCategory->id !== $category->id ? 'true' : 'false';
    $nesting = isset($nesting) ? $nesting : 0;
    $ml = $nesting * 15 . 'px';
    $children = $category->children->isNotEmpty();
@endphp

<li style="margin-left: {{ $ml }}" class="mt-1">
    <a href="{{ route('category', ['slug' => $category->slug]) }}"
        class="link-dark p-1 d-block _{{ $category->slug }}">{{ $category->name }}
    </a>
    @if ($children)
        @php
            $nesting += 1;
        @endphp
        <span ref="id{{ $category->id }}" @click="$refs['id{{ $category->id }}'].classList.toggle('show')"
            :class="{ 'show': {{ $show }} }">
            <i class="fa-solid fa-caret-right"></i>
        </span>
        <ul>
            @foreach ($category->children as $cat)
                @include('_partials.menu-category', ['category' => $categories->get($cat->id)])
            @endforeach
        </ul>
    @endif
</li>
