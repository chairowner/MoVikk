<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$count = isset($_POST['q']) ? (int) $_POST['q'] : 1;
exit(json_encode($_CART->add($_USER->GetId(), $productId, $count), JSON_UNESCAPED_UNICODE)); // return