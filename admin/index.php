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
    // 'user' => ['text' => 'Пользователи', 'title' => 'Список пользователей'],
    'company' => ['text' => 'Компания', 'title' => 'Данные о компании'],
    'page' => ['text' => 'Страницы', 'title' => 'Список страниц'],
    'category' => ['text' => 'Категории', 'title' => 'Список категорий'],
    'product' => ['text' => 'Товары', 'title' => 'Список товаров'],
    'country' => ['text' => 'Страны-изготовители', 'title' => 'Список стран-изготовителей'],
    'instruction' => ['text' => 'Инструкции', 'title' => 'Список инструкций'],
    'faq' => ['text' => 'FAQ', 'title' => 'Часто задаваемые вопросы и ответы на них'],
    'additionalFields' => ['text' => 'Дополнительные поля', 'title' => 'Дополнительные поля'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?=$_PAGE->GetHead($_USER->isGuest(), $_PAGE->title, $_PAGE->description)?>
    <link rel="stylesheet" href="assets/css/main.css">
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
                    <div class="item-list center">
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