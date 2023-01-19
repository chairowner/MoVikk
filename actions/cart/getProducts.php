<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) $_PAGE->redirect();

exit(json_encode($_CART->getProducts($_USER->getId()), JSON_UNESCAPED_UNICODE)); // return