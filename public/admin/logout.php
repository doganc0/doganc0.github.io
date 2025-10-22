<?php
require_once __DIR__ . '/../../bootstrap.php';

$db = Database::getInstance();
$auth = new Auth($db);
$auth->logout();

header('Location: login.php');
exit;
