<?php
set_include_path(".");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);
$_ORDER = new Order($conn);

if ($_USER->isGuest()) {
    $_PAGE->Redirect();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/orders.css">
    <script defer src="/assets/common/js/formatPrice.js"></script>
    <script defer src="/assets/common/js/orders.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
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