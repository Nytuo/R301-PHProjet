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

if (isset($_POST['fname'])){
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
     $zip_code= filter_input(INPUT_POST, 'ZipCode', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $sql->insertFournisseur($fname, $email, $address, $city, $zip_code, $country);
    header("Location: adminZonne.php");
    exit(0);
}

var_dump($_FILES);
if (isset($_POST["name"])) {

    //save the image in the server
    $target_dir = "uploads/";
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
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        echo $imageFileType;
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
    $sql->insertProduct($name, $ref, $public_price, $paid_price, $description, $image, $quantity);
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

    echo "<table  class='responsive-table centered highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Price</th>";
    echo "<th>Description</th>";
    echo "<th>Image</th>";
    echo "<th>Quantity</th>";
    echo "<th>Delete</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['title'] . "</td>";
        echo "<td>" . $product['public_price'] . "</td>";
        echo "<td>" . $product['description'] . "</td>";
        echo "<td><img src=" . $product['image'] . " /  width='100'></td>";
        echo "<td class='inputTD'><input type='number' value=" . $product['quantity'] . " id='quantity" . $product['id'] . "'>
<button class='btn waves-effect' onclick='updateQuantity(document.getElementById(\"quantity" . $product['id'] . "\"), ".$product['id'].")'>Modifier la quantité</button>
</td>";
        echo "<td><a href='deleteProduct.php?id=" . $product['id'] . "'>Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";

}

//detect if a product's quantity is less than 10

function detectQuantity($sql)
{
    $allProducts = $sql->getProducts();
    $count = 0;
    foreach ($allProducts as $product) {
        if ($product['quantity'] < 10) {
            echo "<p class='OutOfStock'>Attention, le produit " . $product['title'] . " est en rupture de stock</p>";
            $count++;
        }
    }
    return $count;

}

function showMessage($sql)
{
    $messages = array("delOk" => "Entrée supprimer avec succès", "delFail" => "Erreur lors de la suppression de l'entrée");
    echo "<script>Toastifycation('" . $messages[$_GET['message']] . "')</script>";
    if (detectQuantity($sql) != 0) {
        echo "<script>Toastifycation('Vous avez des alertes de stock!','#ff0000')</script>";
    }
}

function showClient($sql)
{
    $allProducts = $sql->getClients();

    echo "<table  class='responsive-table centered highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Address</th>";
    echo "<th>City</th>";
    echo "<th>Zip Code</th>";
    echo "<th>Phone</th>";
    echo "<th>Email</th>";
    echo "<th>Delete</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['address'] . "</td>";
        echo "<td>" . $product['city'] . "</td>";
        echo "<td>" . $product['zip_code'] . "</td>";
        echo "<td>" . $product['phone'] . "</td>";
        echo "<td>" . $product['email'] . "</td>";
        echo "<td><a href='deleteClient.php?id=" . $product['id'] . "'>Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

function showFour($sql)
{
    $allProducts = $sql->getFour();

    echo "<table  class='responsive-table centered highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Address</th>";
    echo "<th>City</th>";
    echo "<th>Zip Code</th>";
    echo "<th>Email</th>";
    echo "<th>Delete</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['address'] . "</td>";
        echo "<td>" . $product['city'] . "</td>";
        echo "<td>" . $product['zip_code'] . "</td>";
        echo "<td>" . $product['email'] . "</td>";
        echo "<td><a href='deleteFour.php?id=" . $product['id'] . "'>Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}
function showCommands($sql){
$allProducts = $sql->getCommands();

    echo "<table  class='responsive-table centered highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Client</th>";
    echo "<th>Product</th>";
    echo "<th>Quantity</th>";
    echo "<th>Price</th>";
    echo "<th>Command Date</th>";
    echo "<th>Delivery Date</th>";
    echo "<th>Delivery Status</th>";
    echo "<th>Delete</th>";
    echo "</tr>";

    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['client_id'] . "</td>";
        echo "<td>" . $product['product_id'] . "</td>";
        echo "<td>" . $product['quantity'] . "</td>";
        echo "<td>" . $product['total'] . "</td>";
        echo "<td>" . $product['fournisseur_id'] . "</td>";
        echo "<td>" . $product['date'] . "</td>";
        echo "<td>" . $product['products'] . "</td>";
        echo "<td><a href='deleteCommand.php?id=" . $product['id'] . "'>Delete</a></td>";
        echo "</tr>";
    }
    echo "</table>";


}
?>

<body>

<main>

    <h1>Panel Sans MS de <?php echo $_SESSION['email'] ?></h1>
    <div class="snack_container">
        <div class="snack_rectangle">
            <div class="snack_notification">
                <i class="material-icons">info</i>
                <span id="snack_msg" style="margin-left: 20px ">This is a test notification.</span>
            </div>
        </div>
    </div>
    <script>
        let notifList = [];

        function Toastifycation(message, BGColor = "#333", FrontColor = "#ffffff") {
            console.log("toast");
            notifList.push({
                message: message,
                BGColor: BGColor,
                FrontColor: FrontColor
            });


        }

        function launchNotif() {
            setInterval(() => {
                if (notifList.length > 0) {
                    let notif = notifList.shift();
                    let x = document.querySelector("#snack_msg");
                    x.style.paddingLeft = "10px";
                    document.querySelector(".snack_container").style.display = "flex";
                    document.querySelector(".snack_container").style.opacity = "1";
                    document.querySelector(".snack_container").style.position = "fixed";
                    document.querySelector(".snack_rectangle").style.position = "absolute";
                    document.querySelector(".snack_rectangle").style.bottom = "235px";
                    document.querySelector(".snack_rectangle").style.left = "10px";
                    document.querySelector(".snack_container").style.zIndex = "10";
                    x.innerText = notif.message;
                    document.querySelector(".snack_rectangle").style.backgroundColor = notif.BGColor;
                    x.style.color = notif.FrontColor;
                    setTimeout(function () {
                        document.querySelector(".snack_container").style.opacity = "0";
                    }, 8000);
                }
            }, 9000);
        }


    </script>
    <?php showMessage($sql);
    echo "<script>launchNotif()</script>"; ?>


    <div class="row">
        <div class="col s12">
            <ul class="tabs tabs-fixed-width">
                <li class="tab col s3"><a class="active" href="#compt">Comptabilité</a></li>
                <li class="tab col s3"><a href="#addproduct">Ajouter un produit</a></li>
                <li class="tab col s3"><a href="#addFour">Ajouter un fournisseur</a></li>
                <li class="tab col s3"><a href="#listProducts">Liste des produits</a></li>
                <li class="tab col s3"><a href="#listFour">Liste des fournisseurs</a></li>
                <li class="tab col s3"><a href="#listClients">Liste des clients</a></li>
                <li class="tab col s3"><a href="#listCommands">Liste des commandes</a></li>
            </ul>
        </div>
        <div id="compt" class="col s12">
            <h2>Comptabilité</h2>
            <div style="transform: translateX(50vw)">
            <p class="InStock" id="benef">Bénéfice : <?php
                echo $sql->getBenefices();
                ?>€</p>
            </div>

            <p class="soloInTheMiddle" id="NV">Nombre de vente : <?php
                echo $sql->getNbVente()

                ?></p>
            <p class="priceFOnly soloInTheMiddle" id="CA">Chiffre d'affaire : <?php
                echo $sql->getChiffreAffaire();
                ?>€</p>

            <h3 style="text-align: center">Achats et montant</h3>
            <?php
                echo $sql->getAchatsEtMontant();
                ?>
        </div>
        <script>
            let a = document.querySelector("#benef");
            if (a.innerText.includes("-")) {
                a.className = "OutOfStock";
                a.innerText = "Perte : " + a.innerText.split("-")[1];
            } else {
                a.className = "InStock";
                a.innerText = "Bénéfice : " + a.innerText.split("-")[1];
            }
        </script>
        <div id="addproduct" class="col s12">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <div class="input-field">

                    <input type="text" name="name" id="name">
                    <label for="name">Nom du produit</label>
                </div>
                <div class="input-field">

                    <input type="text" name="ref" id="ref">
                    <label for="ref">Référence</label>
                </div>
                <div class="input-field">

                    <input type="text" name="description" id="description">
                    <label for="description">Description</label>
                </div>
                <div class="input-field">
                    <input type="text" name="public_price" id="public_price">
                    <label for="public_price">Prix public</label>
                </div>
                <div class="input-field">
                    <input type="text" name="paid_price" id="paid_price">
                    <label for="paid_price">Prix d'achat</label>
                </div>
                <div class="input-field">
                    <input type="number" name="quantity" id="quantity">
                    <label for="quantity">Quantité</label>
                </div>
                <div class="file-field input-field">
                    <div class="btn">
                        <span>File</span>
                        <input type="file" name="image" id="image">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
                <input type="submit" class="waves-effect btn" value="Add product">
            </form>
        </div>
        <div id="addFour" class="col s12">
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                <div class="input-field">

                    <input type="text" name="fname" id="fname">
                    <label for="fname">Nom du fournisseur</label>
                </div>
                <div class="input-field">

                    <input type="email" name="email" id="email">
                    <label for="email">email</label>
                </div>
                <div class="input-field">

                    <input type="text" name="adresse" id="adresse">
                    <label for="adresse">adresse</label>
                </div>
                <div class="input-field">
                    <input type="text" name="City" id="City">
                    <label for="City">City</label>
                </div>
                <div class="input-field">
                    <input type="text" name="ZipCode" id="ZipCode">
                    <label for="ZipCode">ZipCode</label>
                </div>
                <div class="input-field">
                    <input type="text" name="country" id="country">
                    <label for="country">country</label>
                </div>
                <input type="submit" class="waves-effect btn" value="Add fournisseur">
            </form>
        </div>
        <div id="listProducts" class="col s12">    <?php showProducts($sql) ?> </div>
        <div id="listClients" class="col s12">    <?php showClient($sql) ?> </div>
        <div id="listFour" class="col s12">    <?php showFour($sql) ?> </div>
        <div id="listCommands" class="col s12">    <?php showCommands($sql) ?> </div>
    </div>


</main>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('.tabs');
        var instance = M.Tabs.init(elems, {
            swipeable: true
        });
        document.querySelector(".tabs-content").style.height = "100vh";
    });
    function updateQuantity(value,ref) {
        console.log(value);
        fetch('updateQuantity.php', {
            method: 'POST',
            body: JSON.stringify({
                quantity: value,
                id: ref,
                gs:true
            })
        })
            .then(response => response.text())
            .then(data => {
                window.location.reload();
            });
    }
</script>

<?php
include 'footer.php';
?>
</body>
