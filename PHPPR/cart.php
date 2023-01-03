<?php

session_start();

//for each element in the cart array
$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    //create a product object
    $productObject = new product($cart['id']);
    $productObject->setName($cart['name']);
    $productObject->setPrice($cart['price']);
    $productObject->setDescription($cart['description']);
    $productObject->setImage($cart['image']);
    $productObject->setQuantity($cart['quantity']);
    $productObject->setCategory($cart['category']);
    $cartList[] = $productObject;
}


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>Le chariot</h1>
<?php
foreach ($cartList as $cart) {
    echo "<div class='product'>";
    echo "<img src='" . $cart->getImage() . "' alt='product image'>";
    echo "<h3>" . $cart->getName() . "</h3>";
    echo "<p>" . $cart->getDescription() . "</p>";
    echo "<p>" . $cart->getPrice() . "€</p>";
    echo "<p>Quantity: " . $cart->getQuantity() . "</p>";
    echo "<p>Category: " . $cart->getCategory() . "</p>";
    echo "<form action='removeProduct.php' method='post'>";
    echo "<input type='hidden' name='id' value='" . $cart->getId() . "'>";
    echo "<input type='submit' value='Remove from cart'>";
    echo "</form>";
    echo "</div>";
}
?>
<form action="">
<input type="submit" value="Payer via Paypal">
</form>
</body>
</html>
