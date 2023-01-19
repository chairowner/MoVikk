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
    <?=$_PAGE->getHead($_USER->isGuest())?>
    <style>
        main {
            min-height: 34vh;
        }
    </style>
</head>
<body>
    <?php include_once('includes/header.php');?>
    <main>
        <section>
            <div class="container">
                <h1>Ошибка 404: Страница не найдена</h1>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php');?>
</body>
</html>