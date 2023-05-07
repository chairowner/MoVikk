<?php
# Версия приложения
define('APP_VERSION', '1.0.0');
# Режим разрабоки
define('DEBUG_MODE', true);
# Публичный reCAPTCHA ключ
define('reCAPTCHA_SITE_KEY', '6LdyVx8jAAAAAFpzmmmkEB4Hr_pAanZrZ-YxMz3L');
# Секретный reCAPTCHA ключ
define('reCAPTCHA_SECRET_KEY', '6LdyVx8jAAAAAGHxDeQzwk69BZTK-PKAFaIki01p');

# перенос глобальной переменной $_SESSION в локальную $session
include_once('includes/session.php');
# подключение к базе данных
include_once('includes/connection.php');

# Авто-подгрузка классов
spl_autoload_register('classAutoloader');
function classAutoloader($className) {
    $path = get_include_path() == '.' ? './' : get_include_path();
    $path .= 'classes/';
    $extension = '.php';
    $fullPath = $path.$className.$extension;
    if (!file_exists($fullPath)) return false;
    require_once($fullPath);
}