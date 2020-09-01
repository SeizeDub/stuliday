<?php 
require 'inc/connect.php';
require 'inc/functions.php';
require 'assets/head.php';
require 'assets/nav.php';
?>

<section>
    <h2>Publier une annonce</h2>
    <?php if (isset($_SESSION['id'])): 
        if (isset($_POST['submit'])){
            tryPostAnnonce();
        }
    ?>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <label for="input_title">Titre de l'annonce</label>
            <input id="input_title" name="input_title" type="text">
        </fieldset>
        <fieldset>
            <label for="input_category">Type de location</label>
            <select name="input_category" id="input_category">*
                <option value="Studio">Studio</option>
                <option value="T1">T1</option>
                <option value="T2">T2</option>
                <option value="T3+">T3+</option>
                <option value="Maison">Maison</option>
                <option value="Chambre">Chambre</option>
            </select>
        </fieldset>
        <fieldset>
            <label for="input_city">Ville</label>
            <input id="input_city" name="input_city" type="text">
        </fieldset>
        <fieldset>
            <label for="input_address">Adresse</label>
            <input id="input_address" name="input_address" type="text">
        </fieldset>
        <fieldset>
            <label for="input_image">Image</label>
            <input type="file" name="input_image" id="input_image" accept="image/*">
            <img id="input_image_thumbnail" src="" alt="">
        </fieldset>
        <fieldset>
            <label for="input_description">Description</label>
            <textarea name="input_description" id="input_description" cols="30" rows="10"></textarea>
        </fieldset>
        <fieldset>
            <label for="input_start_date">Disponible du</label>
            <input type="date" name="input_start_date" id="input_start_date">
        </fieldset>
        <fieldset>
            <label for="input_end_date">Jusqu'au</label>
            <input type="date" name="input_end_date" id="input_end_date">
        </fieldset>
        <fieldset>
            <label for="input_price">Prix / nuit</label>
            <input type="number" name="input_price" id="input_price">
        </fieldset>
        <input name="submit" class="main-button" type="submit" value="Publier l'annonce">
        <p>L'annonce restera modifiable après publication.</p>
    </form>
    <?php else: ?>
    <p>Connectez-vous pour publier une annonce.</p>
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