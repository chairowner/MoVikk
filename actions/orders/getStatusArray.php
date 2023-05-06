<?php
set_include_path('../../');
require_once('includes/autoload.php');
$_PAGE = new Page($conn);
$_USER = new User($conn);
$_ORDER = new Order($conn);

if ($_USER->isGuest()) $_PAGE->Redirect();

$type = 'new';

if (isset($_GET['type']) && !empty($_GET['type']) && in_array($_GET['type'], ['all','process','payment_process','new','close'])) {
    if ($_USER->isAdmin()) {
        $type = $_GET['type'];
    }
}

exit(json_encode($_ORDER->GetStatusArray($type), JSON_UNESCAPED_UNICODE)); // return