<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_FAQs = new FAQ($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$id = null;
$question = null;
$answer = null;

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

if (isset($_POST['question'])) {
    $question = trim($_POST['question']) !== '' ? trim($_POST['question']) : null;
    if (!isset($question)){
        $response['status'] = false;
        $response['info'][] = 'Вопрос не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите вопрос';
}

if (isset($_POST['answer'])) {
    $answer = trim($_POST['answer']) !== '' ? trim($_POST['answer']) : null;
    if (!isset($answer)){
        $response['status'] = false;
        $response['info'][] = 'Ответ не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Укажите ответ';
}

if ($response['status']) $response = $_FAQs->Edit($id, $question, $answer);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return