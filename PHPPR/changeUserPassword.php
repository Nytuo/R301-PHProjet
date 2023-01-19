<?php
require_once "head.php";
require_once "header.php";
require_once "SqlApi.php";
$sql = new SqlApi();
echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
$_SESSION['error'] = "";
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
            if (isset($_POST["password"])){
                if ($_POST["password"] == $_POST["password2"]){
                    $sql->updateUserPassword($email, $_POST["password"]);
                    $_SESSION['email'] = null;
                    $_SESSION['password'] = null;
                    Mailer::sendMail($email, "Changement de mot de passe", "Votre mot de passe a été changé pour votre compte sur le site Comics Sans MS. Si vous n'êtes pas à l'origine de ce changement, veuillez contacter l'administrateur du site.");
                    header("Location: connexion.php");
                }else{
                    $_SESSION['error'] = "Les mots de passe ne correspondent pas";
                    header("Location: changeUserPassword.php");
                }

            }
        }
    }
}else{
    $_SESSION['error'] = "Vous devez vous connecter pour acceder à cette page";
    header("Location: connexion.php");
}

?>

<main>
    <h1>Changer de mot de passe</h1>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <div class="input-field">
            <input type="password" name="password" id="password" required>
            <label for="password">Mot de passe</label>
        </div>
        <div class="input-field">
            <input type="password" name="password2" id="password2" required>
            <label for="password2">Confirmer le mot de passe</label>
        </div>
        <div class="input-field">
            <button type="submit"  class="waves-effect btn">Changer</button>
        </div>
</main>
<?php

require_once "footer.php";
?>

