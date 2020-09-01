<?php
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section id="hero">
    <h2>La solution idéale pour vos vacances !</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolor fugit ab unde quo consectetur quia fuga voluptate! Fugit, veritatis sed.</p>
    <div>
        <a class="main-button" href="annonces.php">Voir les annonces</a>
        <?php if (isset($_SESSION['id'])) {?>
        <a class="main-button" href="mes-reservations.php">Mes réservations</a>
        <?php } ?>
    </div>
</section>

<?php 
require 'assets/footer.php';
?>