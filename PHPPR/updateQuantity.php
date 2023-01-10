<?php
session_start();
require_once "SqlApi.php";
$sql = new SqlApi();
$entityBody = file_get_contents('php://input');
$entityBody = json_decode($entityBody, true);
$ref = $entityBody['id'];
$quantity = $entityBody['quantity'];
if (isset($entityBody['gs'])){
    $sql->updateQuantity($quantity
        , $ref);
}else{
$cart = $_SESSION['cart'];
$cart[$ref]['quantity'] = $quantity;
$_SESSION['cart'] = $cart;
}
?>
