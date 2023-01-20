<?php
require_once "head.php";
require_once "header.php";
require_once "productClass.php";
require_once "SqlApi.php";
require_once "Mailer.php";
?>
<body>
<?php
$sql = new SqlApi();

$cartList = array();
$prodList = array();
foreach ($_SESSION['cart'] as $cart) {
    $DBProduct = $sql->getProduct($cart['id']);
    $productObject = new product($DBProduct['ref'], $DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'], $DBProduct['description'], $DBProduct['image'], $DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'], $DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);
    $prodList[] = $productObject;
    $cartList[] = array('product' => $productObject, 'quantity' => $cart['quantity']);
}
function resetCart()
{
    unset($_SESSION['cart']);
    unset($_SESSION['discount']);
    unset($_SESSION['shipping']);
    unset($_SESSION['total']);
}


function calculateTotal($cartList)
{
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

$userId=$sql->getUserId($_SESSION['email'])["id"];
$json=array();
foreach($cartList as $cart){
    $json[]=array('products'=>$cart['product']->getId(),'quantity'=>$cart['quantity'],'price'=>$cart['product']->getPublicPrice());
}

$sql->insertFacturation($userId,json_encode($json),calculateTotal($cartList));
foreach ($cartList as $cart) {
    $sql->updateQuantity( $cart['product']->getQuantity() - $cart['quantity'],$cart['product']->getId());
}
$paidProducts = array();
foreach ($cartList as $cart) {
    $paidProducts[] = $cart['product']->getTitle() . " x" . $cart['quantity'] . " à " . $cart['product']->getPublicPrice() . "€/unité soit " . $cart['product']->getPublicPrice() * $cart['quantity'] . "€";
}
Mailer::sendMail($_SESSION['email'], "Comics Sans MS -- Facture", "Comics Sans MS vous remercie de votre achat !\nLe montant de votre facture est de " . calculateTotal($cartList) . "€ et vous avez acheté les articles suivants: \n" . implode("\n", $paidProducts));  
Mailer::sendMail("arnaud.beux.ab@gmail.com", "New order", "Une nouvelle commande a été passée de " . calculateTotal($cartList) . "€ par " . $_SESSION['email']."\nLes articles achetés sont les suivants: \n" . implode("\n", $paidProducts));
resetCart();

?>
<div class="center spaceXUp">
    <img src="assets/images/merciAchat.gif" alt="">
    <p class="center">Comics Sans MS vous remercie pour votre achat !</p>
    <p class="center">Votre commande a bien été prise en compte, vous allez recevoir un mail contenant votre facture.</p>
</div>
<div class="cards-list">
    <?php 
    foreach ($prodList as $prod){
        $prod->displayProduct();
    }
    ?>
    </div>
<?php



require_once "footer.php";
?>
</body>