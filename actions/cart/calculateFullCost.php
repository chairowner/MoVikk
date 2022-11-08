<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$CART = new Cart($conn);

if ($USER->isGuest()) $PAGE->redirect();

exit(json_encode($CART->calculateFullCost($USER->getId()), JSON_UNESCAPED_UNICODE)); // return