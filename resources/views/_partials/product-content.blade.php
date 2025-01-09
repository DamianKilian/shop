<div id="page-content">
    <h1>{!! $product->title !!}</h1>
    @include('_partials.product-photos-gallery')
    {!! $product->bodyHtml !!}
</div>
