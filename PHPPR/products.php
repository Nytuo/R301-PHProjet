<?php
require_once "head.php";
require_once "header.php";
?>
<main>
<h1 class="center">All Comics</h1>

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
        $productObject = new product($product['id']);
        $productObject->setTitle($product['title']);
        $productObject->setPublicPrice($product['public_price']);
        $productObject->setDescription($product['description']);
        $productObject->setImage($product['image']);
        $productObject->setQuantity($product['quantity']);
        $productList[] = $productObject;
    }

    //display all products
    foreach ($productList as $product) {
        echo "<div class='cards-list'>";
        $product->displayProduct();
        echo "</div>";
    }

    ?>

</main>