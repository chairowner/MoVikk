<?php
require_once('includes/autoload.php');
$PAGE = new Page($conn);
$COMPANY = new Company($conn);
$USER = new User($conn);
$CART = new Cart($conn);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$PAGE->getHead($USER->isGuest(), $PAGE->title, $PAGE->description)?>
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