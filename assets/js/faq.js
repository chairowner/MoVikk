
for (let i = 0; i < $('.question.open').length; i++) {
    const item = $($('.question.open').get(i));
    item.find('.answer').css({
        padding: '20px',
        height: (item.find('.answer span').height() * 3) + 'px',
        opacity: 1
    });
    console.log(item.find('.answer span').css('height'));
}

$('.question').on('click', function(){
    const item = $(this);
    let params = {};
    item.toggleClass('open');
    if (item.hasClass('open')) {
        params = {
            padding: '20px',
            height: (item.find('.answer span').height() * 3) + 'px',
            opacity: 1
        };
    } else {
        params = {
            padding: 0,
            height: 0,
            opacity: 0
        };
    }
    item.find('.answer').css(params);
});