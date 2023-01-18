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
echo "<div class='cards-list'>";
foreach ($_SESSION['cart'] as $cart) {
    $DBProduct = $sql->getProduct($cart['id'][0]);
    $productObject = new product($DBProduct['ref'], $DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'], $DBProduct['description'], $DBProduct['image'], $DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'], $DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);
    $productObject->displayProduct();
    $cartList[] = array('product' => $productObject, 'quantity' => $cart['quantity']);
}
echo "</div>";
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
Mailer::sendMail($_SESSION['email'], "Facture", "Votre facture est de " . calculateTotal($cartList) . "€ et vous avez acheté : " . json_encode($json));
Mailer::sendMail("arnaud.beux.ab@gmail.com", "New order", "Une nouvelle commande a été passée de " . calculateTotal($cartList) . "€ par " . $_SESSION['email']);
resetCart();

?>

<div class="center">
    <img src="assets/images/merciAchat.gif" alt="">
    <p class="center">Comics Sans MS vous remercie pour votre achat !</p>
    <p class="center">Votre commande a bien été prise en compte, vous allez recevoir un mail contenant votre facture.</p>
</div>
<?php
require_once "footer.php";
?>
</body>