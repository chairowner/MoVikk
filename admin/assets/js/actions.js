const
main = $("#main"),
messageBox = $("#mainMessageBox"),
category = $('input[data-editCategory]').attr('data-editCategory'),
form = $('#formData');
let data;

function act(action, params) {
    let type = "POST";
    if (action === "get") {
        type = "GET";
    }
    $.ajax({
        url: "/admin/actions/"+category+"/"+action,
        cache: false,
        processData: false,
        contentType: false,
        type: type,
        dataType: 'JSON',
        data: params,
        success: function(data) {
            console.log(data);
            let messageType;
            if (data.status) {
                messageType = "success";
                // location.href = "/admin/"+category;
            } else {
                messageType = "error";
            }
            new Message(messageBox, data.info.join("\n"), messageType, 5);
        },
        error: function(error){
            console.error('ERROR', error);
            new Message(messageBox, `Произошла ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
        }
    });
}

// добавление
form.on('submit',function(e) {
    e.preventDefault();
    let action = form.find('input[name=action]');
    if (action.length > 0) {
        action = action.val();
    } else {
        action = 'edit';
    }
    let formData = new FormData(form[0]);
    act(action, formData);
});

// удаление
form.find('.delete').on('click',function(e){
    e.preventDefault();
    const id = parseInt($('#formData').find('input[name="id"]').val());
    if (id > 0) {
        if (confirm('Подтвердите удаление')) {
            let formData = new FormData();
            formData.append("id",id);
            act("remove", formData);
        }
    } else {
        new Message(messageBox, `Произошла ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
    }
});