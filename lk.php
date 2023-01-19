<?php
set_include_path(".");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) {
    $_PAGE->redirect();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->getHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="/assets/common/css/lk.css">
    <script defer src="/assets/common/js/lk.js"></script>
</head>
<body>
    <?php include_once('includes/header.php')?>
    <div class="page__title">
        <h1 class="container"><?=$_PAGE->title?></h1>
    </div>
    <main>
        <section class="m-0">
            <div class="container main">
                <div class="section information shadowBox">
                    <?php
                    $fields = $conn->prepare("SELECT `surname`, `name`, `patronymic`, `email`, `phone` FROM `users` WHERE `id` = :id");
                    $fields->execute(['id'=>$_USER->getId()]);
                    $fields = $fields->fetch(PDO::FETCH_ASSOC);?>
                    <div id="userData">
                        <div class="row">
                            <div class="rowTitle">
                                <h2>ФИО</h2>
                                <img data-name="fullname" class="js-edit fill-secondary" src="/assets/icons/edit.svg" title="Редактирование" alt="Редактировать">
                            </div>
                            <div class="line">
                                <div class="item" title="Фамилия, имя, отчество (если есть)">
                                    <span data-value="fullname"><?=implode(' ', [$fields['surname'],$fields['name'],$fields['patronymic']])?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="rowTitle">
                                <h2>Контактная информация</h2>
                            </div>
                            <div class="line">
                                <div class="item" title="E-mail">
                                    <span class="title">
                                        <span>E-mail</span>
                                        <img data-name="email" class="js-edit fill-secondary" src="/assets/icons/edit.svg" title="Редактирование" alt="Редактировать">
                                    </span>
                                    <span data-value="email"><?=$fields['email']?></span>
                                </div>
                                <div class="item" title="Контактный номер">
                                    <span class="title">
                                        <span>Телефон</span>
                                        <img data-name="phone" class="js-edit fill-secondary" src="/assets/icons/edit.svg" title="Редактирование" alt="Редактировать">
                                    </span>
                                    <span data-value="phone"><?=isset($fields['phone']) && !empty(trim($fields['phone'])) ? trim($fields['phone']) : '<i class="no-data">Не указан</i>'?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include_once('includes/footer.php')?>
</body>
</html>