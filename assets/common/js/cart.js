const errMsg = `При выводе товар из корзины произошла системная ошибка.\nНо вы всё так же можете сделать у нас заказ, связавшись с нами во ВКонтакте, WhatsApp, Viber или позвонив по телефону!`;
const container = $('#products');
const messageBox = $('#mainMessageBox');
const productCounterClass ='js-productCounter', productCounter ='.'+productCounterClass;
const actionBtnClass ='js-cardAction', actionBtn ='.'+actionBtnClass;
const actionAdd = 'add', actionRemove = 'remove';
const deleteBtnClass = 'js-delete', deleteBtn = "."+deleteBtnClass;

function updateCartData() {
    const fullData = $('#orderMaking');
    const oldInner = fullData.html();
    const oldHeight = fullData.css('height');
    showLoad(fullData, null, true, 'primary', true);
    fullData.css({height:oldHeight});
    $.ajax({
        url:'/actions/cart/getCartData',
        dataType:'JSON',
        success: function(data){
            let msgStatus = 'error';
            if (data.status) {
                removeLoad(fullData,oldInner);
                fullData.css('height', 'auto');
                msgStatus = 'success';
                let currentPrice = 0, salePrice = 0, productCount = 0;
                for (let i = 0; i < data.products.length; i++) {
                    let product = data.products[i];
                    productCount += product.countInCart;
                    currentPrice += product.countInCart * product.currentPrice;
                    salePrice += product.countInCart *product.salePrice;
                }
                if (salePrice == 0) {
                    fullData.find('#moreInfo__sale').parent().addClass('d-none');
                } else {
                    fullData.find('#moreInfo__sale').parent().removeClass('d-none');
                    fullData.find('#moreInfo__sale').text("-"+formatPrice(salePrice));
                }
                fullData.find('#totalCost').text(formatPrice(currentPrice));
                fullData.find('#moreInfo__count').text(productCount);
            } else {
                removeLoad(fullData,oldInner);
                fullData.css('height', 'auto');
            }
            if (data.info != null) new Message(messageBox, data.info, msgStatus, 5);
        },
        error: function(err){
            console.log('ERROR');
            console.log(err);
            let msg = '';
            if (action == 'add') msg += `При добавлении товара в корзину`;
            else if (action == 'remove') msg += `При удалении товара из корзину`;
            msg += ` произошла ошибка\nПожалуйста, обновите страницу и повторите действие`;
            new Message(messageBox, msg, 'error', 5);
            removeLoad(btns,oldInner);
            fullData.css('height', 'auto');
        }
    });
}

function updateCounters(product, count = null, oldPrice = null, currentPrice = null) {
    if (count !== null) {
        count = parseInt(count);
        product.find('.js-counter').text(count); // счётчик товара
        const currentPriceDiv = product.find('.price-current');
        const oldPriceDiv = product.find('.price-old');
        const onePrice = product.find('.onePrice');
        if (count < 2) {
            onePrice.removeClass('show');
        } else {
            onePrice.addClass('show');
        }
        if (currentPrice != null) currentPriceDiv.text(formatPrice((parseFloat(currentPrice) * count)));
        if (oldPrice != null) oldPriceDiv.text(formatPrice((parseFloat(oldPrice)*count)));
        updateCartCount(); // счётчик в шапке
        updateCartData(); // данные справа
    }
}

$(document).on('click',actionBtn,function(){
    const btn = $(this);
    const btns = $(actionBtn);
    const action = btn.attr('data-action');
    if (parseInt(btn.attr('data-action-count')) === 0) {
        if (!confirm(`Вы хотите удалить товар из корзины?`)) return;
    }

    const actionCount = btn.attr('data-action-count') == null ? 1 : parseInt(btn.attr('data-action-count'));
    const oldInner = btns.html();
    let product;

    if (btn.hasClass('counter')) product = btn.parent().parent().parent().parent().parent();
    else product = btn.parent().parent().parent();
    
    const productId = parseInt(product.attr('product-id'));
    
    showLoad(btns, null, false);
    $.ajax({
        url:'/actions/cart/' + action,
        type:'POST',
        dataType:'JSON',
        data:{id:productId,q:actionCount},
        success: function(data){
            let msgStatus = 'error';
            if (data.status) {
                msgStatus = 'success';
                if (data.count > 0) {
                    if (data.count === 1) product.find('.counter[data-action="remove"]').attr('data-action-count',0); 
                    else if (data.count > 1) product.find('.counter[data-action="remove"]').attr('data-action-count',1); 
                    updateCounters(product, data.count, data.oldPrice, data.currentPrice);
                } else {
                    location.reload();
                }
            }
            if (data.info != null) new Message(messageBox, data.info, msgStatus, 5);
            removeLoad(btns, oldInner);
        },
        error: function(err){
            console.log('ERROR');
            console.log(err);
            let msg = '';
            if (action == 'add') msg += `При добавлении товара в корзину`;
            else if (action == 'remove') msg += `При удалении товара из корзину`;
            msg += ` произошла ошибка\nПожалуйста, обновите страницу и повторите действие`;
            new Message(messageBox, msg, 'error', 5);
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

            let oneProductPrice = 'onePrice';
            if (product.countInCart > 1) oneProductPrice += ' show';

            $price.append($('<span>',{class:'price-current',text:formatPrice((product.currentPrice*product.countInCart))}));
            if (product.oldPrice != null) $price.append($('<span>',{class:'price-old',text:formatPrice((product.oldPrice*product.countInCart))}));
            container.append(
                $('<div>', {class:'shadowBox product', 'product-id':product.productId})
                .append($('<div>',{class:'item title',html:$('<a>',{href:product.href,text:product.name})}))
                .append(
                    $('<div>',{class:'item data gap-40'})
                    .append($('<div>',{class:'image',style:"flex-grow: 1;"})
                        .append($('<a>',{href:product.href}).append($('<img>',{src:product.image,alt:product.name,title:product.name})))
                    )
                    .append($('<div>',{class:'h-100 d-flex flex-column justify-content-between',style:"flex-grow: 1;"})
                        .append($('<a>',{href:product.href,text:product.name}))
                        .append($('<span>',{class:deleteBtnClass+" addition "+actionBtnClass,text:"Удалить",'data-action':actionRemove,'data-action-count':0}))
                    )
                    .append($('<div>',{class:'data-main d-flex flex-wrap gap-50'})
                        .append($price)
                        .append($('<div>',{class:'data-main-count'})
                            .append(
                                $('<div>', {class:productCounterClass})
                                .append($('<input>',{type:'button',value:'-',class:"button counter minus "+actionBtnClass,'data-action':actionRemove,'data-action-count':(product.countInCart < 2 ? 0 : 1)}))
                                .append($('<span>',{class:'js-counter', text:product.countInCart}))
                                .append($('<input>',{type:'button',value:'+',class:"button counter plus "+actionBtnClass,'data-action':actionAdd}))
                            )
                            .append($('<span>', {class:oneProductPrice,text:formatPrice(product.currentPrice)}))
                        )
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
updateCartData();