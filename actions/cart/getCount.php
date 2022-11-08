<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$CART = new Cart($conn);

if ($USER->isGuest()) exit(0); // return

echo($CART->getCount($USER->getId())); // return