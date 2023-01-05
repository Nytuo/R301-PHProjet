<?php
require_once "head.php";
require_once "header.php";
require_once "productClass.php";
$product = new product($_GET['id']);

?>
<body>
<main>
    <div id="ColCover">
    <img src="<?php echo $product->getImage() ?>" alt="" class="product_img">
    </div>
    <div id="ColContent">
    <h1><?php echo $product->getTitle() ?></h1>
        <div class="purchase">

    <span class="price"><?php echo $product->getPublicPrice() ?>€ (TTC)</span>
    <span class="availability"><?php echo $product->isAvailable() ?></span>
    <form action="addProduct.php" method="post" style="display: inline">
        <input type="hidden" name="id" value="<?php echo $product->getRef() ?>">
        <input type="submit" value="Add to cart" class="waves-effect btn">
    </form>
        </div>
        <div>
    <p><?php echo $product->getDescription() ?></p>
        </div>
    </div>
</main>


</body>


