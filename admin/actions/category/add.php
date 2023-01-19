<?php
set_include_path('../../../');
require_once('includes/autoload.php');
require_once('functions/translitUrl.php');
$_USER = new User($conn);
$_CATEGORIES = new Categories($conn);

if (!$_USER->isAdmin()) $_PAGE->redirect();

$name = null;
$href = null;
if (isset($_POST['name'])) {
    $name = trim($_POST['name']) !== '' ? trim($_POST['name']) : null;
    if (isset($name)) $href = translitUrl($name);
}
exit(json_encode($_CATEGORIES->add($name, $href), JSON_UNESCAPED_UNICODE)); // return