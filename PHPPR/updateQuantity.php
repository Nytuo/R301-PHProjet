<?php
session_start();

$entityBody = file_get_contents('php://input');
$entityBody = json_decode($entityBody, true);
$ref = $entityBody['id'];
$quantity = $entityBody['quantity'];
$cart = $_SESSION['cart'];
$cart[$ref]['quantity'] = $quantity;
$_SESSION['cart'] = $cart;

?>
