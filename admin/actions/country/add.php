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

$name = null;
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

if ($response['status']) $response = $_COUNTRIES->Add($name);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return