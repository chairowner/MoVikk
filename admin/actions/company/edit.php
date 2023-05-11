<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_COMPANY = new Company($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
  'status' => true,
  'info' => [],
];

$name = null;
$phone = null;
$inn = null;
$ogrn = null;
$pay_acc = null;
$bik = null;
$ks = null;
$place = null;

if (isset($_POST['name'])) {
  $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
  if (!isset($name)) {
    $response['status'] = false;
    $response['info'][] = 'Наименование компании не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'Наименование компании не может быть пустым';
}

if (isset($_POST['phone'])) {
  $phone = trim($_POST['phone']) !== '' ? trim($_POST['phone']) : null;
  if (!isset($phone)) {
    $response['status'] = false;
    $response['info'][] = 'Заголовок не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'Заголовок не может быть пустым';
}

if (isset($_POST['inn'])) {
  $inn = trim($_POST['inn']) !== '' ? trim($_POST['inn']) : null;
  if (!isset($inn)) {
    $response['status'] = false;
    $response['info'][] = 'ИНН не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'ИНН не может быть пустым';
}

if (isset($_POST['ogrn'])) {
  $ogrn = trim($_POST['ogrn']) !== '' ? trim($_POST['ogrn']) : null;
  if (!isset($ogrn)) {
    $response['status'] = false;
    $response['info'][] = 'ОГРН не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'ОГРН не может быть пустым';
}

if (isset($_POST['pay_acc'])) {
  $pay_acc = trim($_POST['pay_acc']) !== '' ? trim($_POST['pay_acc']) : null;
  if (!isset($pay_acc)) {
    $response['status'] = false;
    $response['info'][] = 'Номер счёта не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'Номер счёта не может быть пустым';
}

if (isset($_POST['bik'])) {
  $bik = trim($_POST['bik']) !== '' ? trim($_POST['bik']) : null;
  if (!isset($bik)) {
    $response['status'] = false;
    $response['info'][] = 'БИК не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'БИК не может быть пустым';
}

if (isset($_POST['ks'])) {
  $ks = trim($_POST['ks']) !== '' ? trim($_POST['ks']) : null;
  if (!isset($ks)) {
    $response['status'] = false;
    $response['info'][] = 'КС не может быть пустым';
}
} else {
  $response['status'] = false;
  $response['info'][] = 'КС не может быть пустым';
}

if (isset($_POST['place'])) {
  $place = trim($_POST['place']) !== '' ? trim($_POST['place']) : null;
}

if ($response['status']) {
  $query = $conn->prepare("UPDATE `{$_COMPANY->GetTable()}` SET `name` = :name, `phone` = :phone, `inn` = :inn, `ogrn` = :ogrn, `pay_acc` = :pay_acc, `bik` = :bik, `ks` = :ks, `place` = :place");
  if ($query->execute([ 'name' => $name, 'phone' => $phone, 'inn' => $inn, 'ogrn' => $ogrn, 'pay_acc' => $pay_acc, 'bik' => $bik, 'ks' => $ks, 'place' => $place])) {
    $response['info'][] = "Значение успешно обновлено!";
  } else {
    $response['status'] = false;
    $response['info'][] = "Не удалось обновить значение";
  }
}

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return