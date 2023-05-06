<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_ORDER = new Order($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$load = isset($_GET['load']) && !empty($_GET['load']) ? trim($_GET['load']) : null;
exit(json_encode($_ORDER->GetOrders($load, $_USER->GetId()), JSON_UNESCAPED_UNICODE)); // return