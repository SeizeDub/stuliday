<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <div class="title-row">
        <div class="title-menu">
            
        </div>
        <h2>Mes reservations</h2>
    </div>
    <div id="annonces"> 
    <?php 
    if (isset($_SESSION['id'])) {
        
        $annonces = getMyReservations($_SESSION['id']);
        if ($annonces == 'error') {
            echo "<p class='error'>Une erreur s'est produite, impossible d'afficher vos réservations.";
        } else if (empty($annonces)) {
            echo "<p>Vous n'avez aucune réservation.";
        } else {
            foreach($annonces as $annonce) {
            ?>
                <article class="annonce-article">
                    <a href="reservation.php?id=<?= $annonce['id_reservation'] ?>">
                        <img src="<?= 'assets/uploads/'.$annonce['image_url'] ?>" alt="">
                        <div>
                            <h3><?= $annonce['title'] ?></h3>
                            <span class="infos"><?= $annonce['category'].' - '.$annonce['city']?></span>
                            <p><?= 'Du '.displayDate($annonce['start_date']).' au '.displayDate($annonce['end_date'])?></p>
                            <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                        </div>
                    </a>
                </article>
            <?php
            }
        }
    } else {
        echo "<p>Connectez-vous pour voir vos réservations.</p>";
    }
    ?>
    </div>
</section>

<?php
require 'assets/footer.php';
?>