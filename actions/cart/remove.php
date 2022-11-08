<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$CART = new Cart($conn);

if ($USER->isGuest()) $PAGE->redirect();

$productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$count = isset($_POST['q']) ? (int) $_POST['q'] : 1;
exit(json_encode($CART->remove($USER->getId(), $productId, $count), JSON_UNESCAPED_UNICODE)); // return