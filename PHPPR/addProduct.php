<?php
//add product to cart
session_start();
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $cart = $_SESSION['cart'];
    $cart[$id] = $id;
    $_SESSION['cart'] = $cart;
    header("Location: products.php");
}