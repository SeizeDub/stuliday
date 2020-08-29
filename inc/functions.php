<?php 

function getUser() {
    global $db;
    if ($db) {
        $user_id = htmlspecialchars($_SESSION['id']);
        if ($sql = $db->query("SELECT * FROM users WHERE id = '$user_id'")) {
            return $sql->fetch(PDO::FETCH_ASSOC);
        } else {
            return null;
        }
    } else {
        return null;
    }
}

function trySignup() {
    global $db;
    if ($db) {
        $input_first_name = htmlspecialchars($_POST['first_name_signup']);
        $input_last_name = htmlspecialchars($_POST['last_name_signup']);
        $input_email = htmlspecialchars($_POST['email_signup']);
        $input_password = htmlspecialchars($_POST['password_signup']);
        $input_password2 = htmlspecialchars($_POST['password2_signup']);
        
        if ($sql = $db->query("SELECT * FROM users WHERE email = '$input_email'")) {
            if($sql->rowCount() != 0) {
                $message = "Il y a déjà un compte possédant cet e-mail.";
            } else if (!empty($input_first_name) && !empty($input_last_name) && !empty($input_email) && !empty($input_password) && !empty($input_password2)) {
                if($input_password == $input_password2) {
                    $input_password = password_hash($input_password, PASSWORD_DEFAULT);
                    $sth = $db->prepare("INSERT INTO users(first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
                    if ($sth->execute([$input_first_name, $input_last_name, $input_email, $input_password])) {
                        $_SESSION['id'] = $db->lastInsertId();
                        $_SESSION['email'] = $input_email;
                        header('Location: annonces.php');
                        exit;
                    } else {
                        $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                    }
                } else {
                    $message = "Les mots de passe ne concordent pas.";
                }
            } else {
                $message = "Veuillez remplir tous les champs.";
            }
        } else {
            $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
        }
    } else {
        $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
    }
    echo isset($message) ? '<p class="error">'.$message.'</p>' : null;
}

function tryLogin() {
    global $db;
    if ($db) {
        $input_email = htmlspecialchars($_POST['email_login']);
        $input_password = htmlspecialchars($_POST['password_login']);

        if (!empty($input_email) && !empty($input_password)) {
            if ($sql = $db->query("SELECT * FROM users WHERE email = '$input_email'")) {
                if ($user = $sql->fetch(PDO::FETCH_ASSOC)) {
                    $user_id = $user['id'];
                    $user_email = $user['email'];
                    $user_password = $user['password'];
                    if(password_verify($input_password, $user_password)) {
                        $_SESSION['id'] = $user_id;
                        $_SESSION['email'] = $user_email;
                        header('Location: annonces.php');
                        exit;
                    } else {
                        $message = "Mot de passe incorrect.";
                    }
                } else {
                    $message = "Adresse e-mail inconnue.";
                }
            } else {
                $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
            }
        } else {
            $message = "Veuillez remplir tous les champs.";
        }
    } else {
        $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
    }
    echo isset($message) ? '<p class="error">'.$message.'</p>' : null;
}

function shorten_text($text, $max = 130, $append = '&hellip;') {
    if (strlen($text) <= $max) return $text;
    return preg_replace('/\w+$/', '', substr($text, 0, $max)) . $append;
}

function displayAllAnnonces() {
    global $db;
    if ($db) {
        if ($sql = $db->query("SELECT * FROM annonces WHERE booked IS FALSE AND DATE(end_date) > DATE(NOW())", PDO::FETCH_ASSOC)) {
            if ($annonces = $sql->fetchAll(PDO::FETCH_ASSOC)) {
                foreach($annonces as $annonce) {
                    ?>
                    <article class="annonce-article">
                        <a href="#">
                            <img src="<?= 'assets/uploads/'.$annonce['image_url'] ?>" alt="">
                            <div>
                                <h3><?= $annonce['title'] ?></h3>
                                <span class="infos"><?= $annonce['category'].' - '.$annonce['city']?></span>
                                <p><?= $annonce['description'] ?></p>
                                <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                            </div>
                        </a>
                    </article>
                    <?php
                }
            } else {
                $message = "<p>Nous n'avons aucune annonce à vous proposer. Revenez plus tard, ou publiez votre propre annonce !</p>";
            }
        } else {
            $message = "<p class='error'>Une erreur s'est produite : impossible de charger les annonces. Veuillez réessayer ultérieurement.</p>";
        }
    } else {
        $message = "<p class='error'>Une erreur s'est produite, impossible de charger les annonces. Veuillez réessayer ultérieurement.</p>";
    }
    
    echo isset($message) ? $message : null;
}

function displayMyAnnonces() {
    global $db;
    if ($db) {
        $id = $_SESSION['id'];
        if ($sql = $db->query("SELECT * FROM annonces WHERE booked IS FALSE AND DATE(end_date) > DATE(NOW()) AND author = $id", PDO::FETCH_ASSOC)) {
            if ($annonces = $sql->fetchAll(PDO::FETCH_ASSOC)) {
                foreach($annonces as $annonce) {
                    ?>
                    <article class="annonce-article">
                        <a href="#">
                            <img src="<?= 'assets/uploads/'.$annonce['image_url'] ?>" alt="">
                            <div>
                                <h3><?= $annonce['title'] ?></h3>
                                <span class="infos"><?= $annonce['category'].' - '.$annonce['city']?></span>
                                <p><?= 'Du '.$annonce['start_date'].' au '.$annonce['end_date']?></p>
                                <p>Reservé : <?= $annonce['booked'] ? 'Oui' : 'Non' ?></p>
                                <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                            </div>
                        </a>
                    </article>
                    <?php
                }
            } else {
                $message = "<p>Vous n'avez aucune annonce publiée active en ce moment. Vous pouvez mettre à jour les dates de l'une de vos anciennes annonces pour la rendre visible de nouveau dans la liste des annonces.</p>";
            }
        } else {
            $message = "<p class='error'>Une erreur s'est produite : impossible de charger vos annonces. Veuillez réessayer ultérieurement.</p>";
        }
    } else {
        $message = "<p class='error'>Une erreur s'est produite, impossible de charger lvos annonces. Veuillez réessayer ultérieurement.</p>";
    }
    
    echo isset($message) ? $message : null;
}

function tryPostAnnonce() {
    $title = htmlspecialchars($_POST['input_title']);
    $category = htmlspecialchars($_POST['input_category']);
    $city = htmlspecialchars($_POST['input_city']);
    $address = htmlspecialchars($_POST['input_address']);
    $image = $_FILES['input_image'];
    $description = htmlspecialchars($_POST['input_description']);
    $start_date = htmlspecialchars($_POST['input_start_date']);
    $end_date = htmlspecialchars($_POST['input_end_date']);
    $price = htmlspecialchars($_POST['input_price']);

    if (!empty($title) 
    && !empty($category)
    && !empty($city)
    && !empty($address)
    && !empty($image)
    && !empty($description)
    && !empty($start_date)
    && !empty($end_date)
    && !empty($price)) {
        $compare_date_now = new DateTime("now");
        $compare_date_start = new DateTime($start_date);
        $compare_date_end = new DateTime($end_date);
        if ($compare_date_now > $compare_date_start || $compare_date_start > $compare_date_end) {
            $message = "Les dates saisies sont invalides.";
        } else {
            if (!$image['error']) {
                if($image['size'] <= 1000000) {
                    $dbname = uniqid().'_'.$image['name'];
                    $upload_dir = "./assets/uploads/";
                    $upload_name = $upload_dir . $dbname;
                    $move_result = move_uploaded_file($image['tmp_name'], $upload_name);
                    if ($move_result) {
                        global $db;
                        if ($db) {
                            $sth = $db->prepare("INSERT INTO annonces(author,title,description,city,category,image_url,address,price,start_date,end_date) VALUES (?,?,?,?,?,?,?,?,?,?)");
                            if ($sth->execute([$_SESSION['id'], $title, $description, $city, $category, $dbname, $address, $price, $start_date, $end_date])) {
                                header('Location: annonces.php');
                                exit;
                            } else {
                                $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                            }
                        } else {
                            $message = "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                        }
                    } else {
                        $message = "Une erreur s'est produite lors de l'upload de l'image. Veuillez réessayer ultérieurement.";
                    }
                } else {
                    $message = "L'image fournie est trop volumineuse.";
                }
            } else {
                $message = "Une erreur s'est produite lors de l'upload de l'image. Veuillez réessayer ultérieurement.";
            }
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
    echo isset($message) ? "<p class='error'>".$message."</p>" : null;
}

?>