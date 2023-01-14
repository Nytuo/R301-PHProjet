<?php
require_once "head.php";
require_once "header.php";
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


$result = $sql->connectUser($email, $ashPassword);
if (!$result) {
    $_SESSION['error'] = "Wrong password";
    header("Location: connexion.php");
    exit(0);
} else {
    $_SESSION['password'] = $ashPassword;
    $_SESSION['email'] = $email;
    $user = $sql->getUser($email, $ashPassword);
}
?>

<main>
    <h1>Bienvenue <?php echo $user["name"] . ", " . $user["firstName"] ?> <span
                class="sprt s-category-border-rr inline-block"></span></h1>
    <div class="user-info">
        <h1>Voici vos informations personnels<span class="sprt s-category-border-rr inline-block"></span></h1>
        <p>Adresse mail : <?php echo $user["email"] ?></p>
        <p>Nom : <?php echo $user["name"] ?></p>
        <p>Prénom : <?php echo $user["firstName"] ?></p>
        <button class="btn waves-effect" onclick="window.location.href='changeUserPassword.php'">Demander un changement
            de mot de passe
        </button>
        <h1>Adresse<span class="sprt s-category-border-rr inline-block"></span></h1>
        <p><?php echo $user["address"] ?><br><?php echo $user["zip_code"] ?><br><?php echo $user["city"] ?>
            <br>
            <?php echo $user["country"] ?></p>
        <button class="btn waves-effect" onclick="window.location.href = 'changeUserAddress.php';">Changer d'adresse
        </button>
    </div>
</main>

<?php
require_once "footer.php";
?>
