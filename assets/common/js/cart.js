const errMsg = `При выводе товар из корзины произошла системная ошибка.\nНо вы всё так же можете сделать у нас заказ, связавшись с нами во ВКонтакте, WhatsApp, Viber или позвонив по телефону!`;
const container = $('#products');
const messageBox = $('#mainMessageBox');
const productCounterClass ='js-productCounter', productCounter ='.'+productCounterClass;
const actionBtnClass ='js-cardAction', actionBtn ='.'+actionBtnClass;
const actionAdd = 'add', actionRemove = 'remove';

function updateCounter(counter, count = null) {
    if (count !== null) {counter.text(parseInt(count));}
    $.ajax({
        url:'/actions/cart/' + action,
        type:'POST',
        dataType:'JSON',
        data:{id:productId},
        success: function(data){
            let msgStatus = 'error';
            if (data.status) {
                msgStatus = 'success';
                if (data.count >= 1) {
                    updateCounter(counter, data.count);
                } else {
                    location.reload();
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
}

$(document).on('click',actionBtn,function(){
    const btn = $(this);
    const btns = $(actionBtn);
    const action = btn.attr('data-action');
    const oldInner = btns.html();
    let productId = btn;
    if (btn.hasClass('counter')) productId = productId.parent().parent().parent().parent().parent();

    const counter = productId.find('.js-counter');
    
    productId = parseInt(productId.attr('product-id'));
    
    showLoad(btns, null, false);
    $.ajax({
        url:'/actions/cart/' + action,
        type:'POST',
        dataType:'JSON',
        data:{id:productId},
        success: function(data){
            let msgStatus = 'error';
            if (data.status) {
                msgStatus = 'success';
                if (data.count >= 1) {
                    updateCounter(counter, data.count);
                } else {
                    location.reload();
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

function loadProducts(products = []) {
    if (products.length > 0) {
        container.html('');
        for (let i = 0; i < products.length; i++) {
            const product = products[i];
            const $price = $('<div>',{class:'data-main-price'});
            $price.append($('<span>',{class:'price-current',text:formatPrice(product.currentPrice)}));
            if (product.oldPrice != null) $price.append($('<span>',{class:'price-old',text:formatPrice(product.oldPrice)}));
            container.append(
                $('<div>', {class:'shadowBox product', 'product-id':product.productId})
                .append($('<div>',{class:'item title',html:$('<a>',{href:product.href,text:product.name})}))
                .append(
                    $('<div>',{class:'item data'})
                    .append($('<div>',{class:'image',style:"flex-grow: 1;"})
                        .append($('<a>',{href:product.href}).append($('<img>',{src:product.image,alt:product.name,title:product.name})))
                    )
                    .append($('<div>',{class:'data-main d-flex flex-wrap gap-100'})
                        .append($price)
                        .append($('<div>',{class:'data-main-count'}).append(
                            $('<div>', {class:productCounterClass})
                            .append($('<input>',{type:'button',value:'-',class:"button counter minus "+actionBtnClass,'data-action':actionRemove}))
                            .append($('<span>',{class:'js-counter', text:product.countInCart}))
                            .append($('<input>',{type:'button',value:'+',class:"button counter plus "+actionBtnClass,'data-action':actionAdd}))
                        ))
                    )
                )
            );
        };
    } else {
        $('#main').html($('<div>',{class:'section w-100 shadowBox'}).append($('<span>',{style:'',text:'Ваша корзина пока что пуста'})));
    }
}

function uploadCart() {
    $.ajax({
        url:'/actions/cart/getProducts',
        async:false,
        dataType:'JSON',
        success:function(data){
            loadProducts(data);
        },
        error:function(err){
            console.log('ERROR');
            console.log(err);
            loadProducts();
            new Message(messageBox, `При выводе товаров из корзины произошла системная ошибка.\nНо вы всё так же можете сделать у нас заказ, связавшись с нами во ВКонтакте, WhatsApp, Viber или позвонив по телефону!`, 'error', 5);
        }
    });
}

uploadCart();