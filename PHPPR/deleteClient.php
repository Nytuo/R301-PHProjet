<?php
require_once "SqlApi.php";
$sql = new SqlApi();
$sql->deleteClient($_GET['id']);
header("Location: adminZonne.php?message=delOk");