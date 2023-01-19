<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_USER->logout();