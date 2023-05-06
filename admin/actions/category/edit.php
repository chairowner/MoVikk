<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$name = null;
$href = null;
$images = [];

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} else {
    $response['status'] = false;
    $response['info'][] = 'ID не указан';
}

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (isset($name)) {
        $href = translitUrl($name);
    } else {
        $response['status'] = false;
        $response['info'][] = 'Введите название';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Введите название';
}

if (isset($_FILES) && !empty($_FILES)) {
    $images = $_FILES['files'];
    if (count($images['error']) > 0) {
        if ($images['error'][0] !== 0 || !in_array($images['type'][0], $_CATEGORIES->fileTypes)) {
            unset($images['error'][0]);
            unset($images['name'][0]);
            unset($images['size'][0]);
            unset($images['tmp_name'][0]);
            unset($images['type'][0]);
        }
    }
} else {
    $images = [];
}

if ($response['status']) $response = $_CATEGORIES->Edit($id, $name, $href, $images);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return