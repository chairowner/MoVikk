<?php
set_include_path('includes');
$company = new stdClass();
$company->name = 'MoVikk';
$company->description = 'Оборудование для салонов красоты и расходники по приятным ценам';
$company->vk = 'vk.com/movikk';
$company->inst = 'movikk';
$company->tel = '+79223127607';
$company->tel_format = sprintf(
    "%s (%s) %s-%s-%s",
    intval(substr($company->tel, 1, 1)) + 1,
    substr($company->tel, 2, 3),
    substr($company->tel, 5, 3),
    substr($company->tel, 8, 2),
    substr($company->tel, 10)
);

$this_page = basename($_SERVER['PHP_SELF']);
$pages = [
    ['href' => '/', 'title' => 'Главная'],
    ['href' => '/shop', 'title' => 'Каталог'],
    ['href' => '/study', 'title' => 'Обучение'],
    ['href' => '/faq', 'title' => 'FAQ'],
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/assets/icons/favicon.png" type="image/png">
    <link rel="stylesheet" href="/assets/css/nullstyle.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/adaptive.css">
    <link rel="stylesheet" href="/assets/css/index.css">
    <title><?=$company->name;?> - <?=mb_strtolower($company->description)?></title>
    <meta name="description" content="<?=$company->description?>">
</head>
<body>
    <?php include('header.php');?>
    <section>
        <div class="container">
            <h2>Чаще всего у нас покупают</h2>
            <div class="device-cards">
                <div class="device-card">
                    <div class="device-card-image">
                        <img src="/assets/img/dev.png" alt="Device name">
                    </div>
                    <div class="device-card-body">
                        <h5 class="device-card-title">
                            Аппарат эндосферы, модель 2
                        </h5>
                        <div class="device-card-text">
                            <span>
                                Аппаратная процедура для коррекции фигуры, снижения веса, лечения целлюлита (в том числе фиброзного) и возрастных изменений кожи.
                            </span>
                        </div>
                        <p class="device-card-price">280.000₽</p>
                        <a href="/product/1"  class="button elems cart-add">
                            <span>В корзину</span>
                            <img src="/assets/icons/button_plus.svg" alt="+">
                        </a>
                    </div>
                </div>
                <div class="device-card">
                    <div class="device-card-image">
                        <img src="/assets/img/dev.png" alt="Device name">
                    </div>
                    <div class="device-card-body">
                        <h5 class="device-card-title">
                            Аппарат эндосферы, модель 2
                        </h5>
                        <div class="device-card-text">
                            <span>
                                Аппаратная процедура для коррекции фигуры, снижения веса, лечения целлюлита (в том числе фиброзного) и возрастных изменений кожи.
                            </span>
                        </div>
                        <p class="device-card-price">280.000₽</p>
                        <a href="/product/1"  class="button elems cart-add">
                            <span>В корзину</span>
                            <img src="/assets/icons/button_plus.svg" alt="+">
                        </a>
                    </div>
                </div>
                <div class="device-card">
                    <div class="device-card-image">
                        <img src="/assets/img/dev.png" alt="Device name">
                    </div>
                    <div class="device-card-body">
                        <h5 class="device-card-title">
                            Аппарат эндосферы, модель 2
                        </h5>
                        <div class="device-card-text">
                            <span>
                                Аппаратная процедура для коррекции фигуры, снижения веса, лечения целлюлита (в том числе фиброзного) и возрастных изменений кожи.
                            </span>
                        </div>
                        <p class="device-card-price">280.000₽</p>
                        <a href="/product/1"  class="button elems cart-add">
                            <span>В корзину</span>
                            <img src="/assets/icons/button_plus.svg" alt="+">
                        </a>
                    </div>
                </div>
                <div class="device-card">
                    <div class="device-card-image">
                        <img src="/assets/img/dev.png" alt="Device name">
                    </div>
                    <div class="device-card-body">
                        <h5 class="device-card-title">
                            Аппарат эндосферы, модель 2
                        </h5>
                        <div class="device-card-text">
                            <span>
                                Аппаратная процедура для коррекции фигуры, снижения веса, лечения целлюлита (в том числе фиброзного) и возрастных изменений кожи.
                            </span>
                        </div>
                        <p class="device-card-price">280.000₽</p>
                        <a href="/product/1"  class="button elems cart-add">
                            <span>В корзину</span>
                            <img src="/assets/icons/button_plus.svg" alt="+">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <h2>Категории товаров</h2>
            <div class="all_categories" style="background-image:url(/assets/img/all_products.png);">
                <p>Все товары</p>
            </div>
            <div class="main-categories">
                <div class="category" style="background-image:url(/assets/img/categories/category_1.png);">
                    <p>Гели для ухода за кожей</p>
                </div>
                <div class="category" style="background-image:url(/assets/img/categories/category_2.png);">
                    <p>Косметологические аппараты</p>
                </div>
            </div>
        </div>
    </section>
    <section>
        <div class="container">
            <h2 class="text-center">При покупке оборудования, обучение по эксплуатации в подарок!</h2>
            <iframe width="560" height="315" src="https://www.youtube.com/watch?v=WRMnm0s5JpY" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </section>
    <?php include('footer.php');?>
    <?php include('scripts.php');?>
</body>
</html>