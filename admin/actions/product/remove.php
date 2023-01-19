<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_PRODUCT = new Product($conn);

if (!$_USER->isAdmin()) $_PAGE->redirect();

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

exit(json_encode($_PRODUCT->remove($id), JSON_UNESCAPED_UNICODE)); // return