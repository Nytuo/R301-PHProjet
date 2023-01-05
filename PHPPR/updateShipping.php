<?php
//update shipping
session_start();
if (isset($_POST['shipping'])) {
    $_SESSION['shipping'] = $_POST['shipping'];
}
header("Location: cart.php");
?>