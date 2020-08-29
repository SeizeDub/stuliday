<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';

?>
<section id="login">
    <h2>Connexion</h2>
    <?php 
    if (isset($_POST['submit_login'])) {
        tryLogin();
    }
    ?>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
        <fieldset>
            <label for="email_login">Adresse e-mail</label>
            <input id="user_email_login" name="email_login" type="email">
        </fieldset>
        <fieldset>
            <label for="password_login">Mot de passe</label>
            <input id="user_password_login" name="password_login" type="password">
        </fieldset>
        <input class="main-button" type="submit" name="submit_login" value="Connexion">
    </form>
</section>
<section>
    <h2>Créer un compte</h2>
    <?php 
    if (isset($_POST['submit_signup'])) {
        trySignup();
    }
    ?>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">
        <fieldset>
            <label for="first_name_signup">Prénom</label>
            <input id="first_name_signup" name="first_name_signup" type="text">
        </fieldset>
        <fieldset>
            <label for="last_name_signup">Nom</label>
            <input id="last_name_signup" name="last_name_signup" type="text">
        </fieldset>
        <fieldset>
            <label for="email_signup">Adresse e-mail</label>
            <input id="email_signup" name="email_signup" type="email">
        </fieldset>
        <fieldset>
            <label for="password_signup">Mot de passe</label>
            <input id="password_signup" name="password_signup" type="password">
        </fieldset>
        <fieldset>
            <label for="password2_signup">Répétez le mot de passe</label>
            <input id="password2_signup" name="password2_signup" type="password">
        </fieldset>
        <input class="main-button" type="submit" name="submit_signup" value="Créer le compte">
    </form>
</section>

<?php 
require 'assets/footer.php';
?>
