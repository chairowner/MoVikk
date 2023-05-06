<?php
set_include_path("../");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);
if ($_USER->isGuest()) {
    header("HTTP/1.1 301");
    header("Location: /");
    exit;
}
if (count($_CART->getProducts($_USER->GetId())) < 1) {
    header("HTTP/1.1 301");
    header("Location: /");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/main.css">
    <link rel="stylesheet" href="/assets/libs/magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="/assets/common/css/magnific-popup.css">
    <link rel="stylesheet" href="/assets/common/css/createOrder.css">
    <script defer src="/assets/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="/assets/common/js/createOrder.js"></script>
</head>
<body>
    <?php include_once('includes/header.php');?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main class="minScreenHeight loaderBlock-wrapper">
        <div class="container">
            <div class="d-flex flex-column flex-wrap justify-content-center align-items-center">
                <div id="main-wrapper">
                    <div class="shadowBox" id="cartInfo">
                        <div class="loaderBlock">
                            <div class="loader1"></div>
                        </div>
                    </div>
                    <form href="#js-modal" id="js-form" class="main-form shadowBox" method="post">
                        <h2 class="text-center">Данные для отправки</h2>
                        <div class="d-flex flex-column gap-15">
                            <label for="phone"><b>Контактный телефон <span class="error">*</span></b></label>
                                <input type="tel" class="item field" id="phone" name="phone" required  placeholder="Контактный телефон" title="Контактный телефон" value="+79223127609">
                        </div>
                        <div class="d-flex flex-column gap-15">
                            <label for="surname"><b>Фамилия получателя <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="surname" name="surname" required placeholder="Фамилия получателя" title="Фамилия получателя" value="Za">
                            <label for="name"><b>Имя получателя <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="name" name="name" required placeholder="Имя получателя" title="Имя получателя" value="Lu">
                            <label for="patronymic"><b>Отчество получателя</b></label>
                                <input type="text" class="item field" id="patronymic" name="patronymic" placeholder="Отчество получателя" title="Отчество получателя" value="Pin">
                        </div>
                        <div class="d-flex flex-column gap-15">
                            <label for="postcode"><b>Почтовый индекс <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="postcode" name="postcode" required placeholder="Почтовый индекс" title="Почтовый индекс" value="617765">
                            <label for="country"><b>Страна <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="country" name="country" required placeholder="Страна" title="Страна" value="Россия">
                            <label for="region"><b>Регион <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="region" name="region" required placeholder="Регион" title="Регион" value="Пермский край">
                            <label for="place"><b>Населённый пункт <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="place" name="place" required placeholder="Населённый пункт" title="Населённый пункт" value="г. Чайковский">
                            <label for="street"><b>Улица <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="street" name="street" required placeholder="Улица" title="Улица" value="ул. Сосновая">
                            <label for="building"><b>Дом/строение <span class="error">*</span></b></label>
                                <input type="text" class="item field" id="building" name="building" required placeholder="Дом/строение" title="Дом/строение" value="10">
                            <label for="block"><b>Корпус</b></label>
                                <input type="text" class="item field" id="block" name="block" placeholder="Корпус (если необходимо)" title="Корпус (если необходимо)" value="">
                            <label for="cell"><b>Квартира/офис</b></label>
                                <input type="text" class="item field" id="cell" name="cell" placeholder="Квартира/офис (если необходимо)" title="Квартира/офис (если необходимо)" value="40">
                        </div>
                        <div class="d-flex flex-column gap-15">
                            <label for="userComment"><b>Комментарий к заказу</b></label>
                                <textarea class="item field" style="resize: vertical;" id="userComment" name="userComment" placeholder="Комментарий к заказу (при желании)" title="Комментарий к заказу (при желании)"></textarea>
                        </div>
                        <div class="w-100 d-flex justify-content-end">
                            <input type="submit" id="js-form-button" class="button" value="Оформить заказ">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    <div id="js-modal" class="magnific-popup-block d-flex flex-column gap-20 mfp-hide">
        <h1>Подтвердите оформление заказа</h1>
        <div id="js-modal-content" class="magnific-popup-block-content d-flex flex-column align-items-start gap-10"></div>
        <div class="magnific-popup-block-buttons">
            <input type="button" class="js-popup-modal-action button" data-action="confirm" value="Оформить">
            <input type="button" class="js-popup-modal-action button error" data-action="cancel" value="Отмена">
        </div>
    </div>
    
    <?php include_once('includes/footer.php');?>
</body>
</html>