<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <div class="title-row">
        <div class="title-menu">
            <?php if (isset($_SESSION['id'])) { ?>
            <a class="main-button" href="">Anciennes annonces</a>
            <a class="main-button" href="publier-annonce.php">Publier une annonce</a>
            <?php } ?>
        </div>
        
        <h2>Mes annonces</h2>
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