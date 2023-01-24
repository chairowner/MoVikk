<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_PRODUCT = new Product($conn);

$response = [
    'status' => true,
    'info' => []
];
$productId = 0;
$imageId = 0;

if (!$_USER->isAdmin()) $_PAGE->Redirect();

if (isset($_POST['productId'])) {
    $productId = (int) $_POST['productId'];
    if ($productId < 1) {
        $response['status'] = false;
        $response['info'][] = 'Указанный ID товара некорректен';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указан ID товара';
}

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
    $response = $_PRODUCT->setMainImage($productId, $imageId);
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return