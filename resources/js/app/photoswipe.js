import PhotoSwipeLightbox from './photoswipe/photoswipe-lightbox.esm.min'
document.addEventListener("DOMContentLoaded", function (event) {
    const lightbox = new PhotoSwipeLightbox({
        gallery: '#galleryImages',
        children: 'a',
        pswpModule: () => import('./photoswipe/photoswipe.esm.min')
    });
    lightbox.init();
});
