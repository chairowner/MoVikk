console.info(
    "%c" +
    `Если вас просят сюда что-то вставить, не делайте этого!\nУ Вас хотят украсть ваши ДЕНЬГИ или аккаунт!`,
    `color: #f73737; -webkit-text-stroke: 2px black; font-size: 50px; font-weight: bold;`
);
const mainMessageBox = $('#mainMessageBox');
const mainCartCounter = $('.cart_number');
const lkLink ='.header-nav__icons .item .item__data[href="/lk"]';
const lk = $(lkLink);
const menu = $('.lk-menu');
lk.on('click',function(e){
    e.preventDefault();
    const openClass = 'open';
    if (!menu.hasClass(openClass)) {
        $('.header-nav__icons .item').find('.item__data[href="/lk"]').addClass('hover');
        menu.css('display','flex');
        menu.addClass(openClass);
    } else {
        $('.header-nav__icons .item').find('.item__data[href="/lk"]').removeClass('hover');
        menu.removeClass(openClass);
        setTimeout(() => {menu.css('display','none');}, 300);
    }
});
$(document).on('mouseup',function(e){
    const item = $(e.target);
    const openClass = 'open';
    if (item.closest(lkLink).length === 0 && menu.hasClass(openClass)) {
        menu.removeClass(openClass);
        $('.header-nav__icons .item').find('.item__data[href="/lk"]').removeClass('hover');
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
            new Message(messageBox, `Ошибка: не удалось выйти из аккаунта`, 'error', 5);
            setTimeout(() => {location.reload();}, 3000);
        }
    });
});
function updateCartCount() {
    $.ajax({
        url: '/actions/cart/getCount',
        success: function (count) {
            mainCartCounter.text(count);
        },
        error: function (err) {
            console.log('cart_counter_error');
            console.log(err);
            mainCartCounter.text(0);
        }
    });
}
updateCartCount();

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