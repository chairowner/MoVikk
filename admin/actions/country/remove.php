<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_COUNTRIES = new Country($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

exit(json_encode($_COUNTRIES->Remove($id), JSON_UNESCAPED_UNICODE)); // return