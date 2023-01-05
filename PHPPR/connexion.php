<?php
include_once "head.php";
include_once "header.php";
?>
    <main>
        <h1>COMICS SANS ADMIN</h1>
        <!-- conexion form -->

        <form action="adminZonne.php" method="post">
            <div class="input-field">

            <input type="email" name="email" id="email" class="validate" required>
            <label for="email">Email</label>
            </div>
            <div class="input-field">
            <input type="password" name="password" id="password" class="validate" required>
            <label for="password">Password</label>

            </div>
            <input type="submit" class="btn waves-effect" value="Connexion">
        </form>

    </main>

<?php
//open session
session_start();
echo "<p style='color: red'>" . $_SESSION['error'] . "</p>";
$_SESSION['error'] = "";

require_once "footer.php";

