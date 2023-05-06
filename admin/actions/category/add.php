<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => []
];

$name = null;
$href = null;
$images = [];

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (isset($name)) {
        $href = translitUrl($name);
    } else {
        $response['status'] = false;
        $response['info'][] = "Назвние категории не может быть пустым";
    }
} else {
    $response['status'] = false;
    $response['info'][] = "Не указано название категории";
}

if (isset($_FILES) && !empty($_FILES)) {
    $images = $_FILES['files'];
    for ($i = 0; $i < count($images['error']); $i++) {
        if ($images['error'][$i] !== 0 || !in_array($images['type'][$i], $_CATEGORIES->fileTypes)) {
            unset($images['error'][$i]);
            unset($images['name'][$i]);
            unset($images['size'][$i]);
            unset($images['tmp_name'][$i]);
            unset($images['type'][$i]);
        }
    }
} else {
    $images = [];
}

if ($response['status']) $response = $_CATEGORIES->Add($name, $href, $images);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return