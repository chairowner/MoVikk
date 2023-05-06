<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_ORDER = new Order($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$response = [
    'status' => true,
    'info' => []
];
$separator = ';';

if (!isset($_POST['action']) || $_POST['action'] !== "edit") {
    $response['status'] = false;
    $response['info'][] = 'Ошибка запроса';
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}
unset($_POST['action']);

// true - 'Ожидание оплаты','Обработка платежа','Сборка заказа','Отправлен','Доставлен','Закрыт','Отменён'
// false - 'Сборка заказа','Отправлен','Доставлен','Закрыт','Отменён'
$statusArray = $_ORDER->GetStatusArray("progress");
$currentStatus = null;

$userId = $_USER->GetId();
$orderId = null;
$newStatus = null;
$deliveryId = null;
$tracking = null;
$adminComment = null;

if (isset($_POST['orderId'])) {
    $orderId = (int) $_POST['orderId'];
    if ($orderId > 0) {
        $currentStatus = $_ORDER->GetStatus($orderId);
    } else {
        $response['status'] = false;
        $response['info'][] = "Неверный ID заказа";
    }
} else {
    $response['status'] = false;
    $response['info'][] = "Укажите ID заказа";
}

if (isset($_POST['newStatus'])) {
    $newStatus = trim($_POST['newStatus']);
    if (!in_array($newStatus, $statusArray)) {
        if (isset($currentStatus) && $currentStatus === "Ожидание оплаты") {
            $newStatus = $currentStatus;
        } else {
            $response['status'] = false;
            $response['info'][] = "Неверный статус";
        }
    }
} elseif (isset($currentStatus) && $currentStatus === "Ожидание оплаты") {
    $newStatus = $currentStatus;
} else {
    $response['status'] = false;
    $response['info'][] = "Укажите статус заказа";
}

if (isset($_POST['deliveryId'])) {
    $deliveryId = (int) $_POST['deliveryId'];
    if ($deliveryId === 0) {
        $deliveryId = null;
    }
}

if (isset($_POST['tracking'])) {
    $tracking = trim($_POST['tracking']);
    if (empty($tracking)) $tracking = null;
}

if (isset($_POST['adminComment'])) {
    $adminComment = trim($_POST['adminComment']);
    if (empty($adminComment)) $adminComment = null;
}

if ($response['status']) {
    $response = $_ORDER->Change($userId, $orderId, $newStatus, $deliveryId, $tracking, $adminComment, false);
}

if (DEBUG_MODE) $response['POST'] = $_POST;

exit(json_encode($response, JSON_UNESCAPED_UNICODE)); // return