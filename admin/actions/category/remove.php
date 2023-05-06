<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

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
        $response['info'] = "Указанный ID категории некорректен";
    }
} else {
    $response['status'] = false;
    $response['info'] = "Не указан ID категории";
}

if ($response['status']) $response = $_CATEGORIES->Remove($id);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return