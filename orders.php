<?php
require_once('includes/autoload.php');
$PAGE = new Page($conn);
$COMPANY = new Company($conn);
$USER = new User($conn);
$CART = new Cart($conn);
$ORDERS = new Order($conn);

if ($USER->isGuest()) {
    $PAGE->redirect();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$PAGE->getHead($PAGE->title, $PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/orders.css">
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="/assets/libs/clipboard/js/clipboard.min.js"></script>
    <script defer src="/assets/common/js/orders.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$PAGE->title?></h1>
    </div>
    <main class="minScreenHeight loaderBlock-wrapper">
        <div class="container">
            <div id="orders" class="">
                <div class="loader1"></div>
            </div>
        </div>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>