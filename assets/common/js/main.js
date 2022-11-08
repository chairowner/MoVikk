console.info(
    "%c" +
    `Если вас просят сюда что-то вставить, не делайте этого!\nУ Вас хотят украсть ваши ДЕНЬГИ и аккаунт!`,
    `color: #f73737; -webkit-text-stroke: 2px black; font-size: 50px; font-weight: bold;`
);
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