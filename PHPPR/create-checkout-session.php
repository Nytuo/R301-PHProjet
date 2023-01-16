<?php


require_once "SqlApi.php";
require_once "productClass.php";
$sql = new SqlApi();
// open session
session_start();
$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    $DBProduct = $sql->getProduct($cart['id'][0]);
    $productObject = new product($DBProduct['ref'], $DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'], $DBProduct['description'], $DBProduct['image'], $DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'], $DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);
//    $cartList[] = array('name' => $productObject->getTitle(), 'quantity' => $cart['quantity']);
    $cartList[] = array('price_data' => [
        'currency' => 'EUR',
        'product_data' => [
            'name' => $productObject->getTitle(),
        ],
        'unit_amount' => $productObject->getPublicPrice() * 100,
    ],
        'quantity' => $cart['quantity']);
}

print_r($cartList);

require 'payment/stripe-php-10.3.0/init.php';
// This is a public sample test API key.
// Don’t submit any personally identifiable information in requests made with this key.
// Sign in to see your own test API key embedded in code samples.
\Stripe\Stripe::setApiKey('sk_test_VePHdqKTYQjKNInc7u56JBrQ');

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost:80/PHPPR';

$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => $cartList,
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/success.php',
  'cancel_url' => $YOUR_DOMAIN . '/cancel.php',
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);