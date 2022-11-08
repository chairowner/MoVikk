$('.feature__img').hover(function(){
    const obj = $(this);
    obj.find('img').addClass('hover');
},function(){
    const obj = $(this);
    setTimeout(() => {
        if (!obj.is(":hover")) obj.find('img').removeClass('hover');
    }, 1.5 * 1000);
});