const mainMessageBox = $('#mainMessageBox');
const lkLink ='.header-nav__icons .item .item__data[href="/admin"]';
const lk = $(lkLink);
const menu = $('.lk-menu');
lk.on('click',function(e){
    e.preventDefault();
    const openClass = 'open';
    if (!menu.hasClass(openClass)) {
        $('.header-nav__icons .item').find('.item__data[href="/admin"]').addClass('hover');
        menu.css('display','flex');
        menu.addClass(openClass);
    } else {
        $('.header-nav__icons .item').find('.item__data[href="/admin"]').removeClass('hover');
        menu.removeClass(openClass);
        setTimeout(() => {menu.css('display','none');}, 300);
    }
});
$(document).on('mouseup',function(e){
    const item = $(e.target);
    const openClass = 'open';
    if (item.closest(lkLink).length === 0 && menu.hasClass(openClass)) {
        menu.removeClass(openClass);
        $('.header-nav__icons .item').find('.item__data[href="/admin"]').removeClass('hover');
        setTimeout(() => {menu.css('display','none');}, 300);
    }
});
menu.find('.lk-menu-exit').on('click',function(e){
    e.preventDefault();
    $.ajax({
        url: '/actions/lk/logout',
        type: 'GET',
        success: function () {
            location.href = "/";
        },
        error: function () {
            new Message(mainMessageBox, `Ошибка: не удалось выйти из аккаунта`, 'error', 5);
            setTimeout(() => {location.reload();}, 3000);
        }
    });
});

$(document).ready(function() {
    let clipboard = new ClipboardJS('.js-copy',{
        text: function(trigger) {
            return trigger.text;
        }
    });
    clipboard.on('success', function(e) {
        let message = e.trigger.getAttribute('data-copy-success');
        if (message == null) message = "Скопировано";
        new Message(mainMessageBox, message, 'success');
        e.clearSelection();
    });
    clipboard.on('error', function(e) {
        let message = e.trigger.getAttribute('data-copy-error');
        if (message == null) message = "Не удалось скопировать";
        new Message(mainMessageBox, message, 'error');
    });
});