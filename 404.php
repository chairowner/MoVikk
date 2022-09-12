<?php require_once('includes/autoload.php');?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead()?>
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
    <?php include_once('includes/scripts.php');?>
</body>
</html>