<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_PRODUCT = new Product($conn);

$response = [
    'status' => true,
    'info' => []
];

$imageId = 0;

if (!$_USER->isAdmin()) $_PAGE->Redirect();

if (isset($_POST['imageId'])) {
    $imageId = (int) $_POST['imageId'];
    if ($imageId < 1) {
        $response['status'] = false;
        $response['info'][] = 'Указанный ID изображения некорректен';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указан ID изображения';
}

if ($response['status']) {
    $response = [
        'status' => true,
        'info' => []
    ];
    $response = $_PRODUCT->DeleteImage($imageId);
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return