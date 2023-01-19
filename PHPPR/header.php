<header>
    <div>
        <nav>
            <div class="nav-wrapper">
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                
                <ul class="center hide-on-med-and-down">

                    </ul>
                    <a href="index.php" class="brand-logo left"><img src="assets/images/logo.gif" alt="" width="70%"></a>
                <ul class="right hide-on-med-and-down">

                    <li><a href="index.php"><i class="material-icons left">home</i>Accueil</a></li>
                    <li><a href="products.php"><i class="material-icons left">book</i>Comics</a></li>
                    <li><a href="cart.php"><i class="material-icons left">shopping_cart</i>Chariot</a></li>
                    <?php
                    if (isset($_SESSION['email'])) {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Profile</a></li>";
                        echo "<li><a href='logout.php'><i class='material-icons left'>exit_to_app</i>Se déconnecter</a></li>";
                    } else {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Se connecter</a></li>";
                    }
                    ?>
                    </ul>
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
                <script>
                    window.addEventListener('resize', function() {
                        if (window.innerWidth < 900) {
                            document.querySelector('.brand-logo').classList.add('center');
                            document.querySelector('.brand-logo').classList.remove('left');
                        } else {
                            document.querySelector('.brand-logo').classList.add('left');
                            document.querySelector('.brand-logo').classList.remove('center');
                        }
                    });
                    window.addEventListener('DOMContentLoaded', function() {
                        if (window.innerWidth < 900) {
                            document.querySelector('.brand-logo').classList.add('center');
                            document.querySelector('.brand-logo').classList.remove('left');
                        } else {
                            document.querySelector('.brand-logo').classList.add('left');
                            document.querySelector('.brand-logo').classList.remove('center');
                        }
                    });

                </script>
            </div>
            
        </nav>
        <ul class="sidenav" id="mobile-demo">
        <form action="search.php" method="get">
                        <div class="input-field">
                            <input id="search" type="search" name="search" required>
                            <label class="label-icon" for="search"><i class="material-icons">search</i></label>
                            <i class="material-icons">close</i>
                        </div>
                        <input type="submit" style="display: none">
                    </form>
            <li><a class="waves-effect" href="index.php"><i class="material-icons">home</i>Accueil</a></li>
            <li><a class="waves-effect" href="products.php"><i class="material-icons">book</i>Comics</a></li>
            <li><a href="cart.php"><i class="material-icons">shopping_cart</i>Chariot</a></li>
            <?php
                    if (isset($_SESSION['email'])) {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Profile</a></li>";
                        echo "<li><a href='logout.php'><i class='material-icons left'>exit_to_app</i>Se déconnecter</a></li>";
                    } else {
                        echo "<li><a href='connexion.php'><i class='material-icons left'>account_circle</i>Se connecter</a></li>";
                    }
                    ?>
            </li>
        </ul>
    </div>
</header>

