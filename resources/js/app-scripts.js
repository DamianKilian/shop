function __($key) {
    if (
        typeof window.langJson === 'object' &&
        typeof window.langJson[$key] !== 'undefined'
    ) {
        return window.langJson[$key];
    }
    return $key;
}

function toggleMenu() {
    var menuBtns = document.getElementsByClassName('menu-btn');
    var menuOverlay = document.getElementById('menu-overlay');
    var menu = document.getElementById('menu');
    _.forEach(menuBtns, function (menuBtn, key) {
        menuBtn.addEventListener('click', function () {
            if (menu.classList.contains('d-none')) {
                menu.classList.remove('d-none');
                menuOverlay.classList.remove('d-none');
                document.body.classList.add('overflow-hidden');
            } else {
                menu.classList.add('d-none');
                menuOverlay.classList.add('d-none');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });
}

function localeSwitcher() {
    const setLocaleLinks = document.getElementsByClassName('set-locale-link');
    Array.from(setLocaleLinks).forEach(function (element) {
        element.addEventListener('click', function (e) {
            e.preventDefault();
            setCookie('app_locale', element.dataset.locale, 90);
            location.reload();
        });
    });
}

function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
    let expires = 'expires=' + d.toUTCString();
    document.cookie = cname + '=' + cvalue + ';' + expires + ';path=/';
}

export { __, toggleMenu, localeSwitcher };
