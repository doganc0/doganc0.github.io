<?php
require_once __DIR__ . '/../../../bootstrap.php';

$db = Database::getInstance();
$auth = new Auth($db);

if (!$auth->check()) {
    header('Location: login.php');
    exit;
}
