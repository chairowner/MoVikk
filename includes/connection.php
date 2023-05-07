<?php
# отключение вывода предупреждений и ошибок
if (DEBUG_MODE) error_reporting(E_ALL);
else error_reporting(0);

# адрес сервера (имя хоста или IP-адрес)
define('DATABASE_HOST', "");
# имя пользователя базы данных
define('DATABASE_USER', "");
# пароль базы  данных
define('DATABASE_PASS', "");
# имя базы данных
define('DATABASE_NAME', "");

try /* создание подключения */ {
	$conn = new PDO(
		'mysql:dbname='.DATABASE_NAME.';host='.DATABASE_HOST,
		DATABASE_USER, DATABASE_PASS,
		[PDO::ATTR_PERSISTENT => true]
	);
} catch (PDOException $e) /* обработка ошибки */ {
	// die($e->getMessage());
	// Header('Location: /503');
	if (DEBUG_MODE) echo("<p>{$e->getMessage()}</p>");
	require_once("503.php");
	exit;
}