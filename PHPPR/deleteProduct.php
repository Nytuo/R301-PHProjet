<?php
require_once "SqlApi.php";
$sql = new SqlApi();
$sql->deleteProduct($_GET['id']);
header("Location: adminZonne.php?message=delOk");