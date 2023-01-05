<?php
require_once "head.php";
require_once "header.php";
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
    $target_dir = "uploads/";
    // print in console the name of the file
    $target_file = $target_dir . basename($_POST["name"]);
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
    $ref = filter_input(INPUT_POST, 'ref', FILTER_SANITIZE_STRING);
    $public_price = filter_input(INPUT_POST, 'public_price', FILTER_SANITIZE_NUMBER_FLOAT);
    $paid_price = filter_input(INPUT_POST, 'paid_price', FILTER_SANITIZE_NUMBER_FLOAT);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $image = $target_file;
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
    $sql->insertProduct($name,$ref, $public_price,$paid_price, $description, $image, $quantity);
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

//detect if a product's quantity is less than 10

function detectQuantity($sql)
{
    $allProducts = $sql->getProducts();
    foreach ($allProducts as $product) {
        if ($product['quantity'] < 10) {
            echo "<p>Attention, le produit " . $product['title'] . " est en rupture de stock</p>";
            //call js function Toastification
            echo "<script>Toastifycation('Vous avez des alertes de stock!','#ff0000')</script>";
        }
    }
}

?>

<body>

<main>

    <h1>ADMIN ZONE</h1>
    <div class="snack_container">
        <div class="snack_rectangle">
            <div class="snack_notification">
                <i class="material-icons">info</i>
                <span id="snack_msg" style="margin-left: 20px ">This is a test notification.</span>
            </div>
        </div>
    </div>
    <script>
        function Toastifycation(message, BGColor = "#333", FrontColor = "#ffffff") {
            console.log("toast");
            let x = document.querySelector("#snack_msg");
            x.style.paddingLeft = "10px";
            document.querySelector(".snack_container").style.display = "flex";
            document.querySelector(".snack_container").style.opacity = "1";
            document.querySelector(".snack_container").style.position = "fixed";
            document.querySelector(".snack_rectangle").style.position = "absolute";
            document.querySelector(".snack_rectangle").style.bottom = "235px";
            document.querySelector(".snack_rectangle").style.left = "10px";
            document.querySelector(".snack_container").style.zIndex = "10";
            x.innerText = message;
            document.querySelector(".snack_rectangle").style.backgroundColor = BGColor;
            x.style.color = FrontColor;
            setTimeout(function () {
                document.querySelector(".snack_container").style.opacity = "0";
            }, 8000);
        }
    </script>
    <?php detectQuantity($sql); ?>
    <p>WELCOME <?php echo $_SESSION['email'] ?></p>
    <p>YOU ARE CONNECTED</p>
    <p>YOU CAN ADD PRODUCTS</p>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
        <input type="text" name="name">
        <input type="text" name="ref">
        <input type="text" name="description">
        <input type="text" name="public_price">
        <input type="text" name="paid_price">
        <input type="text" name="quantity">
        <input type="file" name="image" id="image">
        <input type="submit" value="Add product">
    </form>
    <p>YOU CAN SHOW PRODUCTS</p>
    <?php showProducts($sql) ?>
</main>



</body>
