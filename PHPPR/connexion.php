<?php
include_once "head.php";
include_once "header.php";
session_start();
require_once "SqlApi.php";
$sql = new SqlApi();

if (isset($_POST['password'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (strlen($_POST['password']) > 50) {
        $_SESSION['error'] = "Password too long";
        header("Location: connexion.php");
        exit(0);
    }
    $ashPassword = hash('sha256', $_POST['password']);
    $resultAdmin = $sql->connectAdmin($email, $ashPassword);
    $resultUser = $sql->connectUser($email, $ashPassword);
    if (!$resultAdmin && !$resultUser) {
        $_SESSION['error'] = "Wrong password";
        header("Location: connexion.php");
        exit(0);
    } else {
        $_SESSION['password'] = $ashPassword;//todo en attendant (ou peut etre définitif)
        $_SESSION['email'] = $email;
        if ($resultAdmin){
            header("Location: adminZonne.php");
        }
        if ($resultUser){
            header("Location: userProfile.php");
        }
    }
} else {
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
                header("Location: adminZonne.php");
            }
            if ($resultUser){
                header("Location: userProfile.php");
            }
        }
    }
}

?>
    <main>
        <h1>CONNEXION SANS MS<span class="sprt s-category-border-rr inline-block"></span></h1>

        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <div class="input-field">

            <input type="email" name="email" id="email" class="validate" required>
            <label for="email">Email</label>
            </div>
            <div class="input-field">
            <input type="password" name="password" id="password" class="validate" required>
            <label for="password">Password</label>

            </div>
            <button type="submit" class="btn waves-effect">Connexion</button>
            ou
        <a class="btn waves-effect" href="register.php">Créé un compte</a>
        <a class="btn waves-effect" href="passForgot.php">Mot de passe oublié</a>
        </form>
    </main>

<?php
//open session
session_start();
echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
$_SESSION['error'] = "";

require_once "footer.php";

