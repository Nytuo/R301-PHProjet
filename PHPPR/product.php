<?php

$product = new product($_POST['id']);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $product->getName() ?></title>
</head>
<body>

<h1><?php echo $product->getName() ?></h1>
<p><?php echo $product->getDescription() ?></p>
<p><?php echo $product->getPrice() ?></p>
<p><?php echo $product->getQuantity() ?></p>
<p><?php echo $product->getCategory() ?></p>
<img src="<?php echo $product->getImage() ?>" alt="">
<form action="addProduct.php" method="post">
    <input type="hidden" name="id" value="<?php echo $product->getId() ?>">
    <input type="submit" value="Add to cart">
</form>

</body>
</html>


