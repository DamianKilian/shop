@php
    $show = isset($parentIds[$category->id]) && $selectedCategory->id !== $category->id ? 'true' : 'false';
    $nesting = isset($nesting) ? $nesting : 0;
    $ml = $nesting * 15 . 'px';
    $children = $category->children->isNotEmpty();
    $firstLi = !$category->parent_id ? 'first-li' : '';
@endphp

<li style="margin-left: {{ $ml }}" class="mt-1 {{ $firstLi }}">
    <a href="{{ route('category', ['slug' => $category->slug]) }}" class="p-1 d-block _{{ $category->slug }}"
        data-nesting='{{ $nesting + 1 }}'>
        <span v-if="productNums['{{ $category->slug }}']" v-html="productNums['{{ $category->slug }}'] "
            class="badge text-bg-primary"></span>
        {{ $category->name }}
    </a>
    @if ($children)
        @php
            $nesting += 1;
        @endphp
        <span ref="id{{ $category->id }}" @click="$refs['id{{ $category->id }}'].classList.toggle('show')"
            :class="{ 'show': {{ $show }} }" class="show-toggle nesting-{{ $nesting }}">
            <i class="fa-solid fa-caret-right"></i>
        </span>
        <ul>
            @foreach ($category->children as $cat)
                @include('_partials.menu-category', ['category' => $categories->get($cat->id)])
            @endforeach
        </ul>
    @endif
</li>
