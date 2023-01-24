<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_COUNTRIES = new Country($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$name = null;
if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
} else {
    $response['status'] = false;
    $response['info'][] = 'Идентификатор не указан';
}
if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (!isset($name)){
        $response['status'] = false;
        $response['info'][] = 'Введите название';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Введите название';
}

if ($response['status']) $response = $_COUNTRIES->Edit($id, $name);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return