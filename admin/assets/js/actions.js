const main = $("#main");
const category = $('input[data-editCategory]').attr('data-editCategory');
const form = $('#formData');
let redirectToCategory = form.attr('redirectToCategory');
if (redirectToCategory == null) {
    redirectToCategory = true;
} else {
    redirectToCategory = redirectToCategory.trim() !== "false";
}
let data;

function act(action, params, redirectToCategory = true) {
    let type = "POST";
    if (action === "get") {
        type = "GET";
    }
    const url = "/admin/actions/"+category+"/"+action;
    $.ajax({
        url: url,
        cache: false,
        processData: false,
        contentType: false,
        type: type,
        dataType: 'JSON',
        data: params,
        success: function(data) {
            console.log(url + "\n", data);
            let messageType;
            if (data.status) {
                messageType = "success";
                if (redirectToCategory) {
                    location.href = "/admin/"+category;
                } else {
                    location.reload();
                }
            } else {
                messageType = "error";
            }
            new Message(mainMessageBox, data.info.join("\n"), messageType, 5);
        },
        error: function(error){
            console.error('ERROR', error);
            new Message(mainMessageBox, `Произошла ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
        }
    });
}

// добавление
form.on('submit',function(e) {
    // e.preventDefault();
    let action = form.find('input[name=action]');
    if (action.length > 0) {
        action = action.val();
    } else {
        action = 'edit';
    }
    let formData = new FormData(form[0]);
    act(action, formData, redirectToCategory);
    return false;
});

// удаление
form.find('.delete').on('click',function(e){
    e.preventDefault();
    const id = parseInt($('#formData').find('input[name="id"]').val());
    if (id > 0) {
        if (confirm('Подтвердите удаление')) {
            let formData = new FormData();
            formData.append("id",id);
            act("remove", formData, redirectToCategory);
        }
    } else {
        new Message(mainMessageBox, `Произошла ошибка\nПожалуйста, обновите страницу и повторите действие`, 'error', 5);
    }
});