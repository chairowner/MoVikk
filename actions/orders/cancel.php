<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_ORDER = new Order($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$response = [
    'status' => false,
    'info' => []
];

$paymentId = null;
$idWhoClosed = 0;

if (!$_USER->isAdmin()) {
    if (isset($_POST['userId'])) {
        $idWhoClosed = (int) $_POST['userId'];
        if ($idWhoClosed < 1) {
            $response['info'][] = "Не указан ID пользователя";
        }
    }
}

if (isset($_POST['paymentId'])) {
    $paymentId = trim($_POST['paymentId']);
}

if (count($response['info'])) exit(json_encode($response, JSON_UNESCAPED_UNICODE));

$prepare = "SELECT `payment_id` FROM `{$_USER->GetTable()}` WHERE `payment_id` = :payment_id";
$execute = ['payment_id' => $paymentId];
if ($idWhoClosed > 0) {
    $prepare .= " `idWhoClosed` = :idWhoClosed";
    $execute['idWhoClosed'] = $idWhoClosed;
}
$query = $conn->prepare($prepare);
$query->execute($execute);
$query = $query->fetch(PDO::FETCH_ASSOC);
if (!isset($query) || empty($query)) {
    $response['info'][] = "Заказ не найден";
    exit(json_encode($response, JSON_UNESCAPED_UNICODE));
}

exit(json_encode($_ORDER->Cancel($paymentId, $idWhoClosed), JSON_UNESCAPED_UNICODE)); // return