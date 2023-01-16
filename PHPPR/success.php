<?php
require_once "head.php";
require_once "header.php";
require_once "productClass.php";
require_once "SqlApi.php";
$sql = new SqlApi();

$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    $DBProduct = $sql->getProduct($cart['id'][0]);
    print_r($DBProduct);
    $productObject = new product($DBProduct['ref'], $DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'], $DBProduct['description'], $DBProduct['image'], $DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'], $DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);
    $cartList[] = array('product' => $productObject, 'quantity' => $cart['quantity']);
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

$userId=$sql->getUserId($_SESSION['email']);
$json=array();
foreach($cartList as $cart){
    $json[]=array('products'=>$cart['product']->getId(),'quantity'=>$cart['quantity'],'price'=>$cart['product']->getPublicPrice());
}

$sql->insertFacturation($userId,json_encode($json),calculateTotal($cartList));
