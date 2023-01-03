<?php

//remove product from cart
session_start();
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cart = $_SESSION['cart'];
    unset($cart[$id]);
    $_SESSION['cart'] = $cart;
    header("Location: cart.php");
}
