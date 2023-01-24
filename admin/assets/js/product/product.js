$('.productImage').on('click', function(e){
    e.preventDefault();
});
$('.productImage_setMain').on('click', function(e){
    e.preventDefault();
    let productId = parseInt($('input[name="id"]').val());
    let imageId = parseInt($(this).attr('data-imageId'));
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
                console.log(data);
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