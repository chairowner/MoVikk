<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
  'status' => true,
  'info' => [],
];

$id = null;
$title = null;
$description = null;

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

if (isset($_POST['title'])) {
  $title = trim($_POST['title']) !== '' ? trim($_POST['title']) : null;
} else {
  $response['status'] = false;
  $response['info'][] = 'Заголовок не может быть пустым';
}

if (isset($_POST['description'])) {
  $description = trim($_POST['description']) !== '' ? trim($_POST['description']) : null;
}

if ($response['status']) {
  $query = $conn->prepare("UPDATE `{$_PAGE->GetTable()}` SET `title` = :title, `description` = :description WHERE `id` = :id");
  if ($query->execute(['title' => $title, 'description' => $description, 'id' => $id])) {
    $response['info'][] = "Значение успешно обновлено!";
  } else {
    $response['status'] = false;
    $response['info'][] = "Не удалось обновить значение";
  }
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return