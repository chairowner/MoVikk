<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

$response = [
    'status' => true,
    'info' => []
];

$id = 0;

if (!$_USER->isAdmin()) $_PAGE->Redirect();

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    if ($id < 1) {
        $response['status'] = false;
        $response['info'][] = 'Указанный ID категории некорректен';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указан ID категории';
}

if ($response['status']) {
    $response = [
        'status' => true,
        'info' => []
    ];
    $response = $_CATEGORIES->DeleteImage($id);
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return