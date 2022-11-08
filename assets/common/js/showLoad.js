const loading_tag = 'mini-loader';
const loading = $('<img>', {class:loading_tag + " fill-white",src:'/assets/icons/spinner.svg',alt:'Загрузка...'});
function showLoad(elem, newInner = null, loader = true) {
    if (loader) newInner = loading;
    elem.attr('disabled', true);
    if (newInner != null) {
        elem.addClass('position-relative');
        elem.html(newInner)
    };
}
function removeLoad(elem, oldInner = null) {
    elem.removeAttr('disabled');
    elem.find('.loading_tag').remove();
    if (oldInner != null) elem.html(oldInner);
}