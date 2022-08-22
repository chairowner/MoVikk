<?php
$nav_icons = [
    ['href' => 'cart', 'src' => 'cart.svg', 'alt' => 'Корзина', 'title' => 'Корзина'],
    ['href' => 'lk', 'src' => 'user.svg', 'alt' => 'Личный кабинет', 'title' => 'Личный кабинет'],
];
?>
<header class="container">
    <div class="header__wrapper">
        <div class="header__social">
            <a href="<?=$_COMPANY->socials['vk']['href']?>" class="text-uppercase" title="<?=$_COMPANY->socials['vk']['name']?>">
                <i class="fa-brands fa-vk"></i>
                <span><?=$_COMPANY->socials['vk']['title']?></span>
            </a>
            <a href="<?=$_COMPANY->socials['instagram']['href']?>" class="text-uppercase" title="<?=$_COMPANY->socials['instagram']['name']?>">
                <i class="fa-brands fa-instagram"></i>
                <span><?=$_COMPANY->socials['instagram']['title']?></span>
            </a>
        </div>
        <a href="/" class="text-uppercase" title="<?=$_COMPANY->name?>">
            <img src="/assets/icons/logo.svg" alt="<?=$_COMPANY->name?>" class="logo">
        </a>
        <div class="header__social">
            <span>Позвоните нам: <a href="tel:<?=$_COMPANY->phone?>"><?=$_COMPANY->phone_format?></a></span>
        </div>
    </div>
</header>
<nav class="header-nav">
    <div class="container d-flex justify-content-between flex-nowrap">
        <div class="header-nav__pages">
            <?php foreach($_PAGE->all as $key => $page):?>
                <?php if($page['isMenuPage']):
                    if ($page['fileName'] === 'index') {
                        $page['fileName'] = '';
                        $page['title'] = 'Главная';
                    }
                    ?>
                    <div class="item">
                        <a href="/<?=$page['fileName']?>"><?=$page['title']?></a>
                    </div>
                <?php endif;?>
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