const form = $(`#js-form`);
const modal = $('#js-modal');
const modalContent = $('#js-modal-content');
const cartInfo = $('#cartInfo');

function closeModal() {
    $.magnificPopup.close();
    modalContent.empty();
}

function createOrder() {
    $.ajax({
        url:'/actions/orders/create',
        async:true,
        dataType:'JSON',
        type:'POST',
        data:form.serialize(),
        success:function(data){
            console.log(data);
            closeModal();
            if (data.status) {
                new Message(mainMessageBox, `Заказ оформлен`, 'success', 3);
                if (data.payment.PaymentURL !== null) location.href = data.payment.PaymentURL;
                // setTimeout(() => {
                //     location.href = "/orders";
                // }, 3000);
            } else {
                new Message(mainMessageBox, data.info, 'error', 5);
            }
        },
        error:function(err){
            console.log('ERROR');
            console.log(err);
            new Message(mainMessageBox, `При оформлении заказа произошла системная ошибка.\nНо вы всё так же можете сделать у нас заказ, связавшись с нами напрямую во ВКонтакте, WhatsApp, Viber!`, 'error', 5);
        }
    });
}

function getCartData() {
    $.ajax({
        url:'/actions/cart/getProducts',
        async:true,
        dataType:'JSON',
        success:function(data){
            let cartInfo_tmp = "";
            let price = 0;
            data.forEach(product => {
                price += (product.currentPrice * product.countInCart);
                cartInfo_tmp +=
                `<p class="d-flex flex-wrap justify-content-center gap-5">` +
                    `<span>` + product.name + `</span>` +
                    `<span>(` + product.countInCart + ` ед.)</span>` +
                    `<span> - ` + formatPrice(product.currentPrice * product.countInCart) + `</span>` +
                `</p>`;
            });
            cartInfo_tmp +=
            `<p class="d-flex flex-wrap justify-content-center gap-5" style="margin-top:10px;">` +
                `<strong>Общая стоимость:</strong> <span>` + formatPrice(price) + `</span>` +
            `</p>`;
            cartInfo.html(cartInfo_tmp);
        },
        error:function(err){
            console.log('ERROR');
            console.log(err);
            new Message(mainMessageBox, `При выводе товаров из корзины произошла системная ошибка.\nНо вы всё так же можете сделать у нас заказ, связавшись с нами во ВКонтакте, WhatsApp, Viber или позвонив по телефону!`, 'error', 5);
        }
    });
}

function objectify(formArray) {
    //serialize data function
    var returnArray = {};
    for (var i = 0; i < formArray.length; i++){
        formArray[i]['value'] = formArray[i]['value'].trim();
        returnArray[formArray[i]['name']] = formArray[i]['value'] === "" ? null : formArray[i]['value'];
    }
    return returnArray;
}

form.on('submit',function(e){
    // e.preventDefault();
    $.magnificPopup.open({
        type: 'inline',
        preloader: true,
        modal:true,
        items: {
            src: '#js-modal' 
        },
        callbacks: {
            beforeOpen: function() {
                const formData = objectify(form.serializeArray());

                const blockClasses = "d-flex flex-column align-items-start flex-wrap gap-5";

                const phoneBlock    = $('<div>', {class: blockClasses});
                const addressBlock  = $('<div>', {class: blockClasses});
                const userBlock  = $('<div>', {class: blockClasses});
                const userCommentBlock  = $('<div>', {class: blockClasses});

                let address = [];
                let user = [];

                phoneBlock.append(`<b>Телефон</b>`);
                if (formData.phone === null) {
                    phoneBlock.append(`<i>Отсутствует</i>`);
                } else {
                    phoneBlock.append(`<span>` + formData.phone + `</span>`);
                }
                
                if (formData.postcode !== null)     address.push(formData.postcode);
                if (formData.country !== null)      address.push(formData.country);
                if (formData.region !== null)       address.push(formData.region);
                if (formData.place !== null)        address.push(formData.place);
                if (formData.street !== null)       address.push(formData.street);
                if (formData.building !== null)     address.push(formData.building);
                if (formData.block !== null)        address.push(formData.block);
                if (formData.cell !== null)         address.push(formData.cell);

                if (formData.surname !== null)      user.push(formData.surname);
                if (formData.name !== null)         user.push(formData.name);
                if (formData.patronymic !== null)   user.push(formData.patronymic);

                userBlock.append(`<b>ФИО получателя</b>`);
                if (user.length === 0) {
                    userBlock.append(`<i>Отсутствует</i>`);
                } else {
                    userBlock.append(`<p>` + user.join(' ') + `</p>`);
                }

                addressBlock.append(`<b>Адрес</b>`);
                if (address.length === 0) {
                    addressBlock.append(`<i>Отсутствует</i>`);
                } else {
                    addressBlock.append(`<p>` + address.join(', ') + `</p>`);
                }

                userCommentBlock.append(`<b>Комментарий к заказу</b>`);
                if (formData.userComment === null) {
                    userCommentBlock.append(`<i>Отсутствует</i>`);
                } else {
                    userCommentBlock.append(`<p style="white-space:pre-wrap;">` + formData.userComment + `</p>`);
                }

                modalContent.empty();
                modalContent.append(phoneBlock);
                modalContent.append(userBlock);
                modalContent.append(addressBlock);
                modalContent.append(userCommentBlock);
            }
        }
    });
    return false;
});

$(document).on('click', '.js-popup-modal-action', function (e) {
    e.preventDefault();
    const action = $(this).attr('data-action');
    if (action === "confirm") {
        createOrder();
    } else if (action === "cancel") {
        closeModal();
    }
});

getCartData();