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


$result = $sql->connectUser($email, $ashPassword);
if (!$result) {
    $_SESSION['error'] = "Wrong password";
    header("Location: connexion.php");
    exit(0);
} else {
    $_SESSION['password'] = $ashPassword;//todo en attendant (ou peut etre définitif)
    $_SESSION['email'] = $email;
}