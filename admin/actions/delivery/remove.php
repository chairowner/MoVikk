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

$id = null;

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    if ($id < 1) {
        $response['status'] = false;
        $response['info'] = "Указанный ID доставки некорректен";
    }
} else {
    $response['status'] = false;
    $response['info'] = "Не указан ID доставки";
}

if ($response['status']) $response = $_DELIVERIES->Remove($id);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return