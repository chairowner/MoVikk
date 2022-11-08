function overlayAct(action, overlay = $('#overlay'), timeout = 0.3) {
    if (action === 'open') {
        overlay.addClass('active');
        overlay.css('display','flex');
        $('body').addClass('overflow-hidden');
        setTimeout(() => {
            overlay.css('opacity',1);
        }, timeout * 1000);
    } else if (action === 'close') {
        overlay.removeClass('active');
        overlay.css('opacity',0);
        $('body').removeClass('overflow-hidden');
        setTimeout(() => {
            overlay.css('display','none');
        }, timeout * 1000);
    }
}