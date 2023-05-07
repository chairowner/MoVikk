<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_FAQs = new FAQ($conn);

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
        $response['info'] = "Указанный ID некорректен";
    }
} else {
    $response['status'] = false;
    $response['info'] = "Не указан ID";
}

if ($response['status']) $response = $_FAQs->Remove($id);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return