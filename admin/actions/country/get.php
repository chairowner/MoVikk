<?php
set_include_path('../../../');
require_once('includes/autoload.php');
$_USER = new User($conn);
$_COUNTRIES = new Country($conn);

if (!$_USER->isAdmin()) $_PAGE->Redirect();

$id = 0;
if (isset($_GET['id'])) {
    if (trim($_GET['id']) === 'all') $id = 'all';
    else $id = (int) $_GET['id'];
}
exit(json_encode($_COUNTRIES->Get($id), JSON_UNESCAPED_UNICODE)); // return