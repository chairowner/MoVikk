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
                console.log('ERROR',err);
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

if ($('.addCart__button').length > 0) {
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
                if (data.info != null) new Message(mainMessageBox, data.info, msgStatus, 5);
                removeLoad(btns, oldInner);
            },
            error: function(err){
                console.log('ERROR',err);
                let msg = '';
                if (action == 'add') msg += `При добавлении товара в корзину`;
                else if (action == 'remove') msg += `При удалении товара из корзину`;
                msg += ` произошла ошибка\nПожалуйста, обновите страницу и повторите действие`;
                new Message(mainMessageBox, msg, 'error', 5);
                removeLoad(btns, oldInner);
            }
        });
    });
}

$('.js-gallery').magnificPopup({
    tClose: 'Закрыть',
    tLoading: 'Загрузка...',
    type: 'image',
    gallery: {
        enabled: true,
        preload: [0,2],
        navigateByImgClick: true,
        arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"></button>',
        tPrev: 'Предыдущее изображение',
        tNext: 'Следующее изображение',
        tCounter: '<span class="mfp-counter">%curr% из %total%</span>'
    },
    image: {
        titleSrc: 'title',
        tError: 'Не удаётся загрузить <a href="%url%">изображение</a>'
    }
});

// const colorThief = new ColorThief();

// /**
//  * Устанавливает задний фон для основного изображения
//  * @param {*} JQelement
//  * @param {int[]} colors
//  */
// function setBackgroundColor(JQelement, colors) {
//     if (colors.length > 0) {
//         JQelement.css('background-color', 'rgb('+colors[0]+','+colors[1]+','+colors[2]+')');
//     }
// }

// $(document).ready(function(){
//     const mainImageRole = document.getElementById('js-mainImageRole');
//     const mainImageRoleBox = $('#js-mainImageRole-box');
//     if ($(mainImageRole).attr('data-founded') === "true") {
//         if (mainImageRole.complete) {
//             setBackgroundColor(mainImageRoleBox, colorThief.getColor(mainImageRole));
//         } else {
//             image.addEventListener('load', function() {
//                 setBackgroundColor(mainImageRoleBox, colorThief.getColor(mainImageRole));
//             });
//         }
//     }
// });