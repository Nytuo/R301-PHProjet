<header>
    <div>
        <nav>
            <div class="nav-wrapper">
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <a href="index.php" class="brand-logo center"><img src="assets/images/logo.gif" alt="" width="70%"></a>
                <ul class="right hide-on-med-and-down">
                    <form action="search.php" method="get">
                        <div class="input-field">
                            <input id="search" type="search" name="search" required>
                            <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                            <i class="material-icons">close</i>
                        </div>
                        <input type="submit" style="display: none">
                    </form>
                </ul>
                <ul class="left hide-on-med-and-down">
                    <li><a href="index.php"><i class="material-icons left">home</i>Accueil</a></li>
                    <li><a href="products.php"><i class="material-icons left">book</i>Comics</a></li>
                    <li><a href="cart.php"><i class="material-icons left">shopping_cart</i>Chariot</a></li>
                    <?php
                    session_start();
                    if (isset($_SESSION['email'])) {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Profile</a></li>";
                        echo "<li><a href='logout.php'><i class='material-icons left'>exit_to_app</i>Se déconnecter</a></li>";
                    } else {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Se connecter</a></li>";
                    }
                    ?>
                </ul>
            </div>
        </nav>
        <ul class="sidenav" id="mobile-demo">
            <li><a class="waves-effect" href="index.php"><i class="material-icons">home</i>Accueil</a></li>
            <li><a class="waves-effect" href="products.php"><i class="material-icons">book</i>Comics</a></li>
            <li><a href="cart.php"><i class="material-icons">shopping_cart</i>Chariot</a></li>
            <li><a class="waves-effect" href="connexion.php"><i class="material-icons">account_circle</i>Profile</a>
            </li>
        </ul>
    </div>
</header>

