<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_INSTRUCTIONS = new Instruction($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$name = null;
$text = null;

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    if ($id < 1) {
        $response['status'] = false;
        $response['info'][] = 'Указанный ID некорректен';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'ID не указан';
}

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (!isset($name)){
        $response['status'] = false;
        $response['info'][] = 'Вопрос не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите вопрос';
}

if (isset($_POST['text'])) {
    $text = trim($_POST['text']) !== '' ? trim($_POST['text']) : null;
    if (!isset($text)){
        $response['status'] = false;
        $response['info'][] = 'Ответ не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите ответ';
}

if ($response['status']) $response = $_FAQs->Edit($id, $name, $text);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return