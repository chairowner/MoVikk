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
    <?=$PAGE->getHead($USER->isGuest())?>
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