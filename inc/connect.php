<?php
    session_start();
    // if (empty($_SESSION) && $page =='annonce' || empty($_SESSION) && $page == 'single-annonce'){
    //     header('Location: login.php');
    //     exit;
    // }

    if (isset($_GET['logout'])){
        session_destroy();
        header('Location: index.php');
        exit;
    }
    
    $servername = 'localhost'; $dbname='stuliday';$user='root'; $pass='';
    try {
        $db = new PDO("mysql:host=$servername;dbname=$dbname",$user,$pass);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }   catch(Exception $ex){
        echo "Error : " . $ex->getMessage();
    }
?>