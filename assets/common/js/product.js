const
actionBtnClass ='js-cardAction', actionBtn ='.'+actionBtnClass,
actionAdd = 'add', actionRemove = 'remove';
const productId = parseInt($('[data-product-id]').attr('data-product-id'));

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