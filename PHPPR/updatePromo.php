<?php
session_start();
$valid_code = array(array('code' => 'CODE1', 'discount' => 0.1), array('code' => 'CODE2', 'discount' => 0.2), array('code' => 'CODE3', 'discount' => 0.3));
if (isset($_POST['promo'])) {
    foreach ($valid_code as $code) {
        if ($code['code'] == $_POST['promo']) {
            $_SESSION['discount'] = $code['discount'];
            $_SESSION['discount_code'] = $code['code'];
        }
    }
}
header("Location: cart.php");