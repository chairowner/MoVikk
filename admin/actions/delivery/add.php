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

$name = null;
$link = null;

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (!isset($name)) {
        $response['status'] = false;
        $response['info'][] = 'Имя страны-изготовителя не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указано имя страны-изготовителя';
}

if (isset($_POST['link'])) {
    $link = trim($_POST['link']) !== '' ? trim($_POST['link']) : null;
    if (!isset($link)) {
        $response['status'] = false;
        $response['info'][] = 'Имя страны-изготовителя не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указано имя страны-изготовителя';
}

if ($response['status']) $response = $_DELIVERIES->Add($name, $link);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return