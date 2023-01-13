<?php
session_start();


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>PAIEMENT SECURISE PAR LES TECHNICIENS DE L'EXTREME<span class="sprt s-category-border-rr inline-block"></span></h1>
//TODO PAYPAL

<?php
//if paypal OK display success message if not display error message
if (isset($_GET['success']) && $_GET['success'] == "true") {
    echo "<h1>Payment successful</h1>";
} else {
    echo "<h1>Payment failed</h1>";
}
?>
</body>
</html>
