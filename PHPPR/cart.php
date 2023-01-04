<?php

session_start();

$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    $productObject = new product($cart['id'][0]);
    $cartList[] = array('product' => $productObject, 'quantity' => $cart['quantity'][0]);
}
function calculateTotal($cartList) {
    $total = 0;
    foreach ($cartList as $cart) {
        $total += $cart['product']->getPublicPrice() * $cart['quantity'];
    }
    return $total;
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
    $qty = $cart['quantity'];
    $cart = $cart['product'];
    echo "<div class='product'>";
    echo "<img src='" . $cart->getImage() . "' alt='product image'>";
    echo "<h3>" . $cart->getTitle() . "</h3>";
    echo "<p>" . $cart->getDescription() . "</p>";
    echo "<p>" . $cart->getPublicPrice() . "€</p>";
    echo "<p>Quantity: " . $cart->getQuantity() . "</p>";
    echo "<p>Category: " . $cart->getCategory() . "</p>";
    echo "<form action='removeProduct.php' method='post'>";
    echo "<input type='hidden' name='id' value='" . $cart->getRef() . "'>";
    echo "<input type='submit' value='Remove from cart'>";
    echo "</form>";
    echo "<input type='number' name='quantity' min='1' max='" . $qty . "' value='" . $cart->getQuantity() . "' onchange='updateQuantity(this.value, " . $cart->getRef() . ")'>";
    echo "</div>";
}
?>
<form action="">
<input type="submit" value="Payer via Paypal">
</form>
<p>Total: <?php echo calculateTotal($cartList) ?>€</p>
<p>Total (TTC): <?php echo calculateTotal($cartList) * 1.2 ?>€</p>
<p>TVA : <?php echo calculateTotal($cartList) * 0.2 ?>€</p>
<script>
    function updateQuantity(value,ref) {
        console.log(value);
        fetch('updateQuantity.php', {
            method: 'POST',
            body: JSON.stringify({
                quantity: value,
                id: ref
            })
        })
            .then(response => response.json())
            .then(data => console.log(data);window.location.reload()));
    }
</body>
</html>
