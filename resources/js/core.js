function meta(name) {
    return document.head.querySelector("[name=" + name + "]") ? document.head.querySelector("[name=" + name + "]").content : null;
}
window.meta = meta;
