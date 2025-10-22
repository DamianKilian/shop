<div id="page-content">
    <div id="h1-container">
        <h1>{!! $product->title !!}</h1>
        <div id="breadcrumb-container">
            @include('_partials.breadcrumb', ['_type' => 'product'])
        </div>
    </div>
    @include('_partials.product-photos-gallery')
    {!! $product->bodyHtml !!}
</div>
