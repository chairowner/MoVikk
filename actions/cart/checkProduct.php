<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$CART = new Cart($conn);

if ($USER->isGuest()) $PAGE->redirect();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
exit(json_encode($CART->checkProduct($USER->getId(), $productId), JSON_UNESCAPED_UNICODE)); // return