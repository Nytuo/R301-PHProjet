<?php
require_once "SqlApi.php";
$sql = new SqlApi();

if (isset($_GET['product'])) {
    $sql->deleteProduct($_GET['id']);
    header("Location: adminZone.php?message=delOk");
}
if (isset($_GET['four'])) {
    $sql->deleteFour($_GET['id']);
    header("Location: adminZone.php?message=delOk");
}
if (isset($_GET['command'])) {
    $sql->deleteCommands($_GET['id']);
    header("Location: adminZone.php?message=delOk");
}
if (isset($_GET['client'])) {
    $sql->deleteClient($_GET['id']);
    header("Location: adminZone.php?message=delOk");
}