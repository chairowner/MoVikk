<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$name = null;
$href = null;
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} else {
    $response['status'] = false;
    $response['info'][] = 'Идентификатор не указан';
}
if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (isset($name)) $href = translitUrl($name);
    else {
        $response['status'] = false;
        $response['info'][] = 'Введите название';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Введите название';
}

if ($response['status']) $response = $_CATEGORIES->edit($id, $name, $href);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return