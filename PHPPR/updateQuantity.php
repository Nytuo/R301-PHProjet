<?php
session_start();
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cart = $_SESSION['cart'];
    $cart[$id]['quantity'] = $_POST['quantity'];
    $_SESSION['cart'] = $cart;
    header("Location: cart.php");
}