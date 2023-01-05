<?php

session_start();
require_once "head.php";
require_once "header.php";

require_once "productClass.php";

$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    $productObject = new product($cart['id'][0]);
    $cartList[] = array('product' => $productObject, 'quantity' => $cart['quantity']);
}
function calculateTotal($cartList) {
    $total = 0;
    foreach ($cartList as $cart) {
        $total += $cart['product']->getPublicPrice() * $cart['quantity'];
    }
    if (isset($_SESSION['discount'])) {
        $total = $total - ($_SESSION['discount']);
    }
    if (isset($_SESSION['shipping'])) {
        $total = $total + $_SESSION['shipping'];
    }
    return $total;
}


?>


<body>
<main>


<h1>Le chariot</h1>
<?php
if (count($cartList) > 0) {
    foreach ($cartList as $cart) {
        $qty = $cart['quantity'];
        $cart = $cart['product'];
        echo "<div class='product_cart'>";
        echo "<img src='" . $cart->getImage() . "' alt='product image' class='product_img_cart'>";
        echo "<span class='cart_title'><a href='product.php?id=" . $cart->getRef() . "'>" . $cart->getTitle() . "</a></span>";
        echo "<span class='cart_price price'>" . $cart->getPublicPrice() . "€</span>";
        echo "<input type='number' name='quantity' min='1' max='" . $cart->getQuantity() . "' value='" . $qty . "' onchange='updateQuantity(this.value, " . $cart->getRef() . ")'>";
        echo "<form action='removeProduct.php' method='post'>";
        echo "<input type='hidden' name='id' value='" . $cart->getRef() . "'>";
        echo "<input type='submit' class='waves-effect btn' value='Supprimer'>";
        echo "</form>";
        echo "</div>";
    }
    //code promo
    echo "<div class='promo'>";
    echo "<form action='updatePromo.php' method='post'>";
    echo "<input type='text' name='promo' id='submitCP'  placeholder='Code promo'>";
    echo "<input type='submit' class='waves-effect btn' value='Valider'>";
    echo "</form>";
    echo "</div>";
    //frais de port en fonction de l'adresse
    echo "<div class='shipping'>";
    echo "<form action='updateShipping.php' method='post'>";
    echo "<div class='input-field col s12'>";
    echo "<select name='shipping'>";
    echo "<option value='0' selected disabled>Calculer les frais de port</option>";
    echo "<option value='5.9'>France</option>";
    echo "<option value='10'>Europe</option>";
    echo "<option value='20'>International</option>";
    echo "<label>Shipping</label>";
    echo "</select>";
    echo "<input type='submit' class='waves-effect btn' value='Valider'>";
    echo "</form>";
    echo "</div>";
    if (isset($_SESSION['discount'])) {
        echo "<p id='discount'>Code promo : " . $_SESSION['discount'] . "€</p>";
        echo "<script>document.getElementById('submitCP').value = '".$_SESSION['discount_code']."'</script>";
    }
    if (isset($_SESSION['shipping'])) {
        echo "<p id='shipping'>Frais de port : " . $_SESSION['shipping'] . "€</p>";
    }
    echo "<p id='totalHT'>Total Hors-Taxes : " . calculateTotal($cartList) - (calculateTotal($cartList) * 0.2) . "€</p>";
    echo "<p id='TVA'>dont " . calculateTotal($cartList) * 0.2 . "€ de TVA (20%)</p>";
    echo "<p id='totalTTC'>Total TTC : " . calculateTotal($cartList) . "€</p>";
echo "<form action=''>
<input type='submit' class='btn waves-effect' value='Payer via Paypal'>
</form>";
} else {
    echo "<p>Le chariot est vide...</p>";
    echo "<a href='products.php' class='btn waves-effect'>Retour aux produits</a>";

}

?>

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
            .then(response => response.text())
            .then(data => {
               window.location.reload();
            });
    }


    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        var instances = M.FormSelect.init(elems, {});
    });
</script>
</main>
<?php
require_once "footer.php";
?>
</body>
