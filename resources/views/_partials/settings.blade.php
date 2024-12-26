@section('styles')

    <style>
        .bs-lightbox .gallery {
            column-width: {{ sett('GALLERY_COLUMN_WIDTH') }}px;
            column-gap: 5px;
        }

        .bs-lightbox .gallery>* {
            margin-bottom: {{ sett('GALLERY_ROW_GAP') }}px;
        }

        /* standard */
        .bs-lightbox .standard img {
            object-fit: {{ sett('GALLERY_IMG_FIT') }};
        }

        /* masonry */
        .bs-lightbox .masonry img {
            object-fit: {{ sett('GALLERY_IMG_FIT_MASONRY') }};
        }

        /* carousel */
        .bs-lightbox .carousel img {
            object-fit: {{ sett('CAROUSEL_IMG_FIT') }};
        }
    </style>
@endsection
