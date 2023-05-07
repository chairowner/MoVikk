<?php
set_include_path("./");
require_once('includes/includes.php');
set_include_path("../");
require_once('includes/autoload.php');
require_once('classes/php-pagination/Pagination.php');
$_PAGE = new Page($conn);
$_PAGE->description = "Авторизация";
$_PAGE->title = "Административная панель";
$_COMPANY = new Company($conn);
$_USER = new User($conn);

if (!$_USER->isGuest()) {
  $_PAGE->Redirect();
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <?= $_PAGE->GetHead($_USER->isGuest(), "{$_PAGE->title} - {$_PAGE->description}", $_PAGE->description) ?>
  <link rel="stylesheet" href="/assets/common/css/login-form.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="/classes/php-pagination/css/main.css">
  <script defer src="/assets/common/js/showLoad.js"></script>
  <script defer src="/assets/common/js/formatPrice.js"></script>
  <script defer src="assets/js/actions.js"></script>
  <script defer src="https://www.google.com/recaptcha/api.js?render=<?=reCAPTCHA_SITE_KEY?>"></script>
  <script defer src="/assets/common/js/loginForm.js"></script>
  <script defer src="/assets/common/js/overlayAct.js"></script>
</head>

<body style="height:100vh; width:100vw;">
  <div id="login-msgBox" class="position-fixed d-flex flex-column align-items-end" style="gap:20px;right:0;bottom:0;margin:20px;z-index:1000000000;width:calc(100% - 40px);"></div>
  <div id="login-form-overlay" class="position-relative">
      <div class="loader1 position-absolute"></div>
      <div id="login-form" class="shadowBox position-relative">
          <a id="close-login-form" href="/">X</a>
          <form id="login-form-auth" class="item active">
              <p class="login-form-title">Авторизация</p>
              <input class="field shadowBox w-100" type="email" name="email" placeholder="E-mail" title="E-mail" required>
              <div class="login-form-passwordBlock">
                  <input class="field shadowBox w-100" type="password" name="password" placeholder="Пароль" title="Пароль" required>
                  <div class="h-100 d-flex align-items-center"><div class="passView close" title="Показать пароль"><span class="passView-eyelid"></span></div></div>
              </div>
              <p class="text-center"><a href="recoverPassword" class="primary cursor-pointer">Восстановить пароль</a></p>
              <input class="button shadowBox w-100" type="submit" value="Войти">
              <input type="hidden" class="g-recaptcha-response" name="g-recaptcha-response">
          </form>
          <p class="grecaptcha-badge-text w-100 text-center">Сайт защищён reCAPTCHA.<br>К нему применяются <a target="_blank"href="https://policies.google.com/privacy">Политика конфиденциальности</a> и <a target="_blank"href="https://policies.google.com/terms">Условия предоставления услуг</a> Google.</p>
      </div>
  </div>
  <div id="mainMessageBox"></div>
</body>
</html>