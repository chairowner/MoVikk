<?php
set_include_path(".");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/terms.css">
    <script defer src="/assets/common/js/terms.js"></script>
</head>
<body>
    <?php include_once('includes/header.php');?>
    <main>
        <section>
            <div class="container">
                <h2 class="text-uppercase">Популярные товары</h2>
                <div class="w-100">
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php');?>
</body>
</html>