<?php
require_once "head.php";
require_once "header.php";
session_set_cookie_params(36000, '/');
session_start();
require_once "SqlApi.php";
require_once "productClass.php";
$sql = new SqlApi();


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


$result = $sql->connectAdmin($email, $ashPassword);
if (!$result) {
    $_SESSION['error'] = "Wrong password";
    header("Location: connexion.php");
    exit(0);
} else {
    $_SESSION['password'] = $ashPassword;//todo en attendant (ou peut etre définitif)
    $_SESSION['email'] = $email;
}


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


if (isset($_POST["name"])) {
    $fileUploaded = false;
    if ($_FILES["image"]["name"] != "") {
        $fileUploaded = true;
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        if ($_FILES["image"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            echo $imageFileType;
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
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
    $pages = filter_input(INPUT_POST, 'pages', FILTER_SANITIZE_NUMBER_INT);
    $publisher = filter_input(INPUT_POST, 'editor', FILTER_SANITIZE_STRING);
    $author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $dimensions = filter_input(INPUT_POST, 'dimensions', FILTER_SANITIZE_STRING);
    $format = filter_input(INPUT_POST, 'format', FILTER_SANITIZE_STRING);
    $language = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'outDate', FILTER_SANITIZE_STRING);
    $sql->insertProduct($name, $ref, $public_price, $paid_price, $description, $image, $quantity, $pages, $publisher, $date, $author, $language, $format, $dimensions, $category);
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

    echo "<table  class='responsive-table  highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Name</th>";
    echo "<th>Image</th>";
    echo "<th>Description</th>";
    echo "<th>Date de parution</th>";
    echo "<th>Langage</th>";
    echo "<th>Price</th>";
    echo "<th>Pages</th>";
    echo "<th>Auteur</th>";
    echo "<th>Éditeur</th>";
    echo "<th>Catégorie</th>";
    echo "<th>Quantité</th>";
    echo "<th>Suppression</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['title'] . "</td>";
        echo "<td><img src=" . $product['image'] . " /  width='100'></td>";
        $description = $product['description'];
        $description = explode(" ", $description);
        $description = array_slice($description, 0, 20);
        $description = implode(" ", $description);
        $description = $description . "...";
        echo "<td>" . $description . "</td>";
        echo "<td>" . $product['out_date'] . "</td>";
        echo "<td>" . $product['language'] . "</td>";
        echo "<td>" . $product['price'] . "</td>";
        echo "<td>" . $product['pages'] . "</td>";
        echo "<td>" . $product['author'] . "</td>";
        echo "<td>" . $product['publisher'] . "</td>";
        echo "<td>" . $product['category'] . "</td>";
        echo "<td >";
        echo "<form class='inputTD' action='adminZonne.php' method='post'>";
        echo "<input name='changeQty'  type='number' value=" . $product['quantity'] . " id='quantity" . $product['id'] . "'>
        <input type='hidden' name='id' value=" . $product['id'] . ">
<input type='submit' value='Modifier' class='btn waves-effect'/>
</form>
</td>";
        echo "<td><a href='deleteProduct.php?id=" . $product['id'] . "'>Supprimer</a></td>";
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

        if ($product['quantity'] !== null && $product['quantity'] !== "undefined" && $product['quantity'] < 10 && $product['quantity'] > 0) {
            echo "<p class='NotThatMuch'>Attention, le produit " . $product['title'] . " à moins de 10 exemplaires en stock</p>";
            $count++;
        }
        if ($product['quantity'] !== null && $product['quantity'] !== "undefined" && $product['quantity'] == 0) {
            echo "<p class='OutOfStock'>Attention, le produit " . $product['title'] . " est en rupture de stock</p>";
            $count++;
        }
    }
    return $count;

}

function showMessage($sql)
{
    $messages = array("delOk" => "Entrée supprimer avec succès", "delFail" => "Erreur lors de la suppression de l'entrée", "updateOK" => "Quantité modifié avec succès", "updateFail" => "Erreur lors de la modification de la quantité");
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if (array_key_exists($message, $messages)) {
            echo "<script>Toastifycation('" . $message . "')</script>";
        }
    }
    if (detectQuantity($sql) != 0) {
        echo "<script>Toastifycation('Vous avez des alertes de stock!','#ff0000')</script>";
    }
}

function showClient($sql)
{
    $allProducts = $sql->getClients();

    echo "<table  class='responsive-table  highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Nom</th>";
    echo "<th>Prénom</th>";
    echo "<th>Email</th>";
    echo "<th>Adresse</th>";
    echo "<th>Ville</th>";
    echo "<th>Code postal</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Suppression</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['firstName'] . "</td>";
        echo "<td>" . $product['email'] . "</td>";
        echo "<td>" . $product['address'] . "</td>";
        echo "<td>" . $product['city'] . "</td>";
        echo "<td>" . $product['zip_code'] . "</td>";
        echo "<td>" . $product['phone'] . "</td>";
        echo "<td><a href='deleteClient.php?id=" . $product['id'] . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

function showFour($sql)
{
    $allProducts = $sql->getFour();

    echo "<table  class='responsive-table highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Nom</th>";
    echo "<th>Email</th>";
    echo "<th>Adresse</th>";
    echo "<th>Ville</th>";
    echo "<th>Code postal</th>";
    echo "<th>Pays</th>";
    echo "<th>Suppression</th>";
    echo "</tr>";
    foreach ($allProducts as $product) {
        echo "<tr>";
        echo "<td>" . $product['id'] . "</td>";
        echo "<td>" . $product['name'] . "</td>";
        echo "<td>" . $product['email'] . "</td>";
        echo "<td>" . $product['address'] . "</td>";
        echo "<td>" . $product['city'] . "</td>";
        echo "<td>" . $product['zip_code'] . "</td>";
        echo "<td>" . $product['country'] . "</td>";
        echo "<td><a href='deleteFour.php?id=" . $product['id'] . "'>Supprimer</a></td>";
        echo "</tr>";
    }
    echo "</table>";
}

function showCommands($sql)
{
    $allProducts = $sql->getCommands();

    echo "<table  class='responsive-table  highlight'>";
    echo "<tr>";
    echo "<th>Id</th>";
    echo "<th>Client</th>";
    echo "<th>Product ID</th>";
    echo "<th>Quantity</th>";
    echo "<th>Price</th>";
    echo "<th>fournisseur_id</th>";
    echo "<th>Delivery date</th>";
    echo "<th>Products</th>";
    echo "<th>Suppression</th>";
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
        echo "<td><a href='deleteCommand.php?id=" . $product['id'] . "'>Supprimer</a></td>";
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
                        setTimeout(function () {
                            document.querySelector(".snack_container").style.display = "none";
                        }, notifDelay)

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
            <p class="InStock soloInTheMiddle" id="benef">Bénéfice : <?php
                echo $sql->getBenefices();
                ?>€</p>

            <p class="soloInTheMiddle" id="NV">Nombre de vente : <?php
                echo $sql->getNbVente()

                ?></p>
            <p class="soloInTheMiddle priceFOnly" id="MA">Montant des achats : <?php
                echo $sql->getMA()

                ?>€</p>
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
                a.innerText = "Bénéfice : " + a.innerText.split(" : ")[1];
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
                        <span>Fichier</span>
                        <input type="file" name="image" id="image">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text">
                    </div>
                </div>
                <div class="input-field">
                    <label for="imageURL">URL vers couverture</label><input type="text" id="imageURL" name="imageURL">
                </div>
                <div class="input-field">
                    <label for="pages">Nombre de pages</label><input type="text" id="pages" name="pages">
                </div>
                <div class="input-field">
                    <label for="author">Auteur</label><input type="text" id="author" name="author">
                </div>
                <div class="input-field">
                    <label for="editor">Editeur</label><input type="text" id="editor" name="editor">
                </div>
                <div class="input-field">
                    <label for="outDate">Date de parution</label><input type="text" id="outDate" name="outDate">
                </div>
                <div class="input-field">
                    <label for="language">Langage</label><input type="text" id="language" name="language">
                </div>
                <div class="input-field">
                    <label for="format">Format</label><input type="text" id="format" name="format">
                </div>
                <div class="input-field">
                    <label for="dimensions">Dimensions</label><input type="text" id="dimensions" name="dimensions">
                </div>
                <div class="input-field">
                    <label for="category">Catégorie</label><input type="text" id="category" name="category">
                </div>
                <input type="submit" class="waves-effect btn" value="Ajouter un produit">
            </form>

            <div id="GoogleBooksAPI">
                <h3>Google Books API</h3>

                <label for="ISBN">ISBN</label><input type="text" id="ISBN" placeholder="ISBN">
                <button class="waves-effect btn" onclick="getBookByISBN(document.querySelector('#ISBN').value)">
                    Rechercher
                </button>
                <br>
                <label for="title">Titre</label><input type="text" id="title" placeholder="Titre">
                <button class="waves-effect btn" onclick="getBookByTitle(document.querySelector('#title').value)">
                    Rechercher
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
                    <label for="City">Ville</label>
                </div>
                <div class="input-field">
                    <input type="text" name="ZipCode" id="ZipCode">
                    <label for="ZipCode">Code Postal</label>
                </div>
                <div class="input-field">
                    <input type="text" name="country" id="country">
                    <label for="country">Pays</label>
                </div>
                <input type="submit" class="waves-effect btn" value="Ajouter un fournisseur">
            </form>
        </div>
        <div id="listProducts" class="col s12">    <?php showProducts($sql) ?> </div>
        <div id="listClients" class="col s12">    <?php showClient($sql) ?> </div>
        <div id="listFour" class="col s12">    <?php showFour($sql) ?> </div>
        <div id="listCommands" class="col s12">    <?php showCommands($sql) ?> </div>
    </div>


</main>
<script src="assets/js/product.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var elems = document.querySelectorAll('.tabs');
        var instance = M.Tabs.init(elems, {
            swipeable: true
        });
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
                    console.log(cover);
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
                let card = new Product(cdata["volumeInfo"]["industryIdentifiers"][0]["identifier"], cdata["id"], cdata["volumeInfo"]["title"], price, price, cdata["volumeInfo"]["description"], cover, 1, cdata["volumeInfo"]["pageCount"], cdata["volumeInfo"]["publisher"], cdata["volumeInfo"]["publishedDate"], cdata["volumeInfo"]["authors"], cdata["volumeInfo"]["language"], cdata["volumeInfo"]["printType"], cdata["volumeInfo"]["dimensions"], cdata["volumeInfo"]["categories"]).displayProduct();

                card.addEventListener("click", () => {
                    window.location.scrollTo(0, 0);
                    document.querySelector("#name").value = cdata["volumeInfo"]["title"];
                    document.querySelector("#ref").value = cdata["id"];
                    document.querySelector("#description").value = cdata["volumeInfo"]["description"];
                    document.querySelector("#public_price").value = price !== null ? price : 0;
                    document.querySelector("#paid_price").value = 0;
                    document.querySelector("#imageURL").value = cover;
                    document.querySelector("#pages").value = cdata["volumeInfo"]["pageCount"];
                    document.querySelector("#author").value = cdata["volumeInfo"]["authors"][0];
                    document.querySelector("#editor").value = cdata["volumeInfo"]["publisher"];
                    document.querySelector("#outDate").value = cdata["volumeInfo"]["publishedDate"];
                    document.querySelector("#category").value = cdata["volumeInfo"]["categories"][0];
                    document.querySelector("#language").value = cdata["volumeInfo"]["language"];
                    document.querySelector("#dimensions").value = cdata["volumeInfo"]["dimensions"] !== undefined ? cdata["volumeInfo"]["dimensions"]["height"] + "x" + cdata["volumeInfo"]["dimensions"]["width"] + "x" + cdata["volumeInfo"]["dimensions"]["thickness"] : "unknown";
                    document.querySelector("#format").value = cdata["volumeInfo"]["printType"];


                })
                div.appendChild(card);
            }
        } else {
            let title = document.createElement("p");
            title.innerText = "No results";
            div.appendChild(title);
        }
        document.querySelector("#GBContent").innerHTML = "<h1>Résultat pour " + name + "<span class='sprt s-category-border-rr inline-block'></span></h1>";
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
                    document.querySelector("#pages").value = cdata["volumeInfo"]["pageCount"];
                    document.querySelector("#author").value = cdata["volumeInfo"]["authors"][0];
                    document.querySelector("#editor").value = cdata["volumeInfo"]["publisher"];
                    document.querySelector("#outDate").value = cdata["volumeInfo"]["publishedDate"];
                    document.querySelector("#category").value = cdata["volumeInfo"]["categories"][0];
                    document.querySelector("#language").value = cdata["volumeInfo"]["language"];
                    document.querySelector("#dimensions").value = cdata["volumeInfo"]["dimensions"]["height"] + "x" + cdata["volumeInfo"]["dimensions"]["width"] + "x" + cdata["volumeInfo"]["dimensions"]["thickness"];
                    document.querySelector("#format").value = cdata["volumeInfo"]["printType"];
                })
                div.appendChild(card);
            }
        } else {
            let title = document.createElement("p");
            title.innerText = "No results";
            div.appendChild(title);
        }
        document.querySelector("#GBContent").innerHTML = "Results for " + ISBN;
        document.getElementById("GBContent").appendChild(div);
    }


</script>

<?php
include 'footer.php';
?>
</body>
