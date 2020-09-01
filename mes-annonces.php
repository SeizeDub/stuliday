<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <div class="title-row">
        <div class="title-menu">
            <?php if (isset($_SESSION['id'])) {
                if (isset($_GET['old'])): ?>
                <a class="main-button" href="mes-annonces">Annonces actives</a>
                <?php else: ?>
                <a class="main-button" href="?old">Anciennes annonces</a>
                <?php endif; ?>
            <a class="main-button" href="publier-annonce.php">Publier une annonce</a>
            <?php } ?>
        </div>
        <?php if (isset($_GET['old'])): ?>
        <h2>Mes anciennes annonces</h2>
        <?php else: ?>
        <h2>Mes annonces actives</h2>
        <?php endif; ?>

    </div>
    <?php 
    if (isset($_SESSION['id'])):
    ?>
    <div id="annonces">
        <?= displayMyAnnonces(); ?>
    </div>
    <?php else: ?>
    <p>Connectez-vous pour voir vos annonces publiées.</p>
    <?php endif; ?>
</section>

<?php
require 'assets/footer.php';
?>