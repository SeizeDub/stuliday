<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <div class="title-row">
        <div class="title-menu">
            <?php if (isset($_SESSION['id'])) {?>
            <a class="main-button" href="mes-reservations.php">Mes réservations</a>
            <a class="main-button" href="mes-annonces.php">Mes annonces</a>
            <?php } ?>
        </div>
        <h2>Mon compte</h2>
    </div>
    
    
    <?php if (isset($_SESSION['id'])):
        $user = getUser();
        if ($user) {
            if (isset($_POST['submit'])) {
                $message = updateUser($user);
            }
    ?>  
        <?= isset($message) ? "<p class='error'>".$message."</p>" : null ?>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
            <fieldset>
                <label for="first_name">Prénom *</label>
                <input id="first_name" name="first_name" type="text" value="<?= $user['first_name'] ?>">
            </fieldset>
            <fieldset>
                <label for="last_name">Nom *</label>
                <input id="last_name" name="last_name" type="text" value="<?= $user['last_name'] ?>">
            </fieldset>
            <fieldset>
                <label for="email">Adresse e-mail *</label>
                <input id="email" name="email" type="email" value="<?= $user['email'] ?>">
            </fieldset>
            <fieldset>
                <label for="password">Mot de passe *</label>
                <input id="password" name="password" type="password">
            </fieldset>
            <fieldset>
                <label for="new_password">Nouveau mot de passe</label>
                <input id="new_password" name="new_password" type="password">
            </fieldset>
            <fieldset>
                <label for="new_password2">Répetez le nouveau mot de passe</label>
                <input id="new_password2" name="new_password2" type="password">
            </fieldset>
            <p>Les champs avec * sont obligatoires.</p>
            <input class="main-button" name="submit" type="submit" value="Modifier">
        </form>
        <?php } else {
            echo "<p class='error'>Une erreur s'est produite. Veuillez réessayer ultérieurement.</p>";
        }

    else: ?>
    <p>Vous n'êtes pas connecté.</p>
    <?php endif; ?>
</section>

<?php
require 'assets/footer.php';
?>