import { forEach } from 'lodash';

var ceInlineToolbar = null;
var initEvent = null;

function init() {
    initEvent = new Event("init");
    const config = { attributes: false, childList: true, subtree: true };
    const editorjs = getData('editorjs');
    const callback = (mutationList, observer) => {
        var c = editorjs.getElementsByClassName('ce-inline-toolbar');
        if (c.length) {
            ceInlineToolbar = c[0];
            observer.disconnect();
            document.dispatchEvent(initEvent);
        }
    };
    const observer = new MutationObserver(callback);
    observer.observe(editorjs, config);
}

window.editorjsFixData = {
    editorjs: null,
};

function getData(key) {
    if (window.editorjsFixData[key]) {
        return window.editorjsFixData[key];
    }
    var data = null;
    switch (key) {
        case 'editorjs':
            data = document.getElementById('editorjs');
            break;
    }
    window.editorjsFixData[key] = data;
    return data;
}

function addIconsToToolbar() {
    const config = { attributes: false, childList: true, subtree: false };
    const callback = (mutationList, observer) => {
        var icons = editorjs.querySelectorAll('.icon--bold, .icon--italic');
        _.forEach(icons, function (icon) {
            if (icon.classList.contains('icon--bold')) {
                icon.outerHTML = '<b>B</b>';
            } else {
                icon.outerHTML = '<b><i>i</i></b>';
            }
        });
    };
    const observer = new MutationObserver(callback);
    observer.observe(ceInlineToolbar, config);
}

function createGenericInlineTool() {
    if (!ceInlineToolbar) {
        if (!initEvent) {
            init();
        }
        document.addEventListener("init", (e) => {
            addIconsToToolbar();
        }, false);
    } else {
        addIconsToToolbar();
    }
}

export { createGenericInlineTool };
