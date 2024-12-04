function setCarouselsHeights(carousels) {
    _.forEach(carousels, function (carousel) {
        var imgs = carousel.querySelectorAll('.carousel-item img');
        _.forEach(imgs, function (img) {
            img.style.height = 'auto';
        });
        var carouselItems = carousel.querySelectorAll('.carousel-item');
        var imgActive = carousel.querySelector('.carousel-item.active img');
        var height = imgActive.width / imgs[0].width * imgs[0].height;
        _.forEach(carouselItems, function (carouselItem) {
            carouselItem.style.height = height + 'px';
        });
        _.forEach(imgs, function (img) {
            img.style.height = height + 'px';
        });
    });
}

document.addEventListener("DOMContentLoaded", function (event) {
    var carousels = document.querySelectorAll('.bs-lightbox .carousel');
    if (!carousels.length) {
        return;
    }
    setCarouselsHeights(carousels);
    window.addEventListener("resize", _.debounce(function () {
        setCarouselsHeights(carousels);
    }, 500));
});
