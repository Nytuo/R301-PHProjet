<?php
session_start();
$valid_code = array(array('code' => 'CODE1', 'discount' => 0.1), array('code' => 'CODE2', 'discount' => 0.2), array('code' => 'CODE3', 'discount' => 0.3));

if (isset($_POST['shipping'])) {
//apply shipping fees
    $_SESSION['shipping'] = $_POST['shipping'];
    header("Location: cart.php");
} else if (isset($_POST['promo'])) {
    //apply discount
    foreach ($valid_code as $code) {
        if ($code['code'] == $_POST['promo']) {
            $_SESSION['discount'] = $code['discount'];
            $_SESSION['discount_code'] = $code['code'];
        }
    }
    header("Location: cart.php");
} else {
    //modify desired quantity
    $entityBody = file_get_contents('php://input');
    $entityBody = json_decode($entityBody, true);
    $ref = $entityBody['id'];
    $quantity = $entityBody['quantity'];
    $cart = $_SESSION['cart'];
    $cart[$ref]['quantity'] = $quantity;
    $_SESSION['cart'] = $cart;
}