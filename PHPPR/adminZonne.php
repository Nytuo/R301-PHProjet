<?php

session_set_cookie_params(360000,'/');
session_start();

// open sql connection


// filter input
if (isset($_SESSION['password']))
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
// pasword max 50 char
if (strlen($_POST['password']) < 50) {
    $_SESSION['error'] = "Password too long";
    header("Location: connexion.php");
    exit(0);

}

$ashPassword = hash('sha256', $_POST['password']);


$dbhost = "localhost";
$dbuser = "root";
$dbpass = "1234";
$dbname ="ComicsSansMS";
$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname) or die ("Error connecting to database");
// do a pre generate sql query to thck if the admin password is correct
$sqlQuery="SELECT count(*) FROM admin WHERE email=':email' AND password=':password'";

//prepare the query
$stmt = $db->prepare($sqlQuery, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
//execute the query
$stmt->execute(array(':email' => $_POST['email'], ':password' => $_POST['password']));
//fetch the result
$result = $stmt->fetch();

if ($result[0] == 0) {
    // if the password is correct, open the admin zone
    $_SESSION['error'] = "Wrong password";
    header("Location: connexion.php");
    closeConnection($db);
    exit(0);
}
// save

// do a fonction to close the sql connection
$_SESSION['password'] = $ashPassword;//todo en attendant (ou peut etre définitif)

function closeConnection($db) {
    $db->close();
}

