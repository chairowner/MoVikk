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
$value = null;

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

if (isset($_POST['value'])) {
  $value = trim($_POST['value']) !== '' ? trim($_POST['value']) : null;
}

if ($response['status']) {
  $query = $conn->prepare("UPDATE `additional_fields` SET `value` = :value WHERE `id` = :id");
  if ($query->execute(['value' => $value, 'id' => $id])) {
    $response['info'][] = "Значение успешно обновлено!";
  } else {
    $response['status'] = false;
    $response['info'][] = "Не удалось обновить значение";
  }
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return