<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_CART = new Cart($conn);

if ($_USER->isGuest()) exit(0); // return

echo($_CART->getCount($_USER->getId())); // return