<div id="product-photos-gallery">
    <div id="galleryImages" class="pswp-gallery pswp-gallery--single-column">
        @foreach ($product->productImages as $image)
            <a href="{{ asset('storage/' . $image->url) }}" data-pswp-width="{{ $image->data()->width }}"
                data-pswp-height="{{ $image->data()->height }}" target="_blank">
                <img src="{{ asset('storage/' . $image->url_thumbnail) }}" alt="{{ $product->title }}" />
            </a>
        @endforeach
    </div>
</div>
