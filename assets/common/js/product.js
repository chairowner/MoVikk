const msgBox = $('#mainMessageBox');
const
cartCounterId ='js-cartCounter', cartCounter ='#'+cartCounterId,
actionBtnClass ='js-cardAction', actionBtn ='.'+actionBtnClass,
actionAdd = 'add', actionRemove = 'remove';
const
buttonWrapper = $('.addCart__button'),
addToCartButton = $("<button>",{class:"button w-100 "+actionBtnClass,'data-action':actionAdd}).append($('<span>',{text:'Добавить в корзину'})),
cartCounterDiv = $('<div>', {id:cartCounterId})
    .append($('<input>',{type:'button',value:'-',class:"button counter minus "+actionBtnClass,'data-action':actionRemove}))
    .append($('<span>',{id:'js-counter', text:0}))
    .append($('<input>',{type:'button',value:'+',class:"button counter plus "+actionBtnClass,'data-action':actionAdd})),
productId = parseInt($('[data-product-id]').attr('data-product-id'));

function updateCounter(count = null) {
    if (count !== null) {
        $(cartCounter).find('#js-counter').text(parseInt(count));
    }
}

function updateButton(data = null) {
    if (data == null) {
        $.ajax({
            url:'/actions/cart/checkProduct',
            type:'GET',
            dataType:'JSON',
            data:{id:productId},
            async: false,
            success: function(data){
                if (data.status) {
                    buttonWrapper.html(cartCounterDiv);
                    const elem = $(cartCounter);
                    elem.find('#js-counter').text(data.count);
                } else {
                    buttonWrapper.html(addToCartButton);
                }
            },
            error: function(err){
                console.log('ERROR');
                console.log(err);
                buttonWrapper.html(addToCartButton);
            }
        });
    } else {
        if (data.count != null) {
            if (data.count > 0) {
                buttonWrapper.html(cartCounterDiv);
                const elem = $(cartCounter);
                elem.find('#js-counter').text(data.count);
            } else {
                buttonWrapper.html(addToCartButton);
            }
        }
    }
}

updateButton();

$(document).on('click',actionBtn,function(){
    const btn = $(this);
    const btns = $(actionBtn);
    const action = btn.attr('data-action');
    const oldInner = btns.html();
    showLoad(btns, null, false);
    $.ajax({
        url:'/actions/cart/' + action,
        type:'POST',
        dataType:'JSON',
        data:{id:productId},
        success: function(data){
            let msgStatus = 'error';
            if (data.status) {
                updateCartCount();
                msgStatus = 'success';
                if (data.count > 1) {
                    if ($(cartCounter).length == 0) updateButton(data);
                    updateCounter(data.count);
                } else {
                    updateButton(data);
                }
            }
            if (data.info != null) new Message(msgBox, data.info, msgStatus, 5);
            removeLoad(btns, oldInner);
        },
        error: function(err){
            console.log('ERROR');
            console.log(err);
            let msg = '';
            if (action == 'add') msg += `При добавлении товара в корзину`;
            else if (action == 'remove') msg += `При удалении товара из корзину`;
            msg += ` произошла ошибка\nПожалуйста, обновите страницу и повторите действие`;
            new Message(msgBox, msg, 'error', 5);
            removeLoad(btns, oldInner);
        }
    });
});

Fancybox.bind("[data-fancybox=images]", {
    l10n: {
        CLOSE: "Закрыть",
        NEXT: "Следующее изображение",
        PREV: "Предыдущее изображение",
        MODAL: "Вы можете закрыть это модальное содержимое с помощью клавиши ESC",
        ERROR: "Что-то пошло не так. Пожалуйста, повторите попытку позже",
        IMAGE_ERROR: "Изображение не найдено",
        ELEMENT_NOT_FOUND: "HTML-элемент не найден",
        AJAX_NOT_FOUND: "Ошибка при загрузке AJAX: Не найдено",
        AJAX_FORBIDDEN: "Ошибка при загрузке AJAX: Запрещено",
        IFRAME_ERROR: "Ошибка загрузки страницы",
    },
});