<?php if($_USER->isGuest()):?>
    <div id="login-msgBox" class="position-fixed d-flex flex-column align-items-end" style="gap:20px;right:0;bottom:0;margin:20px;z-index:1000000000;width:calc(100% - 40px);"></div>
    <div id="login-form-overlay" class="position-relative">
        <div class="loader1 position-absolute"></div>
        <div id="login-form" class="shadowBox position-relative">
            <span id="close-login-form">X</span>
            <form id="login-form-auth" class="item active">
                <p class="login-form-title">Авторизация</p>
                <input class="field shadowBox w-100" type="email" name="email" placeholder="E-mail" title="E-mail" required>
                <div class="login-form-passwordBlock">
                    <input class="field shadowBox w-100" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                    <div class="h-100 d-flex align-items-center"><div class="passView close" title="Показать пароль"><span class="passView-eyelid"></span></div></div>
                </div>
                <p class="text-center"><a href="/recoverPassword" class="primary cursor-pointer">Восстановить пароль</a></p>
                <input class="button shadowBox w-100" type="submit" value="Войти">
                <input type="hidden" class="g-recaptcha-response" name="g-recaptcha-response">
            </form>
            <form id="login-form-reg" class="item">
                <p class="login-form-title">Регистрация</p>
                <input class="field shadowBox w-100" type="text" name="name" placeholder="Имя" title="Имя" required>
                <input class="field shadowBox w-100" type="text" name="surname" placeholder="Фамилия" title="Фамилия" required>
                <input class="field shadowBox w-100" type="text" name="patronymic" placeholder="Отчество" title="Отчество">
                <input class="field shadowBox w-100" type="email" name="email" placeholder="E-mail" title="E-mail" required>
                <div class="login-form-passwordBlock">
                    <input class="field shadowBox w-100" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                    <div class="h-100 d-flex align-items-center"><div class="passView close" title="Показать пароль"><span class="passView-eyelid"></span></div></div>
                </div>
                <input class="field shadowBox w-100" type="password" name="passwordRepeat" placeholder="Повторите пароль" title="Повторите пароль" required>
                <label>
                    <input class="" type="checkbox" name="terms" value="true" title="Политика конфиденциальности и обработки персональных данных" required>
                    <span>Соглашаюсь с <a href="/terms">политикой конфиденциальности и обработки персональных данных</a></span>
                </label>
                <input class="button shadowBox w-100" type="submit" value="Зарегистрироваться">
                <input type="hidden" class="g-recaptcha-response" name="g-recaptcha-response" require>
            </form>
            <input class="button secondary shadowBox w-100" id="toggle-login-forms" type="button" value="Регистрация">
            <p class="grecaptcha-badge-text w-100 text-center">Сайт защищён reCAPTCHA.<br>К нему применяются <a target="_blank"href="https://policies.google.com/privacy">Политика конфиденциальности</a> и <a target="_blank"href="https://policies.google.com/terms">Условия предоставления услуг</a> Google.</p>
        </div>
    </div>
<?php endif;?>
<header class="container">
    <div class="header__wrapper">
        <div class="header__social">
            <a target="_blank" href="<?=$_COMPANY->socials['vk']['href']?>" class="text-uppercase d-flex align-items-center" title="<?=$_COMPANY->socials['vk']['name']?>">
                <img class="social-icon" src="/assets/icons/social-vk.svg" alt="ВКонтакте">
                <span><?=$_COMPANY->socials['vk']['title']?></span>
            </a>
            <a target="_blank" href="<?=$_COMPANY->socials['instagram']['href']?>" class="text-uppercase d-flex align-items-center" title="<?=$_COMPANY->socials['instagram']['name']?>">
            <img class="social-icon" src="/assets/icons/social-instagram.svg" alt="Инстаграм">
                <span><?=$_COMPANY->socials['instagram']['title']?></span>
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
            <div class="item position-relative d-flex justify-content-center">
                <a class="item__data" href="/lk">
                    <img src="/assets/icons/user.svg" alt="Личный кабинет">
                    <span class="item__data__title"><?php if(!$_USER->isGuest()){echo($_USER->get(["name"]));}else{echo("Вход");}?></span>
                </a>
                <?php if(!$_USER->isGuest()):?>
                    <div class="lk-menu shadowBox">
                        <?php if($_USER->isAdmin()):?>
                            <a href="/admin" class="item" title="Административная панель">Административная панель</a>
                        <?php endif;?>
                        <a href="/lk" class="item" title="Личный кабинет">Личный кабинет</a>
                        <a href="/lk/orders" class="item" title="Заказы">Заказы</a>
                        <span class="item lk-menu-exit" title="Выйти">Выйти</span>
                    </div>
                <?php endif;?>
            </div>
            <?php if(!$_USER->isGuest()):?>
                <div class="item">
                    <a class="item__data" href="/cart">
                        <img src="/assets/icons/cart.svg" alt="Корзина" title="Корзина">
                        <span class="cart_number">0</span>
                        <span class="item__data__title">Корзина</span>
                    </a>
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
                ]
            ];
            if (!$_USER->isGuest()) {
                $headerAdd['/cart'] = [
                    'title' => 'Корзина',
                    'icon' => 'cart.svg',
                ];
            }
            $headerAdd['/lk'] = [
                'title' => 'Личный кабинет',
                'icon' => 'user.svg',
            ];
            foreach($headerAdd as $href => $item):?>
                <div class="item mobile">
                    <a class="item__data" href="<?=$href?>">
                        <img src="/assets/icons/<?=$item['icon']?>" alt="<?=$item['title']?>" title="<?=$item['title']?>">
                        <?php if($href === '/cart'):?><span class="cart_number"><?=$_CART->getCount($_USER->GetId())?></span><?php endif;?>
                    </a>
                    <?php if($href === '/lk' &&!$_USER->isGuest()):?>
                        <div class="lk-menu shadowBox">
                            <?php if($_USER->isAdmin()):?>
                                <a href="/admin" class="item" title="Административная панель">Административная панель</a>
                            <?php endif;?>
                            <a href="/lk" class="item" title="Личный кабинет">Личный кабинет</a>
                            <a href="/lk/orders" class="item" title="Заказы">Заказы</a>
                            <span class="item lk-menu-exit" title="Выйти">Выйти</span>
                        </div>
                    <?php endif;?>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</nav>