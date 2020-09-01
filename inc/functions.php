<?php 

function displayDate($date) {
    return join('/', array_reverse(explode('-', $date)));
}

function displayTimestamp($time) {
    $time = explode(' ', $time);
    return displayDate($time[0]).' à '.$time[1];
}

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

function getAllReservations() {
    global $db;
    if ($db) {

    }
}

function getMyAnnonce($id) {
    global $db;
    if ($db) {
        $sql = "SELECT a.*, u.first_name, u.last_name FROM annonces a LEFT JOIN reservations r ON a.id = r.id_annonce LEFT JOIN users u ON r.id_user = u.id WHERE a.id = $id";
        if ($sql = $db->query($sql)) {
            if ($annonce = $sql->fetch(PDO::FETCH_ASSOC)) {
                return $annonce;
            }
        }
    }
}

function getAnnonce($id) {
    global $db;
    if ($db) {
        $sql = "SELECT a.*, u.first_name, u.last_name FROM annonces a INNER JOIN users u ON a.author = u.id WHERE a.id = $id AND a.booked IS FALSE AND DATE(a.end_date) > DATE(NOW())";
        if ($sql = $db->query($sql)) {
            if ($annonce = $sql->fetch(PDO::FETCH_ASSOC)) {
                return $annonce;
            }
        }
    }
}

function displayAllAnnonces() {
    global $db;
    if ($db) {

        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
            $sql = "SELECT * FROM annonces WHERE booked IS FALSE AND DATE(end_date) > DATE(NOW()) AND author != $id";
        } else {
            $sql = "SELECT * FROM annonces WHERE booked IS FALSE AND DATE(end_date) > DATE(NOW())";
        }
        if ($sql = $db->query($sql)) {
            if ($annonces = $sql->fetchAll(PDO::FETCH_ASSOC)) {
                foreach($annonces as $annonce) {
                    ?>
                    <article class="annonce-article">
                        <a href="annonce.php?id=<?= $annonce['id'] ?>">
                            <img src="<?= 'assets/uploads/'.$annonce['image_url'] ?>" alt="">
                            <div>
                                <h3><?= $annonce['title'] ?></h3>
                                <span class="infos"><?= $annonce['category'].' | '.$annonce['city']?></span>
                                <p><?= $annonce['description'] ?></p>
                                <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                            </div>
                        </a>
                    </article>
                    <?php
                }
            } else {
                $message = "<p>Nous n'avons aucune annonce à vous proposer. Revenez plus tard !</p>";
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
        if (isset($_GET['old'])) {
            $sql = "SELECT * FROM annonces WHERE DATE(end_date) < DATE(NOW()) AND author = $id";
        } else {
            $sql = "SELECT * FROM annonces WHERE DATE(end_date) >= DATE(NOW()) AND author = $id";
        }
        if ($sql = $db->query($sql)) {
            if ($annonces = $sql->fetchAll(PDO::FETCH_ASSOC)) {
                foreach($annonces as $annonce) {
                    ?>
                    <article class="annonce-article">
                        <?php $old = isset($_GET['old']) ? '&old' : '' ?>
                        <a href="mon-annonce.php?id=<?= $annonce['id'].$old ?>">
                            <img src="<?= 'assets/uploads/'.$annonce['image_url'] ?>" alt="">
                            <div>
                                <h3><?= $annonce['title'] ?></h3>
                                <span class="infos"><?= $annonce['category'].' - '.$annonce['city']?></span>
                                <p><?= 'Du '.displayDate($annonce['start_date']).' au '.displayDate($annonce['end_date'])?></p>
                                <p><?= isset($_GET['old']) ? 'A été reservée' : 'Reservée' ?> : <?= $annonce['booked'] ? 'Oui' : 'Non' ?></p>
                                <span class="price"><?= $annonce['price'].'€ / nuit'?></span>
                            </div>
                        </a>
                    </article>
                    <?php
                }
            } else {
                if (isset($_GET['old'])) {
                    $message = "<p>Vous n'avez pas d'anciennes annonces.";
                } else {
                    $message = "<p>Vous n'avez pas d'annonce active en ce moment.";
                }
            }
        } else {
            $message = "<p class='error'>Une erreur s'est produite : impossible de charger vos annonces. Veuillez réessayer ultérieurement.</p>";
        }
    } else {
        $message = "<p class='error'>Une erreur s'est produite, impossible de charger vos annonces. Veuillez réessayer ultérieurement.</p>";
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
                                header('Location: mes-annonces.php');
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

function tryBook($annonce_id, $user_id) {
    global $db;
    if ($db) {
        try {
        $db->beginTransaction();
        $sql = "INSERT INTO reservations(id_annonce, id_user) VALUES (?, ?)";
        $sth = $db->prepare($sql);
        $sth->execute([$annonce_id, $user_id]);
        $sql = "UPDATE annonces SET booked = TRUE WHERE id = $annonce_id";
        $db->exec($sql);
        $db->commit();
        }
        catch(PDOException $ex) {
            $db->rollBack();
            return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
        }
        header('Location: mes-reservations.php');
        exit;
    }
    return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
}

function deleteAnnonce($id, $url) {
    global $db;
    if ($db) {
        try {
            $db->beginTransaction();
            $sql = "DELETE FROM reservations WHERE id_annonce = $id";
            $db->exec($sql);
            $sql = "DELETE FROM annonces WHERE id = $id";
            $db->exec($sql);
            $db->commit();
        } 
        catch(PDOException $ex) {
            $db->rollBack();
            return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
        }
        unlink('assets/uploads/'.$url);
        header('Location: mes-annonces.php');
        exit;
    }
    return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
}

function modifyAnnonce($id, $url) {
    global $db;
    $title = htmlspecialchars($_POST['input_title']);
    $category = htmlspecialchars($_POST['input_category']);
    $city = htmlspecialchars($_POST['input_city']);
    $address = htmlspecialchars($_POST['input_address']);
    $description = htmlspecialchars($_POST['input_description']);
    $start_date = htmlspecialchars($_POST['input_start_date']);
    $end_date = htmlspecialchars($_POST['input_end_date']);
    $price = htmlspecialchars($_POST['input_price']);
    if (!empty($title) 
    && !empty($category)
    && !empty($city)
    && !empty($address)
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
            if ($_FILES['input_image']['size'] && !empty($_FILES['input_image']['name'])) {
                $image = $_FILES['input_image'];
                if (!$image['error']) {
                    if($image['size'] <= 1000000) {
                        $dbname = uniqid().'_'.$image['name'];
                        $upload_dir = "./assets/uploads/";
                        $upload_name = $upload_dir . $dbname;
                        $move_result = move_uploaded_file($image['tmp_name'], $upload_name);
                        if ($move_result) {
                            if ($db) {
                                $sth = $db->prepare("UPDATE annonces SET title = ?,description = ?,city = ?,category = ?,image_url = ?,address = ?,price = ?,start_date = ?,end_date = ? WHERE id = $id");
                                if ($sth->execute([$title, $description, $city, $category, $dbname, $address, $price, $start_date, $end_date])) {
                                    unlink('assets/uploads/'.$url);
                                    header('Location: mes-annonces.php');
                                    exit;
                                } else {
                                    return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                                }
                            } else {
                                return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                            }
                        } else {
                            return "Une erreur s'est produite lors de l'upload de l'image. Veuillez réessayer ultérieurement.";
                        }
                    } else {
                        return "L'image fournie est trop volumineuse.";
                    }
                } else {
                    return "Une erreur s'est produite lors de l'upload de l'image. Veuillez réessayer ultérieurement.";
                }
            } else {
                if ($db) {
                    $sth = $db->prepare("UPDATE annonces SET title = ?,description = ?,city = ?,category = ?,address = ?,price = ?,start_date = ?,end_date = ? WHERE id = $id");
                    if ($sth->execute([$title, $description, $city, $category, $address, $price, $start_date, $end_date])) {
                        header('Location: mes-annonces.php');
                        exit;
                    } else {
                        return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                    }
                } else {
                    return "Une erreur s'est produite. Veuillez réessayer ultérieurement.";
                }
            }
        }
    } else {
        return "Veuillez remplir tous les champs.";
    }
}
?>