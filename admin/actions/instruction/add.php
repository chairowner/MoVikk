<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_INSTRUCTIONS = new Instruction($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => [],
];

$name = null;
$text = null;
$video = null;

if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (!isset($name)) {
        $response['status'] = false;
        $response['info'][] = 'Наименование инструкции не может быть пустым';
    }
} else {
    $response['status'] = false;
    $response['info'][] = 'Не указан вопрос';
}

if (isset($_POST['text'])) {
    $text = trim($_POST['text']) !== '' ? trim($_POST['text']) : null;
}

if (isset($_FILES['video']) && !empty($_FILES['video'])) {
    if (is_array($_FILES['video']['name'])) {
        $response['status'] = false;
        $response['info'][] = "Видео может быть только одно";
    } else {
        if ($_FILES['video']['error'] === 0 && key_exists($_FILES['video']['type'], $_INSTRUCTIONS->fileTypes)) {
            $video = $_FILES['video'];
        }
    }
}

if (!isset($video) && !isset($text)) {
    $response['status'] = false;
    $response['info'][] = "Для занесения инструкции либо введите текст, либо заргузите видео1";
}

if ($response['status']) $response = $_INSTRUCTIONS->Add($name, $text, $video);

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return