
    <!-- add products from product object -->
    <?php
    require_once "head.php";
    require_once "header.php";
    require_once "productClass.php";
    require_once "SqlApi.php";
    ?>
    <main>
    <h1 class="center">Résultat de recherche<span class="sprt s-category-border-rr inline-block"></span></h1>
        <div class="cards-list">
    <?php
    echo "<script>document.getElementById('search').value = '" . $_GET['search'] . "'</script>";
    $db=new SqlApi();
    //open session
    session_start();

    $products = $db->searchProduct($_GET['search']);
    $productList = array();

    //for each product create a product object

    foreach ($products as $product) {
        $productObject = new product($product['id']);
        $productList[] = $productObject;
    }

    //display all products
    foreach ($productList as $product) {
        $product->displayProduct();
    }
    if (count($productList) == 0) {
        echo "<h2 class='center'>Aucun résultat</h2>";
    }

    ?>
        </div>
    </main>

