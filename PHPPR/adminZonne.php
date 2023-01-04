<?php

session_set_cookie_params(36000, '/');
session_start();
// importe SqlApi
require_once "SqlApi.php";
$sql = new SqlApi();

// open sql connection


// filter input


// pasword max 50 char
if (isset($_POST['password'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (strlen($_POST['password']) > 50) {
        $_SESSION['error'] = "Password too long";
        header("Location: connexion.php");
        exit(0);
    }
    $ashPassword = hash('sha256', $_POST['password']);
} else {
    if (isset($_SESSION['password']) && isset($_SESSION['email'])) {
        $ashPassword = $_SESSION['password'];
        $email = $_SESSION['email'];
    } else {
        header("Location: connexion.php");
        exit(0);
    }
}


$result = $sql->connectUser($email, $ashPassword);
if (!$result) {

    // if the password is correct, open the admin zone
    $_SESSION['error'] = "Wrong password";

    header("Location: connexion.php");
    exit(0);
}

$_SESSION['password'] = $ashPassword;//todo en attendant (ou peut etre définitif)
$_SESSION['email'] = $email;

var_dump($_FILES);
if (isset($_POST["name"])) {

    //save the image in the server
    $target_dir = "images/";
    // print in console the name of the file
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if (isset($_POST["name"])) {
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["image"]["name"]) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $image = $target_file;
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $sql->insertProduct($name, $price, $description, $image, $quantity);
    header("Location: adminZonne.php");
    exit(0);
}


function closeConnection($db)
{
    $db->close();
}

function showProducts($sql)
{
    //get all products from DB

    $allProducts = $sql->getProducts();


    echo "<h1>Products</h1>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Price</th>";
    echo "<th>Description</th>";
    echo "<th>Image</th>";
    echo "<th>Quantity</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['title'] . "</td>";
        echo "<td>" . $product['public_price'] . "</td>";
        echo "<td>" . $product['description'] . "</td>";
        echo "<td><img src=" . $product['image'] . " /></td>";
        echo "<td>" . $product['quantity'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SITE MY ADMIN</title>
</head>
<body>
<h1>ADMIN ZONE</h1>
<p>WELCOME <?php echo $_SESSION['email'] ?></p>
<p>YOU ARE CONNECTED</p>
<p>YOU CAN ADD PRODUCTS</p>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
    <input type="text" name="name">
    <input type="text" name="description">
    <input type="text" name="price">
    <input type="text" name="quantity">
    <input type="file" name="image" id="image">
    <input type="submit" value="Add product">
</form>
<p>YOU CAN SHOW PRODUCTS</p>
<?php showProducts($sql) ?>
</body>
</html>
