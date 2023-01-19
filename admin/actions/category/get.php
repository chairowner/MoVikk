<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->redirect();

$id = 0;
if (isset($_GET['id'])) {
    if (trim($_GET['id']) === 'all') $id = 'all';
    else $id = (int) $_GET['id'];
}
exit(json_encode($_CATEGORIES->get($id), JSON_UNESCAPED_UNICODE)); // return