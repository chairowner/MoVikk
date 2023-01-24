<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_ORDER = new Order($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$load = isset($_GET['load']) && !empty($_GET['load']) ? trim($_GET['load']) : null;
exit(json_encode($_ORDER->getOrders($load, $_USER->getId()), JSON_UNESCAPED_UNICODE)); // return