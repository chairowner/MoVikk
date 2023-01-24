<?php
set_include_path("../");
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_PAGE->title = "Административная панель";
$_PAGE->description = "Панель управления сайтом";
$_COMPANY = new Company($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) {
    $_PAGE->Redirect();
}

$items = [
    'category' => ['text' => 'Категории', 'title' => 'Список категорий'],
    'product' => ['text' => 'Товары', 'title' => 'Список товаров'],
    'instruction' => ['text' => 'Инструкции', 'title' => 'Список инструкций'],
    'country' => ['text' => 'Страны-изготовители', 'title' => 'Список стран-изготовителей'],
    'delivery' => ['text' => 'Доставки', 'title' => 'Список доставок'],
    'faq' => ['text' => 'FAQ', 'title' => 'Часто задаваемые вопросы и ответы на них'],
    'user' => ['text' => 'Пользователи', 'title' => 'Список пользователей'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="assets/css/main.css">
    <script defer src="/assets/common/js/showLoad.js"></script>
    <script defer src="/assets/common/js/formatPrice.js"></script>
</head>
<body>
    <div class="page__title">
        <div class="container">
            <a href="/"><img src="/assets/icons/logo.svg" class="logo" alt="<?=$_COMPANY->name?>"></a>
            <h1 class="container"><?=$_PAGE->title?></h1>
        </div>
    </div>
    <main style="min-height: 35vh;">
        <section class="m-0">
            <div id="main">
                <div class="container">
                    <div class="item-list">
                        <?php foreach ($items as $href => $item):?>
                            <a class="item shadowBox" href="<?=$href?>" title="<?=$item['title']?>"><?=$item['text']?></a>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div id="mainMessageBox"></div>
</body>
</html>