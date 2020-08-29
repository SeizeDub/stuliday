<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <div class="title-row">
        <div class="title-menu">
            <?php if (isset($_SESSION['id'])): ?>
            <a class="main-button" href="mes-reservations.php">Mes réservations</a>
            <?php else: ?>
            <p>Connectez-vous pour voir vos réservations.</p>
            <?php endif; ?>
        </div>
        
        <h2>Liste des annonces</h2>
    </div>
    <div id="annonces">
        <?= displayAllAnnonces() ?>
    </div>
</section>

<?php 
require 'assets/footer.php';
?>