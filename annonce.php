<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';

if (!empty($_GET['id'])) {
    $annonce = getAnnonce($_GET['id']);
    if (!empty($annonce) && isset($_SESSION['id']) && $_SESSION['id'] == $annonce['author']) {
        $self = true;
    }
    if (isset($_GET['book'])) {
        if (!isset($self) && isset($_SESSION['id'])) {
            $bookerror = tryBook($annonce['id'],$_SESSION['id']);
        } else {
            $bookerror = "Une erreur s'est produite. Vous ne pouvez pas effectuer cette action.";
        }
    }
}
?>


<section>
    <div class="title-row">
        <div class="title-menu">
            <?php if (isset($self)) {?>
            <p>Vous êtes l'auteur de cette annonce.</p>
            <?php } else if (!isset($_SESSION['id'])) { ?>
            <p>Connectez-vous pour réserver cette annonce.</p>
            <?php } ?>
            <a class="main-button" href="annonces.php">Retour aux annonces</a>
        </div>
        
        <h2>Description de l'annonce</h2>
    </div>
    <div id="annonce" class="annonce-article">
            <?php 
                if (!empty($annonce) && !isset($bookerror)) {
                    ?>
                    <img src="assets/uploads/<?=$annonce['image_url']?>" alt="">
                    <h3><?=$annonce['title']?></h3>
                    <span class="infos"><?= $annonce['category']." | ".$annonce['address']." à ".$annonce['city']?></span>
                    <span class="proprietaire"><?= $annonce['first_name'].'  '.$annonce['last_name'] ?></span>
                    <p><?= $annonce['description'] ?></p>
                    <span class="date"><?= 'Disponible du '.displayDate($annonce['start_date']).' au '.displayDate($annonce['end_date']) ?></span>
                    <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                    <?php
                } else if (isset($bookerror)) {
                    echo "<p class='error'>".$bookerror."</p>";
                } else {
                    echo "<p class='error'>Une erreur s'est produite. Impossible d'afficher cette annonce.</p>";
                }
            ?>
    </div>
    <?php if (!empty($annonce) && isset($_SESSION['id'])) { ?>
    <a class="main-button" href="?id=<?=$annonce['id'] ?>&book">Réserver</a>
    <?php } ?>
</section>

<?php 
require 'assets/footer.php';
?>