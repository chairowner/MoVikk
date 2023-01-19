const loading_tag = 'mini-loader';
const loading = $('<img>', {class:loading_tag,src:'/assets/icons/spinner.svg',alt:'Загрузка...'});
function showLoad(elem, newInner = null, loader = true, loadingColor = 'white', elemCenter = false) {
    if (loader) {
        if (elemCenter) {
            elem.addClass('showLoad-center')
        }
        loading.addClass("fill-"+loadingColor);
        newInner = loading;
    }
    elem.attr('disabled', true);
    if (newInner != null) {
        elem.addClass('position-relative');
        elem.html(newInner)
    };
}
function removeLoad(elem, oldInner = null) {
    elem.removeClass('showLoad-center');
    elem.removeAttr('disabled');
    elem.find('.loading_tag').remove();
    if (oldInner != null) elem.html(oldInner);
}