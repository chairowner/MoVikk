<?php
define('reCAPTCHA_SITE_KEY', '6LcQsRAiAAAAALV4CnipHkz_FlVwo3MnZro7655c');
define('reCAPTCHA_SECRET_KEY', '6LcQsRAiAAAAAMa-VOyLPNj34AY-Xv1YlMeyLJ9s');

# перенос глобальной переменной $_SESSION в локальную $session
include_once('includes/session.php');
# подключение к базе данных
include_once('includes/connection.php');

spl_autoload_register('classAutoloader');
function classAutoloader($className) {
    $path = get_include_path() == '.' ? './' : get_include_path();
    $path .= 'classes/';
    $extension = '.php';
    $fullPath = $path.$className.$extension;
    if (!file_exists($fullPath)) return false;
    require_once($fullPath);
}