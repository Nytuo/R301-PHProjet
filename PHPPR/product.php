<?php
require_once "head.php";
require_once "header.php";
require_once "productClass.php";
require_once "SqlApi.php";
$sql = new SqlApi();
$DBProduct = $sql->getProduct($_GET['id']);
$product = new product($DBProduct['ref'],$DBProduct['id'], $DBProduct['title'], $DBProduct['public_price'], $DBProduct['paid_price'],$DBProduct['description'], $DBProduct['image'],$DBProduct['quantity'], $DBProduct['pages'], $DBProduct['publisher'],$DBProduct['out_date'], $DBProduct['author'], $DBProduct['language'], $DBProduct['format'], $DBProduct['dimensions'], $DBProduct['category']);

?>
<body>
<main>
    <div id="ColCover">
    <img src="<?php echo $product->getImage() ?>" alt="" class="product_img">
    </div>
    <div id="ColContent">
    <h1><?php echo $product->getTitle() ?><span class="sprt s-category-border-rr inline-block"></span></h1>
        <div class="purchase">

    <p class="price"><?php echo $product->getPublicPrice() ?>€ (TTC)</p>
    <p class="availability" style="position: relative;top: -40px"><?php echo $product->isAvailable() ?></p>
    <form action="addProduct.php" method="post" style="position: relative;top: -40px">
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


