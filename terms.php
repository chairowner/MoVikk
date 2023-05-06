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
    <?=$_PAGE->GetHead($_USER->isGuest())?>
    <link rel="stylesheet" href="/assets/common/css/terms.css">
    <script defer src="/assets/common/js/terms.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main style="min-height:35vh;">
        <section class="m-0">
            <div class="container">
                <div>
                    
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>