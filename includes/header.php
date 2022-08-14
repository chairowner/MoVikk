<?php
$nav_icons = [
    ['href' => 'cart', 'src' => 'cart.svg', 'alt' => 'Корзина', 'title' => 'Корзина'],
    ['href' => 'lk', 'src' => 'user.svg', 'alt' => 'Личный кабинет', 'title' => 'Личный кабинет'],
];
?>
<header class="container">
    <div class="header__wrapper">
        <div class="d-flex flex-column justify-content-center">
            <a href="https://<?=$company->vk?>" class="text-uppercase" title="ВКонтакте"><?=$company->vk?></a>
            <a href="https://instagram.com/<?=$company->inst?>" class="text-uppercase" title="Инстаграм">@<?=$company->inst?></a>
        </div>
        <a href="/" class="text-uppercase" title="<?=$company->name?>">
            <img src="/assets/icons/logo.svg" alt="<?=$company->name?>" class="logo">
        </a>
        <div class="d-flex flex-column justify-content-center">
            <span>Позвоните нам: <a href="tel:<?=$company->tel?>"><?=$company->tel_format?></a></span>
        </div>
    </div>
</header>
<nav class="header-nav">
    <ul class="container">
        <li>
            <ul class="header-nav__pages">
                <?php foreach($pages as $key => $page):?>
                    <li>
                        <a href="<?=$page['href']?>"><?=$page['title']?></a>
                    </li>
                <?php endforeach;?>
            </ul>
        </li>
        <li>
            <ul class="header-nav__icons">
                <li>
                    <form class="item" id="search">
                        <img src="/assets/icons/search.svg" alt="Поиск" title="Поиск">
                    </div>
                </li>
                <li>
                    <a class="item" href="/cart">
                        <img src="/assets/icons/cart.svg" alt="Корзина" title="Корзина">
                        <span class="cart_number">10+</span>
                    </a>
                </li>
                <li>
                    <a class="item" href="/lk">
                        <img src="/assets/icons/user.svg" alt="Личный кабинет" title="Личный кабинет">
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>