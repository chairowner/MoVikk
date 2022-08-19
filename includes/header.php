<?php
$nav_icons = [
    ['href' => 'cart', 'src' => 'cart.svg', 'alt' => 'Корзина', 'title' => 'Корзина'],
    ['href' => 'lk', 'src' => 'user.svg', 'alt' => 'Личный кабинет', 'title' => 'Личный кабинет'],
];
?>
<header class="container">
    <div class="header__wrapper">
        <div class="header__social">
            <a href="https://<?=$company->vk?>" class="text-uppercase" title="ВКонтакте"><?=$company->vk?></a>
            <a href="https://instagram.com/<?=$company->inst?>" class="text-uppercase" title="Инстаграм">@<?=$company->inst?></a>
        </div>
        <a href="/" class="text-uppercase" title="<?=$company->name?>">
            <img src="/assets/icons/logo.svg" alt="<?=$company->name?>" class="logo">
        </a>
        <div class="header__social">
            <span>Позвоните нам: <a href="tel:<?=$company->tel?>"><?=$company->tel_format?></a></span>
        </div>
    </div>
</header>
<nav class="header-nav">
    <div class="container d-flex justify-content-between flex-nowrap">
        <div class="header-nav__pages">
            <?php foreach($pages as $key => $page):?>
                <div class="item">
                    <a href="<?=$page['href']?>"><?=$page['title']?></a>
                </div>
            <?php endforeach;?>
        </div>
        <div class="header-nav__icons">
            <div class="item">
                <form class="item__data" id="search">
                    <img src="/assets/icons/search.svg" alt="Поиск" title="Поиск">
                </form>
            </div>
            <div class="item">
                <a class="item__data" href="/cart">
                    <img src="/assets/icons/cart.svg" alt="Корзина" title="Корзина">
                    <span class="cart_number">10+</span>
                </a>
            </div>
            <div class="item">
                <a class="item__data" href="/lk">
                    <img src="/assets/icons/user.svg" alt="Личный кабинет" title="Личный кабинет">
                </a>
            </div>
        </div>
    </div>
</nav>