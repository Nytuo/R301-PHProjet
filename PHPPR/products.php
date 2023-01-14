<?php
require_once "head.php";
require_once "header.php";
?>
    <main>
        <h1 class="center">Tous les produits<span class="sprt s-category-border-rr inline-block"></span></h1>
        <?php
        require_once "SqlApi.php";
        require_once "productClass.php";
        $db = new SqlApi();
        $products = $db->getProducts();
        $productList = array();
        foreach ($products as $product) {
            $productObject = new product($product['ref'], $product['id'], $product['title'], $product['public_price'], $product['paid_price'], $product['description'], $product['image'], $product['quantity'], $product['pages'], $product['publisher'], $product['out_date'], $product['author'], $product['language'], $product['format'], $product['dimensions'], $product['category']);
            $productObject->setQuantity($product['quantity'] ?? 0);
            $productList[] = $productObject;
        }
        echo "<div class='cards-list'>";
        foreach ($productList as $product) {
            $product->displayProduct();
        }
        echo "</div>";

        ?>

    </main>
<?php
require_once "footer.php";
?>