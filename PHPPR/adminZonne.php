<?php
require_once "head.php";
require_once "header.php";
session_set_cookie_params(36000, '/');
session_start();
// importe SqlApi
require_once "SqlApi.php";
require_once "productClass.php";
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

if (isset($_POST["changeQty"])) {
    $sql->updateQuantity($_POST["changeQty"], $_POST["id"]);
    header("Location: adminZonne.php?message=updateOK");
    exit(0);
}

if (isset($_POST['fname'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $zip_code = filter_input(INPUT_POST, 'ZipCode', FILTER_SANITIZE_STRING);
    $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
    $sql->insertFournisseur($fname, $email, $address, $city, $zip_code, $country);
    header("Location: adminZonne.php");
    exit(0);
}

var_dump($_FILES);
if (isset($_POST["name"])) {
    $fileUploaded = false;
    if (isset($_POST["image"])) {
        $fileUploaded = true;
        //save the image in the server
        $target_dir = "uploads/";
        // print in console the name of the file
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
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
    }

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $ref = filter_input(INPUT_POST, 'ref', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $image = "uploads/default.png";
    if ($fileUploaded) {
        $image = $target_file;
    } else if (isset($_POST["imageURL"])) {
        $image = $_POST["imageURL"];
    }
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
    if ($quantity == null) {
        $quantity = 0;
    }
    $public_price = filter_input(INPUT_POST, 'public_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $paid_price = filter_input(INPUT_POST, 'paid_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
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
        echo "<td >";
        echo "<form class='inputTD' action='adminZonne.php' method='post'>";
        echo "<input name='changeQty'  type='number' value=" . $product['quantity'] . " id='quantity" . $product['id'] . "'>
        <input type='hidden' name='id' value=" . $product['id'] . ">
<input type='submit' value='Modifier la quantité' class='btn waves-effect'/>
</form>
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

        if ($product['quantity'] !== null && $product['quantity'] !== "undefined" && $product['quantity'] < 10) {
            echo "<p class='OutOfStock'>Attention, le produit " . $product['title'] . " est en rupture de stock</p>";
            $count++;
        }
    }
    return $count;

}

function showMessage($sql)
{
    $messages = array("delOk" => "Entrée supprimer avec succès", "delFail" => "Erreur lors de la suppression de l'entrée", "updateOK" => "Quantité modifié avec succès", "updateFail" => "Erreur lors de la modification de la quantité");
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

function showCommands($sql)
{
    $allProducts = $sql->getCommands();

    echo "<table  class='responsive-table centered highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Client</th>";
    echo "<th>Product ID</th>";
    echo "<th>Quantity</th>";
    echo "<th>Price</th>";
    echo "<th>fournisseur_id</th>";
    echo "<th>Delivery date</th>";
    echo "<th>Products</th>";
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

putenv("GBAPIKEY=AIzaSyCMmAxUdCNLNh14IMSmHV6tQwZ-zs5iW6g")

?>

<body>

<main>

    <h1>Panel Sans MS de <?php echo $_SESSION['email'] ?><span class="sprt s-category-border-rr inline-block"></span>
    </h1>
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


        let notifDelay = 2500;

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
                    console.log(notif);
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
                    }, notifDelay);
                }
            }, notifDelay + 1000);
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
                <div class="input-field">
                    <input type="text" id="imageURL" name="imageURL">
                </div>
                <input type="submit" class="waves-effect btn" value="Add product">
            </form>

            <div id="GoogleBooksAPI">
                <h3>Google Books API</h3>

                option 1 : <input type="text" id="ISBN" placeholder="ISBN">
                <button class="waves-effect btn" onclick="getBookByISBN(document.querySelector('#ISBN').value)">Get
                    book
                </button>
                <br>
                option 2 : <input type="text" id="title" placeholder="Title">
                <button class="waves-effect btn" onclick="getBookByTitle(document.querySelector('#title').value)">Get
                    book
                </button>

                <div id="GBContent"></div>

            </div>

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
        document.querySelector(".tabs-content").style.height = "1000vh";
    });

    async function getBookByTitle(name = "") {
        if (name === "") {
            console.log("GETGOOGLEAPI_book : name is empty");
            return;
        }

        name = name.replaceAll(/[(].+[)]/g, "");
        name = name.replaceAll(/[\[].+[\]]/g, "");
        name = name.replaceAll(/[\{].+[\}]/g, "");
        name = name.replaceAll(/[#][0-9]{1,}/g, "");
        name = name.replace(/\s+$/, "");
        console.log("GETGOOGLEAPI_book : name : " + name);
        let url = "https://www.googleapis.com/books/v1/volumes?q=" + encodeURIComponent(name) + "&maxResults=10&key=<?php echo $_ENV["GBAPIKEY"] ?>";
        let response = await fetch(url);
        let data = await response.json();
        console.log(data);
        let div = document.createElement("div");
        div.classList.add("cards-list");
        if (data["totalItems"] > 0) {
            for (let i = 0; i < (data["items"].length); i++) {
                let cdata = data["items"][i];
                console.log(cdata);
                let cover;
                if (cdata["volumeInfo"]["imageLinks"] !== undefined) {

                    cover = cdata["volumeInfo"]["imageLinks"]
                    if (cover["large"] !== undefined) {
                        cover = cover["large"]
                    } else if (cover["thumbnail"] !== undefined) {
                        cover = cover["thumbnail"]
                    } else {
                        cover = null
                    }
                } else {
                    cover = null;
                }
                let price;
                if (cdata["saleInfo"]["retailPrice"] !== undefined) {
                    price = cdata["saleInfo"]["retailPrice"]["amount"]
                } else {
                    price = null;
                }
                let card = document.createElement("div");
                card.classList.add("product");
                let subDiv = document.createElement("div");
                subDiv.classList.add("flip");
                let subDiv2 = document.createElement("div");
                subDiv2.classList.add("front");
                subDiv2.style.backgroundImage = "url(" + cover + ")";
                subDiv2.style.backgroundSize = "cover";
                subDiv.appendChild(subDiv2);
                let subDiv3 = document.createElement("div");
                subDiv3.classList.add("back");
                subDiv3.innerHTML = "<h3>" + cdata["volumeInfo"]["title"] + "</h3><p>" + cdata["volumeInfo"]["description"] + "</p>";
                subDiv.appendChild(subDiv3);
                card.appendChild(subDiv);
                card.addEventListener("click", () => {
                    document.querySelector("#name").value = cdata["volumeInfo"]["title"];
                    document.querySelector("#ref").value = cdata["id"];
                    document.querySelector("#description").value = cdata["volumeInfo"]["description"];
                    document.querySelector("#public_price").value = price;
                    document.querySelector("#paid_price").value = 0;
                    document.querySelector("#imageURL").value = cover;
                })
                div.appendChild(card);
            }
        } else {
            let title = document.createElement("p");
            title.innerText = "No results";
            div.appendChild(title);
        }

        document.getElementById("GBContent").appendChild(div);

    }

    async function getBookByISBN(ISBN = "") {
        if (ISBN === "") {
            console.log("GETGOOGLEAPI_book : ISBN is empty");
            return;
        }

        ISBN = ISBN.replaceAll(/[(].+[)]/g, "");
        ISBN = ISBN.replaceAll(/[\[].+[\]]/g, "");
        ISBN = ISBN.replaceAll(/[\{].+[\}]/g, "");
        ISBN = ISBN.replaceAll(/[#][0-9]{1,}/g, "");
        ISBN = ISBN.replace(/\s+$/, "");
        console.log("GETGOOGLEAPI_book : ISBN : " + ISBN);
        let url = "https://www.googleapis.com/books/v1/volumes?q=ISBN:" + encodeURIComponent(ISBN) + "&maxResults=1&key=key=<?php echo $_ENV["GBAPIKEY"] ?>";
        let response = await fetch(url);
        let cdata = await response.json();
        let div = document.createElement("div");
        if (cdata["totalItems"] > 0) {
            for (let i = 0; i < cdata["totalItems"]; i++) {
                let cdata = cdata["items"][i];
                let cover;
                if (cdata["volumeInfo"]["imageLinks"] !== undefined) {

                    cover = cdata["volumeInfo"]["imageLinks"]
                    if (cover["large"] !== undefined) {
                        cover = cover["large"]
                    } else if (cover["thumbnail"] !== undefined) {
                        cover = cover["thumbnail"]
                    } else {
                        cover = null
                    }
                } else {
                    cover = null;
                }
                let price;
                if (cdata["saleInfo"]["retailPrice"] !== undefined) {
                    price = cdata["saleInfo"]["retailPrice"]["amount"]
                } else {
                    price = null;
                }
                let card = document.createElement("div");
                card.classList.add("card");
                let title = document.createElement("p");
                title.innerHTML = cdata["title"];
                let coverImg = document.createElement("img");
                coverImg.src = cover;
                let id = document.createElement("p");
                id.innerHTML = cdata["id"];
                let priceP = document.createElement("p");
                priceP.innerHTML = price;
                card.appendChild(title);
                card.appendChild(coverImg);
                card.appendChild(id);
                card.appendChild(priceP);
                card.addEventListener("click", () => {
                    document.querySelector("#name").value = cdata["volumeInfo"]["title"];
                    document.querySelector("#ref").value = cdata["id"];
                    document.querySelector("#description").value = cdata["volumeInfo"]["description"];
                    document.querySelector("#public_price").value = price;
                    document.querySelector("#paid_price").value = 0;
                    document.querySelector("#imageURL").value = cover;
                })
                div.appendChild(card);
            }
        } else {
            let title = document.createElement("p");
            title.innerText = "No results";
            div.appendChild(title);
        }

        document.getElementById("GBContent").appendChild(div);
    }


</script>

<?php
include 'footer.php';
?>
</body>
