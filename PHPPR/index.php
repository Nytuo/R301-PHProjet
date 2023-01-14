<?php
include_once "head.php"
?>
<body>
<?php
include_once "header.php";
require_once "productClass.php";
require_once "SqlApi.php";
$sql = new SqlApi();
$products = $sql->getProducts();
    $prodList = array();
    foreach ($products as $product) {
        $productObject = new product($product['ref'], $product['id'], $product['title'], $product['public_price'], $product['paid_price'], $product['description'], $product['image'], $product['quantity'], $product['pages'], $product['publisher'], $product['out_date'], $product['author'], $product['language'], $product['format'], $product['dimensions'], $product['category']);
        $prodList[] = $productObject;
    }
?>
<main>
    <h1>Les nouveaux arrivages<span class="sprt s-category-border-rr inline-block"></span></h1>
    <div class='cards-list'>
    <?php
        $ids = array();
        foreach ($prodList as $product) {
            $ids[] = $product->getId();
        }
        rsort($ids);
        $ids = array_slice($ids, 0, 5);
        foreach ($ids as $id) {
            foreach ($prodList as $product) {
                if ($product->getId() == $id) {
                    $product->displayProduct();
                }
            }
        }

    ?>
    </div>
    <?php
    $publisherList = array();
    foreach ($prodList as $product) {
        $publisherList[] = $product->getPublisher();
    }
    $publisherList = array_unique($publisherList);
    foreach ($publisherList as $publisher) {
        echo "<h1>$publisher<span class='sprt s-category-border-rr inline-block'></span></h1>";
        echo "<div class='cards-list'>";
        foreach ($prodList as $product) {
            if ($product->getPublisher() == $publisher) {
                $product->displayProduct();
            }
        }
        echo "</div>";
    }
    ?>

</main>
<?php
include_once "footer.php"
?>
</body>
</html>