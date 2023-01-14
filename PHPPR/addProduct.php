<?php
session_start();
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cart = $_SESSION['cart'];
    $cart[$id] = array('id' => $id, 'quantity' => 1);
    $_SESSION['cart'] = $cart;
    header("Location: cart.php");
}