<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';

$old = isset($_GET['old']) ? '&old' : '';
if (!empty($_GET['id'])) {
    $id = htmlspecialchars($_GET['id']);
    $annonce = getMyAnnonce($id);
    
    if (!empty($annonce)) {
        // mauvais fuseau horaire (UTC)
        $compare_date_now = date_format(new DateTime("now"), 'Y-m-d');
        $compare_date_end = date_format(new DateTime($annonce['end_date']), 'Y-m-d');
        $oldAnnonce = $compare_date_now > $compare_date_end;
        $url = $annonce['image_url'];
        if (isset($_GET['delete'])) {
            if ($annonce['booked'] && !$oldAnnonce) {
                $message = "Vous ne pouvez pas supprimer une annonce reservée.";
            } else {
               
                $message = deleteAnnonce($id, $url);
            }
        } else if (isset($_POST['submit'])) {
            if ($oldAnnonce) {
                $message = "Vous ne pouvez pas modifier une ancienne annonce.";
            } else if ($annonce['booked']) {
                $message = "Vous ne pouvez pas modifier une annonce reservée.";
            } else {
                $message = modifyAnnonce($id, $url);
            }
        }
    }
}
?>


<section>
    <div class="title-row">
        <div class="title-menu">
            <a class="main-button" href="mes-annonces.php<?= $old ?>">Retour</a>
        </div>
        
        <h2>Mon annonce</h2>
    </div>
    <?php if (!empty($annonce)): ?>
    <p>Réservée <?= $annonce['booked'] ? 'par '.$annonce['first_name'].' '.$annonce['last_name'] : ': non' ?> </p>
    <p class="publish-date">Date de publication : <?= displayTimestamp($annonce['publish_date']) ?></p>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$annonce['id'].$old ?>" method="post" enctype="multipart/form-data">
    <?= isset($message) ? '<p class="error">'.$message.'</p>' : null ?>
    <fieldset>
            <label for="input_title">Titre de l'annonce</label>
            <input id="input_title" name="input_title" type="text" value="<?= $annonce['title'] ?>">
        </fieldset>
        <fieldset>
            <label for="input_category">Type de location</label>
            <select name="input_category" id="input_category" >
                <option value="Studio" <?= $annonce['category'] == 'Studio' ? 'selected' : null ?>>Studio</option>
                <option value="T1" <?= $annonce['category'] == 'T1' ? 'selected' : null ?>>T1</option>
                <option value="T2" <?= $annonce['category'] == 'T2' ? 'selected' : null ?>>T2</option>
                <option value="T3+" <?= $annonce['category'] == 'T3+' ? 'selected' : null ?>>T3+</option>
                <option value="Maison" <?= $annonce['categeory'] == 'Maison' ? 'selected' : null ?>>Maison</option>
                <option value="Chambre"<?= $annonce['tcategory'] == 'Chambre' ? 'selected' : null ?>>Chambre</option>
            </select>
        </fieldset>
        <fieldset>
            <label for="input_city">Ville</label>
            <input id="input_city" name="input_city" type="text" value="<?= $annonce['city'] ?>">
        </fieldset>
        <fieldset>
            <label for="input_address">Adresse</label>
            <input id="input_address" name="input_address" type="text" value="<?= $annonce['address'] ?>">
        </fieldset>
        <fieldset>
            <label for="input_image">Image</label>
            <input type="file" name="input_image" id="input_image" accept="image/*">
            <img id="input_image_thumbnail" src="assets/uploads/<?= $annonce['image_url'] ?>" alt="">
        </fieldset>
        <fieldset>
            <label for="input_description">Description</label>
            <textarea name="input_description" id="input_description" cols="30" rows="10"><?= $annonce['description'] ?></textarea>
        </fieldset>
        <fieldset>
            <label for="input_start_date">Disponible du</label>
            <input type="date" name="input_start_date" id="input_start_date" value="<?= $annonce['start_date'] ?>">
        </fieldset>
        <fieldset>
            <label for="input_end_date">Jusqu'au</label>
            <input type="date" name="input_end_date" id="input_end_date" value="<?= $annonce['end_date'] ?>">
        </fieldset>
        <fieldset>
            <label for="input_price">Prix / nuit</label>
            <input type="number" name="input_price" id="input_price" value="<?= $annonce['price'] ?>">
        </fieldset>
        <div>
            <?php if ($oldAnnonce) { ?>
                <p>Vous ne pouvez pas modifier une ancienne annonce.</p>
                <a class="main-button" href="<?= htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$annonce['id'].$old.'&delete' ?>">Supprimer l'annonce</a>
            <?php } else if ($annonce['booked']) { ?>
                <p>Vous ne pouvez pas modifier ni supprimer une annonce reservée.</p>
            <?php } else { ?>
                <input name="submit" class="main-button" type="submit" value="Modifier l'annonce">
                <a class="main-button" href="<?= htmlspecialchars($_SERVER["PHP_SELF"]).'?id='.$annonce['id'].$old.'&delete' ?>">Supprimer l'annonce</a>
            <?php } ?>
        </div>
        
    </form>
    <?php else: ?>
    <p class="error">Une erreur est survenue, impossible d'afficher cette annonce.</p>
    <?php endif; ?>
</section>
<script>
    document.getElementById('input_image').oninput = (event) => {
        let fileReader = new FileReader();
        fileReader.readAsDataURL(event.target.files[0]);
        fileReader.onloadend = (event) => {
            document.getElementById('input_image_thumbnail').src = event.target.result;
        }
    }
    document.getElementById('input_start_date').oninput = (event) => {
        let date = new Date(event.target.value);
        date.setDate(date.getDate() + 1);
        document.getElementById('input_end_date').min = date.toISOString().split('T')[0];
    }
</script>
<?php 
require 'assets/footer.php';
?>