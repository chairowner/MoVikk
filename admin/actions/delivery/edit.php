<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_DELIVERIES = new Delivery($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$name = null;
$link = null;

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} else {
    $response['status'] = false;
    $response['info'][] = 'ID не указан';
}

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (!isset($name)){
        $response['status'] = false;
        $response['info'][] = 'Название доставки не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите название';
}

if (isset($_POST['link'])) {
    $link = trim($_POST['link']) !== '' ? trim($_POST['link']) : null;
    if (!isset($link)){
        $response['status'] = false;
        $response['info'][] = 'Ссылка на доставку не может быть пустой';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите ссылку';
}

if ($response['status']) $response = $_DELIVERIES->Edit($id, $name, $link);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return