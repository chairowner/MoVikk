const messageBox = $('#mainMessageBox');
const statuses = [
    {title:'Сборка заказа',icon:'box-up.svg'},
    {title:'Отменён',icon:'box-close.svg'},
    {title:'Отправлен',icon:'delivery-drive.svg'},
    {title:'Доставлен',icon:'delivery-arrived.svg'},
    {title:'Возврат',icon:'return.svg'}
];
const currentDate = new Date();
const months = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
function loadOrders() {
    const ordersBlock = $("#orders");
    const noOrders = $('<span class="noOrders">Нет заказов</span>');
    $.ajax({
        url: '/actions/orders/getStatusArray',
        type: 'GET',
        dataType: 'JSON',
        success: function (data) {
            const newOrderStatuses = data;
            $.ajax({
                url: '/actions/orders/get',
                type: 'GET',
                dataType: 'JSON',
                data: {'load': 'all'},
                success: function (data) {
                    console.log(data);
                    let html = [];
                    if (data.noOrders) {
                        html.push(noOrders);
                    } else {
                        for (let i = 0; i < data.orders.length; i++) {
                            const order = data.orders[i];
                            order.added = new Date(order.added);
                            let dateFrom = `Заказ №` + order.id + ` от `+order.added.getDate()+" "+months[order.added.getMonth()];
                            if (currentDate.getFullYear() !== order.added.getFullYear()) {
                                dateFrom += " "+order.added.getFullYear();
                                dateFrom += " года";
                            }
                            
                            let $products = $('<div>', {class:'order-body-products'});
                            Object.entries(order.products).forEach((productItem) => {
                                const [productId, product] = productItem;
                                $products.append($('<a>',{
                                    class:'order-body-products-item',
                                    href:product.href,
                                    title:product.name,
                                    html:$('<img>',{
                                        src:product.image.path+product.image.name,
                                        alt:product.name,
                                    })
                                }));
                            });
        
                            let $deliveryBlock = null;
                            if (order.delivery.name != null) {
                                $deliveryBlock = $('<span>',{class:'order-body-info-delivery'});
                                $deliveryBlock.append($('<span>',{text:'Служба доставки:'}));
                                if (order.delivery.link != null) {
                                    $deliveryBlock.append($('<a>',{href:order.delivery.link,text:order.delivery.name,target:'_blank'}));
                                } else {
                                    $deliveryBlock.append($('<span>',{text:order.delivery.name}));
                                }
                            } else if (order.delivery.link != null) {
                                $deliveryBlock = $('<span>',{class:'order-body-info-delivery'});
                                $deliveryBlock.append($('<span>',{text:'Служба доставки:'}));
                                $deliveryBlock.append($('<a>',{href:order.delivery.link,text:'Сайт доставки',target:'_blank'}));
                            }
        
                            let $trackingBlock = null;
                            if (order.tracking != null) {
                                $trackingBlock = $('<span>',{class:'order-body-info-tracking'});
                                $trackingBlock
                                .append($("<span>",{text:'Трек-номер:'}))
                                .append($("<a>",{class:'js-copy','data-copy-error':"Не удалось скопировать трек-номер",'data-copy-success':"Трек-номер скопирован",title:"Скопировать",text:order.tracking}));
                            }
        
                            let $paymentBlock = null;
                            if (newOrderStatuses.includes(order.status) && order.payment_link != null) {
                                $paymentBlock = $('<a>',{class:'button secondary button-payment', target: '_blank', href: order.payment_link, text: "Оплатить"});
                            }
        
                            let orderStatus = {
                                isLoad:true,
                                alt:order.status,
                                src:'/assets/icons/'
                            };
                            
                            for (let i = 0; i < statuses.length; i++) {
                                const status = statuses[i];
                                if (order.status == status.title) {
                                    orderStatus.src += status.icon;
                                    orderStatus.isLoad = false;
                                    break;
                                }
                            }
        
                            if (orderStatus.isLoad) orderStatus.src += 'payment-loading.svg';
                            
                            let $order = $('<div>',{class:'order shadowBox','data-order-id':order.id});
                            if (!order.isClosed) $order.addClass('active');
                            $order
                            .append(
                                $('<h3>',{class: 'order-item order-head'})
                                .append(
                                    $('<div>',{
                                        class:'order-head-from',
                                        text:dateFrom
                                    })
                                )
                                .append(
                                    $('<div>',{class:'order-head-info'})
                                    .append($('<div>',{
                                        class:'order-head-info-price',
                                        text:formatPrice(order.price)
                                    }))
                                )
                            )
                            .append(
                                $('<div>',{class: 'order-item order-body'})
                                .append(
                                    $('<div>',{class: 'order-body-info'})
                                    .append(
                                        $('<div>',{class: 'order-body-info-status'})
                                        .append($('<img>',{
                                            class:'status-icon',
                                            alt:orderStatus.alt,
                                            src:orderStatus.src
                                        }))
                                        .append($('<span>',{class:'status-text',text:order.status}))
                                        .append($paymentBlock)
                                    )
                                    .append($deliveryBlock)
                                    .append($trackingBlock)
                                )
                                .append($products)
                            );
                            html.push($order);
                        }
                        if (html.length === 0) html.push(noOrders);
                    }
                    $(".loaderBlock-wrapper").removeClass("loaderBlock-wrapper");
                    ordersBlock.html(html);
                },
                error: function (err) {
                    console.log("ERROR");
                    console.log(err);
                    ordersBlock.html(noOrders);
                    let msg = 'Ошибка загрузки истории заказов';
                    new Message(messageBox, msg, 'error', 5);
                }
            });
        },
        error: function (err) {
            console.log("ERROR");
            console.log(err);
            ordersBlock.html(noOrders);
            let msg = 'Ошибка загрузки истории заказов';
            new Message(messageBox, msg, 'error', 5);
        }
    });
}
loadOrders(); // active orders