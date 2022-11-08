<?php
set_include_path('../../');
require_once('includes/autoload.php');
$USER = new User($conn);
$USER->logout();