<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
exit(json_encode($_CART->checkProduct($_USER->GetId(), $productId), JSON_UNESCAPED_UNICODE)); // return