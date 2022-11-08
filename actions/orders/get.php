<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$ORDER = new Order($conn);

if ($USER->isGuest()) $PAGE->redirect();

$load = isset($_GET['load']) && !empty($_GET['load']) ? trim($_GET['load']) : null;
exit(json_encode($ORDER->getOrders($load, $USER->getId()), JSON_UNESCAPED_UNICODE)); // return