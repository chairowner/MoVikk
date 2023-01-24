<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_DELIVERIES = new Delivery($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = 0;

if (isset($_GET['id'])) {
    $id = trim($_GET['id']);
    if ($id === 'all') {
        $id = 'all';
    } else {
        $id = (int) $id;
        if ($id < 1) {
            $response['status'] = false;
            $response['info'][] = "Указанный ID доставки некорректен";
        }
    }
} else {
    $response['status'] = false;
    $response['info'][] = "Не указан ID доставки";
}

if ($response['status']) $response = $_DELIVERIES->Get($id);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return