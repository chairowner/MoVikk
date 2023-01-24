<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$name = null;
$href = null;

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (isset($name)) {
        $href = translitUrl($name);
    } else {
        $response['stauts'] = false;
        $response['info'] = "Назвние категории не может быть пустым";
    }
} else {
    $response['stauts'] = false;
    $response['info'] = "Не указано название категории";
}

if ($response['stauts']) $response = $_CATEGORIES->Add($name, $href);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return