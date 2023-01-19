<?php
set_include_path(".");
require_once('includes/autoload.php');
$_COMPANY = new Company($conn);
$_USER = new User($conn);
$_CART = new Cart($conn);

$result = [];
echo(json_encode($result,JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));