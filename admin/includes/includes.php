<?php
// редактируемая категория
$editCategory = explode('/', $_SERVER['PHP_SELF']);
$editCategory = basename($editCategory[count($editCategory) - 1], '.php');

$adminUrl = basename(dirname(__DIR__));

$action = null;
$editId = null;
if (isset($_GET['action'])) {
    if (in_array($_GET['action'], ['add', 'edit', 'remove'])) {
        $action = $_GET['action'];
        if ($action === "edit") {
            if (isset($_GET['id'])) {
                $editId = (int) $_GET['id'];
                if ($editId < 1) $editId = null; 
            } else {
                $action = null;
            }
        }
    }
}