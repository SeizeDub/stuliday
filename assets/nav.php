<header>
    <div class="background"></div>
    <h1><a href="index.php">Stuliday</a></h1>
    <nav>
        <label for="hamburger-menu"></label>
        <input id="hamburger-menu" type="checkbox" style="display : none">
        <ul>
            <li><a href="annonces.php">Annonces</a></li>
            <?php if (isset($_SESSION['id'])): ?>
            <li><a href="account.php">Mon compte</a></li>
            <li><a href="?logout">Deconnexion</a></li>
            <?php else: ?>
            <li><a href="login.php">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>