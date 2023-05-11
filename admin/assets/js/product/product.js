const productId = parseInt($('input[name="id"]').val());

$('.productImage_setMain').on('click', function(e){
    e.preventDefault();
    const imageId = parseInt($(this).attr('data-imageId'));
    if (confirm(`Подтвердите замену основного изображения`)) {
        $.ajax({
            url: 'actions/product/setMainImage',
            type: 'POST',
            dataType: 'JSON',
            data: {
                productId: productId,
                imageId: imageId
            },
            success: function(data){
                if (data.status) {
                    location.reload();
                } else {
                    new Message(mainMessageBox, data.info, 'error', 5);
                }
            },
            error: function(error){
                console.log("ERROR", error);
                new Message(mainMessageBox, `Произошла системная ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
            }
        });
    }
});
$('.productImage_close').on('click', function(e){
    e.preventDefault();
    const imageId = parseInt($(this).attr('data-imageId'));
    if (confirm(`Подтвердите удаление изображения`)) {
        $.ajax({
            url: 'actions/product/removeImage',
            type: 'POST',
            dataType: 'JSON',
            data: {
                imageId: imageId
            },
            success: function(data){
                if (data.status) {
                    location.reload();
                } else {
                    new Message(mainMessageBox, data.info, 'error', 5);
                }
            },
            error: function(error){
                console.log("ERROR", error);
                new Message(mainMessageBox, `Произошла системная ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
            }
        });
    }
});