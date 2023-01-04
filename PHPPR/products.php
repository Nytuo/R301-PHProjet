<div>

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
        echo '<a href="product.php?id=' . $product->ref . '">';
        echo "<div class='product'>";
        echo "<img src=" . $product->getImage() . " alt='product image'>";
        echo "<h3>" . $product->getTitle() . "</h3>";
        echo "<p>" . $product->getDescription() . "</p>";
        echo "<p>" . $product->getPublicPrice() . "€</p>";
        echo "<p>Quantity: " . $product->getQuantity() . "</p>";
        echo "<form action='addProduct.php' method='post'>";
        echo "<input type='hidden' name='id' value='" . $product->getRef() . "'>";
        echo "<input type='submit' value='Add to cart'>";
        echo "</form>";
        echo "</div>";
        echo "</a>";
    }

    ?>

</div>