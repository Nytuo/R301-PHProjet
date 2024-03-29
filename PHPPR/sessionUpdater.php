<?php
session_start();
$valid_code = array(array('code' => 'CODE1', 'discount' => 1), array('code' => 'CODE2', 'discount' => 2), array('code' => 'CODE3', 'discount' => 3));

if (isset($_POST['shipping'])) {
    $_SESSION['shipping'] = $_POST['shipping'];
    header("Location: cart.php");
} else if (isset($_POST['promo'])) {
    foreach ($valid_code as $code) {
        if ($code['code'] == $_POST['promo']) {
            $_SESSION['discount'] = $code['discount'];
            $_SESSION['discount_code'] = $code['code'];
        }
    }
    header("Location: cart.php");
} else {
    $entityBody = file_get_contents('php://input');
    $entityBody = json_decode($entityBody, true);
    $ref = $entityBody['id'];
    $quantity = $entityBody['quantity'];
    $cart = $_SESSION['cart'];
    $cart[$ref]['quantity'] = $quantity;
    $_SESSION['cart'] = $cart;
}