<div>

    <!-- add products from product object -->
    <?php
    //open session
    session_start();

    //get all products from database
    $this->db = new mysqli('localhost', 'root', '', 'comics');
    if ($this->db->connect_errno) {
        echo "Failed to connect to MySQL: " . $this->db->connect_error;
        exit();
    }
    $sql = "SELECT * FROM products WHERE title LIKE '%" . $_POST['search'] . "%' OR ref LIKE '%" . $_POST['search'] . "%'";
    $result = $this->db->query($sql);
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $this->db->close();
    $productList = array();

    //for each product create a product object
    foreach ($products as $product) {
        $productObject = new product($product['id']);
        $productObject->setTitle($product['name']);
        $productObject->setPublicPrice($product['price']);
        $productObject->setDescription($product['description']);
        $productObject->setImage($product['image']);
        $productObject->setQuantity($product['quantity']);
        $productObject->setCategory($product['category']);
        $productList[] = $productObject;
    }

    //display all products
    foreach ($productList as $product) {
        echo "<div class='product'>";
        echo "<img src='" . $product->getImage() . "' alt='product image'>";
        echo "<h3>" . $product->getTitle() . "</h3>";
        echo "<p>" . $product->getDescription() . "</p>";
        echo "<p>" . $product->getPublicPrice() . "€</p>";
        echo "<p>Quantity: " . $product->getQuantity() . "</p>";
        echo "<p>Category: " . $product->getCategory() . "</p>";
        echo "<form action='addProduct.php' method='post'>";
        echo "<input type='hidden' name='id' value='" . $product->getRef() . "'>";
        echo "<input type='submit' value='Add to cart'>";
        echo "</form>";
        echo "</div>";
    }

    ?>

</div>