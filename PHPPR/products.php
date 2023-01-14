<?php
require_once "head.php";
require_once "header.php";
//show php errors
ini_set('display_errors', 1);
?>
<main>
<h1 class="center">Tous les produits<span class="sprt s-category-border-rr inline-block"></span></h1>

    <!-- add products from product object -->
    <?php
    //open session
    session_start();

    //get all products from database
    require_once "SqlApi.php";
    require_once "productClass.php";
    $db=new SqlApi();

    $products = $db->getProducts();
    $productList = array();
    //for each product create a product object
    foreach ($products as $product) {
        $productObject = new product($product['ref'],$product['id'], $product['title'], $product['public_price'], $product['paid_price'],$product['description'], $product['image'],$product['quantity'], $product['pages'], $product['publisher'],$product['out_date'], $product['author'], $product['language'], $product['format'], $product['dimensions'], $product['category']);
        $productObject->setTitle($product['title']);
        $productObject->setPublicPrice($product['public_price']);
        $productObject->setDescription($product['description']);
        $productObject->setImage($product['image']);
        $productObject->setQuantity($product['quantity']??0);
        $productList[] = $productObject;
    }

    //display all products
        echo "<div class='cards-list'>";
    foreach ($productList as $product) {
        $product->displayProduct();
    }
        echo "</div>";

    ?>

</main>