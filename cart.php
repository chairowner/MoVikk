<?php
set_include_path(".");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) {
    $_PAGE->Redirect();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/cart.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="/assets/common/js/cart.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main>
        <section class="m-0">
            <div class="container" id="main">
                <div id="orderMaking__wrapper">
                    <div class="shadowBox" id="orderMaking">
                        <div class="item" id="orderMaking__button">
                            <input type="button" class="button w-100 fw-bold" style="height:56px;" value="Перейти к оформлению">
                            <p id="orderMaking__button__text">Доставка оплачивается отдельно при получении товаров</p>
                        </div>
                        <div class="item" id="moreInfo__wrapper">
                            <span class="fw-bold big-text">Ваша корзина</span>
                            <div class="d-flex justify-content-between">
                                <span>Товары (<span id="moreInfo__count">0</span>)</span>
                                <span id="moreInfo__price"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Скидка</span>
                                <span id="moreInfo__sale" class="fw-bold error-light pe-none">-0</span>
                            </div>
                        </div>
                        <div class="item big-text" id="totalCost__wrapper">
                            <span class="fw-bold">Общая стоимость</span>
                            <span id="totalCost">
                                <img style="height:40px;width:40px;" src="/assets/icons/spinner.svg" alt="Загрузка...">
                            </span>
                        </div>
                    </div>
                </div>
                <div id="products">
                    <div class="section shadowBox">
                        <div class="loaderBlock">
                            <div class="loader1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>