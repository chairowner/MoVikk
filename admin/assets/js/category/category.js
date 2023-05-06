const categoryId = parseInt($('input[name="id"]').val());

$('.productImage_close').on('click', function(e){
    e.preventDefault();
    if (confirm(`Подтвердите удаление изображения`)) {
        $.ajax({
            url: 'actions/category/removeImage',
            type: 'POST',
            dataType: 'JSON',
            data: {
                id: categoryId
            },
            success: function(data){
                // console.log(data);
                if (data.status) {
                    location.reload();
                } else {
                    new Message(messageBox, data.info, 'error', 5);
                }
            },
            error: function(error){
                console.log("ERROR", error);
                new Message(messageBox, `Произошла системная ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
            }
        });
    }
});

const colorThief = new ColorThief();

/**
 * Устанавливает задний фон для основного изображения
 * @param {*} JQelement
 * @param {int[]} colors
 */
function setBackgroundColor(JQelement, colors) {
    if (colors.length > 0) {
        JQelement.css('background-color', 'rgb('+colors[0]+','+colors[1]+','+colors[2]+')');
    }
}

$(document).ready(function(){
    const blocks = $('.productImage');
    for (let i = 0; i < blocks.length; i++) {
        const block = $(blocks[i]);
        const image = block.find('img')[0];
        if (image.complete) {
            setBackgroundColor(block, colorThief.getColor(image));
        } else {
            image.addEventListener('load', function() {
                setBackgroundColor(block, colorThief.getColor(image));
            });
        }
    }
});