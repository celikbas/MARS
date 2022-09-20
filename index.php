<?php
require_once "functions.php";
require_once 'vendor/autoload.php';
include 'config.php';

// Initialise database object and establish a connection
$db = new ezSQL_mysqli(USER, PASS, DB, HOST);

/*--------------------------------------------------
Handle submittings
---------------------------------------------------*/
if (!empty($_POST)) {
    // Is the email address valid?
    if (!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        die('Lütfen geçerli bir eposta adresi giriniz!');
    }
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!strpos($_POST['email'], "itu.edu.tr")) {
        die('Lütfen yalnızca İTÜ (@itu.edu.tr) uzantılı eposta adresinizi kullanınız!');
    }

    // Is name exist?
    if (!isset($_POST['name']) || !filter_var($_POST['name'], FILTER_SANITIZE_STRING)) {
        die('Lütfen isim soyisim giriniz.');
    }
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

    // This will throw an exception if the person is above
    // the allowed login attempt limits (see functions.php for more):
    rate_limit($_SERVER['REMOTE_ADDR']);

    // Record this login attempt
    rate_limit_tick($_SERVER['REMOTE_ADDR'], $email);

    // Send the message to the user
    if (email_exist($email)) {
        die("Kayıtlıdır");
    }

    //Save
    if (save_all($name, $email)) {
        redirect("success.php");
    } else {
        die("Bir sorun var. Sonra tekrar deneyin!");
    }
}

/*--------------------------------------------------
    Output the login form
---------------------------------------------------*/
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?=TITLE?></title>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <!-- The main CSS file -->
        <link href="assets/css/style.css" rel="stylesheet" />
    </head>
    <body>
        <form id="login-register" method="post" action="index.php">
            <h1><?=TITLE?></h1>
            <input type="text" placeholder="Isim Soyisim" name="name" autofocus />
            <p>İsim Soyisim.</p>
            <input type="text" placeholder="@itu.edu.tr eposta adresi" name="email" />
            <p>İTÜ uzantılı eposta adresi</p>
            <input type="submit" value="Submit">
            <span></span>
        </form>

        <footer>
            <p align="center">Kayıt yaparken lütfen yalnızca İTÜ uzantılı eposta adresinizi kullanınız!</p>
            <p align="center"><img src="assets/img/uubf-l_QR.png"></p>
            <div id="tzine-actions"></div>
            <span class="close"></span>
        </footer>
    </body>
</html>
