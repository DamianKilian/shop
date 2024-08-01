<div id="products" data-last-page="{{ $products->lastPage() }}">
    @foreach ($products as $product)
        @if ($product->categories)
            <div class="product-breadcrumb">
                @foreach ($product->categories as $category)
                    @if (!$loop->first)
                        <i class="fa-solid fa-angle-right" style="position: relative;top: 2px;"></i>
                    @endif
                    <a class="badge bg-secondary"
                        href="{{ route('category', ['slug' => $category->slug]) }}">{{ $category->name }}</a>
                @endforeach
            </div>
        @endif
        <a href="#" class="product clearfix">
            <div class="img-container">
                @if ($product->productPhotos->get(0))
                    <img src="{{ asset('storage/' . $product->productPhotos->get(0)->url_small) }}"
                        alt="{{ $product->title }}">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
                        <rect width="400" height="400" fill="#cccccc"></rect>
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace"
                            font-size="26px" fill="#333333">{{ __('No image') }}</text>
                    </svg>
                @endif
            </div>
            <div class="title-desc">
                <article>
                    <h2>{{ $product->title }}</h2>
                    <p>{{ $product->descStr }}</p>
                </article>
            </div>
            <div class="p-2 price">
                <div class="fs-1 number">{{ str_replace('.', ',', $product->price) }}&nbsp;zł</div>
            </div>
        </a>
    @endforeach
    @if (!$products->total())
        <div class="alert alert-primary text-center" role="alert">
            {{ __('Products not found') }}
        </div>
    @endif
</div>
{{ $products->links() }}
