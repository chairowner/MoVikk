<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

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
            $response['info'][] = "Указанный ID некорректен";
        }
    }
} else {
    $response['status'] = false;
    $response['info'][] = "Не указан ID";
}

if ($response['status']) $response = $_CATEGORIES->Get($id);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return