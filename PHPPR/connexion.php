<div>
    <!-- conexion form -->

    <form action="adminZonne.php" method="post">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" value="Connexion">
    </form>

</div>

<?php
//open session
session_start();
echo "<p style='color: red'>".$_SESSION['error']."</p>";
$_SESSION['error'] = "";

