<header class="container">
    <div class="header__wrapper">
        <div class="header__social">
            <a target="_blank" href="<?=$_COMPANY->socials['vk']['href']?>" class="text-uppercase d-flex align-items-center" title="<?=$_COMPANY->socials['vk']['name']?>">
                <img class="social-icon" src="/assets/icons/social-vk.svg" alt="ВКонтакте">
                <span><?=$_COMPANY->socials['vk']['title']?></span>
            </a>
            <a target="_blank" href="<?=$_COMPANY->socials['telegram']['href']?>" class="text-uppercase d-flex align-items-center" title="<?=$_COMPANY->socials['telegram']['name']?>">
            <img class="social-icon" src="/assets/icons/social-telegram.svg" alt="Инстаграм">
                <span><?=$_COMPANY->socials['telegram']['title']?></span>
            </a>
        </div>
        <a href="/" class="text-uppercase" title="<?=$_COMPANY->name?>">
            <img src="/assets/icons/logo.svg" alt="<?=$_COMPANY->name?>" class="logo">
        </a>
        <div class="header__social justify-content-center">
            <span>Позвоните нам:</span>
            <a href="tel:<?=$_COMPANY->phone?>"><?=$_COMPANY->phone_format?></a>
        </div>
    </div>
</header>
<nav class="header-nav">
    <div class="container header-nav-main">
        <div class="header-nav__pages">
            <?php foreach($_PAGE->all as $key => $page):?>
                <?php if($page['isMenuPage']):
                    if ($page['fileName'] === 'index') {
                        $page['fileName'] = '';
                        $page['title'] = 'Главная';
                    }
                    ?>
                    <div class="item<?=$page['fileName'] === $_PAGE->current ? ' open' : null?>">
                        <a href="/<?=$page['fileName']?>"><?=$page['title']?></a>
                    </div>
                <?php endif;?>
            <?php endforeach;?>
        </div>
        <div class="header-nav__icons">
            <?php if(!$_USER->isGuest()):?>
                <div class="item position-relative d-flex justify-content-center">
                    <a class="item__data" href="/admin">
                        <img src="/assets/icons/user.svg" alt="<?=$_USER->get(["name"])?>">
                        <span class="item__data__title"><?=$_USER->get(["name"])?></span>
                    </a>
                    <?php if(!$_USER->isGuest()):?>
                        <div class="lk-menu shadowBox">
                            <a href="/<?=ADMIN_URL?>" class="item" title="Административная панель">Административная панель</a>
                            <span class="item lk-menu-exit" title="Выйти">Выйти</span>
                        </div>
                    <?php endif;?>
                </div>
            <?php endif;?>
        </div>
    </div>
    <div class="container header-nav-addition">
        <div class="header-nav__icons">
            <?php
            $headerAdd = [
                '/' => [
                    'title' => 'Главная',
                    'icon' => 'home.svg',
                ],
                '/shop' => [
                    'title' => 'Каталог',
                    'icon' => 'shop.svg',
                ],
                '/study' => [
                    'title' => 'Обучение',
                    'icon' => 'study.svg',
                ],
                '/faq' => [
                    'title' => 'FAQ',
                    'icon' => 'faq.svg',
                ]
            ];
            if (!$_USER->isGuest()) {
                $headerAdd['/admin'] = [
                    'title' => 'Административная панель',
                    'icon' => 'user.svg',
                ];
            }
            foreach($headerAdd as $href => $item):?>
                <div class="item mobile">
                    <a class="item__data" href="<?=$href?>">
                        <img src="/assets/icons/<?=$item['icon']?>" alt="<?=$item['title']?>" title="<?=$item['title']?>">
                    </a>
                    <?php if($href === '/admin' && !$_USER->isGuest()):?>
                        <div class="lk-menu shadowBox">
                            <a href="/<?=ADMIN_URL?>" class="item" title="Административная панель">Административная панель</a>
                            <span class="item lk-menu-exit" title="Выйти">Выйти</span>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</nav>