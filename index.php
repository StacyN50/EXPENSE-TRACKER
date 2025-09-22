<?php
require __DIR__.'/db.php';
$page = $_GET['page'] ?? 'entry';
include __DIR__.'/partials/header.php';
$allowed = ['entry','report','charts'];
if(!in_array($page,$allowed)) $page='entry';
include __DIR__ . "/pages/{$page}.php";
include __DIR__.'/partials/footer.php';
?>