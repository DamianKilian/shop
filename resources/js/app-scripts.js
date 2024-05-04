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
    var menuBtn = document.getElementById('menu-btn');
    var menu = document.getElementById('menu');
    menuBtn.addEventListener('click', function () {
        menu.classList.toggle('d-none');
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
