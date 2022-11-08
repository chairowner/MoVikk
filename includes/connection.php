<?php
# отключение вывода предупреждений и ошибок
error_reporting(E_ALL);

# адрес сервера (имя хоста или IP-адрес)
define('DATABASE_HOST', "localhost");
# имя пользователя базы данных
define('DATABASE_USER', "cv33474_movikk");
# пароль базы  данных
define('DATABASE_PASS', "PyZbSN6N");
# имя базы данных
define('DATABASE_NAME', "cv33474_movikk");

try /* создание подключения */ {
	$conn = new PDO(
		'mysql:dbname='.DATABASE_NAME.';host='.DATABASE_HOST,
		DATABASE_USER, DATABASE_PASS
	);
} catch (PDOException $e) /* обработка ошибки */ {
	// die($e->getMessage());
	Header('Location: /503');
	exit;
}