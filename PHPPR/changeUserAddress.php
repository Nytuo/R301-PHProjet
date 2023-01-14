<?php
require_once "head.php";
require_once "header.php";
require_once "SqlApi.php";
require_once "Mailer.php";
$sql = new SqlApi();

if (isset($_SESSION['password']) && isset($_SESSION['email'])) {
    $ashPassword = $_SESSION['password'];
    $email = $_SESSION['email'];
    $resultAdmin = $sql->connectAdmin($email, $ashPassword);
    $resultUser = $sql->connectUser($email, $ashPassword);
    if (!$resultAdmin && !$resultUser) {
        $_SESSION['error'] = "Wrong password";
        header("Location: connexion.php");
        exit(0);
    } else {
        if ($resultAdmin){
            $_SESSION['error'] = "Impossbile pour cette utilisateur";
            header("Location: connexion.php");
        }
        if ($resultUser){
            if (isset($_POST["address"])){
                $sql->updateUserAddress($email, $_POST["address"], $_POST["city"], $_POST["zip"], $_POST["country"]);
                Mailer::sendMail($email, "[Comics Sans MS] Changement d'adresse", "Votre adresse a été changée pour votre compte sur le site Comics Sans MS. Si vous n'êtes pas à l'origine de ce changement, veuillez contacter l'administrateur du site.");
                header("Location: userProfile.php");
            }
        }
    }
}else{
    $_SESSION['error'] = "Vous devez vous connecter pour acceder à cette page";
    header("Location: connexion.php");
}

?>

        <main>
          <h1>Changer d'adresse</h1>
            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
                <div class="input-field">
                <input type="text" name="address" id="address" required>
                <label for="address">Adresse</label>
                </div>
                <div class="input-field">
                    <input type="text" name="city" id="city" required>
                    <label for="city">Ville</label>
                </div>
                <div class="input-field">
                    <input type="text" name="zip" id="zip" required>
                    <label for="zip">Code postal</label>
                </div>
                <div class="input-field">
                    <input type="text" name="country" id="country" required>
                    <label for="country">Pays</label>
                </div>
                <div class="input-field">
                <input type="submit" value="Changer" class="waves-effect btn">
                </div>
        </main>
<?php
require_once "footer.php";
?>

