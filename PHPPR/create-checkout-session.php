<?php


require_once "SqlApi.php";
require_once "productClass.php";
$sql = new SqlApi();
ini_set('display_errors', 1);
// open session
session_start();
$cartList = array();
foreach ($_SESSION['cart'] as $cart) {
    print_r($cart);
    $DBProduct = $sql->getProduct($cart['id']);
    $productObject = new product($DBProduct['ref'], $DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'], $DBProduct['description'], $DBProduct['image'], $DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'], $DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);
//    $cartList[] = array('name' => $productObject->getTitle(), 'quantity' => $cart['quantity']);
    $cartList[] = array('price_data' => [
        'currency' => 'EUR',
        'product_data' => [
            'name' => $productObject->getTitle(),
            'images' => [$productObject->getImage() == null ? 'https://comicssansms.pq.lu/assets/images/no-image.webp' : $productObject->getImage()],
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

$YOUR_DOMAIN = 'http://comicssansms.pq.lu/';
$shipping_rate = \Stripe\ShippingRate::create([
    'display_name' => 'Comics Sans Shipping',
    'type' => 'fixed_amount',
    'fixed_amount' => [
      'amount' => $_SESSION['shipping'] != null ? $_SESSION['shipping']*100 : 20*100,
      'currency' => 'EUR',
    ],
    'delivery_estimate' => [
      'minimum' => [
        'unit' => 'business_day',
        'value' => 1,
      ],
      'maximum' => [
        'unit' => 'business_day',
        'value' => 7,
      ],
    ],
  ]);
  if ($_SESSION['discount_code'] != null){
    
    try{
      $coupon = \Stripe\Coupon::retrieve($_SESSION['discount_code']);
    }catch (\Exception $e){
      $coupon = \Stripe\Coupon::create([
          'amount_off' => $_SESSION['discount']*1000,
          'currency' => 'eur',
          'duration' => 'forever',
          'id' => $_SESSION['discount_code'],
        ]);
    }
  }
  $makeSession = array();
  $makeSession['line_items'] = $cartList;
  $makeSession['mode'] = 'payment';
  $makeSession['success_url'] = $YOUR_DOMAIN . '/success.php';
  $makeSession['cancel_url'] = $YOUR_DOMAIN . '/cart.php';
  $makeSession['currency'] = 'EUR';
  $makeSession['customer_email'] = $_SESSION['email'];
  $makeSession['payment_method_types'] = [
    'card',
  ];
  $makeSession['shipping_options'] = [['shipping_rate' => $shipping_rate->id]];
  if($_SESSION["discount_code"] != null){
    $makeSession['discounts'] = [
        [
            'coupon' => $_SESSION['discount_code'],
            ]
        ];
  }

$checkout_session = \Stripe\Checkout\Session::create($makeSession);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);