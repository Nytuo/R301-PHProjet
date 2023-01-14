<?php
require_once "head.php";
require_once "header.php";
require_once "SqlApi.php";
require_once "Mailer.php";

if (isset($_POST["nom"])){
    $nom = FILTER_INPUT(INPUT_POST, "nom", FILTER_SANITIZE_STRING);
    $prenom = FILTER_INPUT(INPUT_POST, "prenom", FILTER_SANITIZE_STRING);
    $email = FILTER_INPUT(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = FILTER_INPUT(INPUT_POST, "password", FILTER_SANITIZE_STRING);
    $password2 = FILTER_INPUT(INPUT_POST, "password2", FILTER_SANITIZE_STRING);
    $adresse = FILTER_INPUT(INPUT_POST, "address", FILTER_SANITIZE_STRING);
    $ville = FILTER_INPUT(INPUT_POST, "ville", FILTER_SANITIZE_STRING);
    $codePostal = FILTER_INPUT(INPUT_POST, "codePostal", FILTER_SANITIZE_STRING);
    $pays = FILTER_INPUT(INPUT_POST, "pays", FILTER_SANITIZE_STRING);
    $sql = new SqlApi();
    if ($password == $password2){
        $sql->insertUser($nom, $prenom, $email, $password, $adresse, $ville, $codePostal, $pays);
        Mailer::sendMail($email, "Bienvenue sur Comics sans MS", "Bienvenue sur Comics Sans MS, vous venez de créé un compte et nous vous en remercions!
        Vous pouvez maintenant vous connecter et commencez à acheter des comics!");
        header("Location: connexion.php");
        exit();
    }else{
        $_SESSION['error'] = "Les mots de passe ne correspondent pas";
        header("Location: register.php");
        exit();
    }

}

?>
<main>
    <h1>INSCRIPTION SANS MS<span class="sprt s-category-border-rr inline-block"></span></h1>

    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="input-field">

            <input type="email" name="email" id="email" class="validate" required>
            <label for="email">Email</label>
        </div>
        <div class="input-field">
            <input type="password" name="password" id="password" class="validate" required>
            <label for="password">Mot de passe</label>
        </div>
        <div class="input-field">
            <input type="password" name="password2" id="password2" class="validate" required>
            <label for="password2">Vérification du mot de passe</label>
        </div>
        <div class="input-field">
            <input type="text" name="nom" class="validate" id="nom" required>
            <label for="nom">Nom</label>
        </div>
        <div class="input-field">
            <input type="text" name="prenom" class="validate" id="prenom" required>
            <label for="prenom">Prénom</label>
        </div>
        <div class="input-field">
            <input type="text" name="address" id="address" class="validate" required>
            <label for="address">Adresse</label>
        </div>
        <div class="input-field">
            <input type="text" name="ville" id="ville" class="validate" required>
            <label for="ville">Ville</label>
        </div>
        <div class="input-field">
            <input type="text" name="codePostal" id="codePostal" class="validate" required>
            <label for="codePostal">Code Postal</label>
        </div>
        <div class="input-field">
            <input type="text" name="pays" id="pays" class="validate" required>
            <label for="pays">Pays</label>
        </div>

        <button type="submit" class="btn waves-effect">Créé un compte</button>
        ou
        <a class="btn waves-effect" href="connexion.php">Se connecter</a>
    </form>
</main>
<?php

echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
$_SESSION['error'] = "";

require_once "footer.php";
