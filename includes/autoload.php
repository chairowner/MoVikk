<?php
# перенос глобальной переменной $_SESSION в локальную $session
include_once('includes/session.php');
# подключение к базе данных
include_once('includes/connection.php');
# подключение класса страницы
require_once('classes/Page.php');
# подключение класса компании
require_once('classes/Company.php');
# подключение класса пользователя
require_once('classes/User.php');
# подключение функции форматирования цены
require_once('functions/formatPrice.php');
# подключение функции вывода массива/объекта в формат JSON (develop)
require_once('functions/dev-getJSON.php');