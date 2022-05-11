<?php
include 'config.php';

$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="Lütfen giriş yapınız"');
    header('HTTP/1.0 401 Unauthorized');
    die("Yetkiniz bulunmamaktadır!");
}
header('Content-Type: text/html; charset=utf-8');
require_once "functions.php";
require_once 'vendor/autoload.php';

// Initialise database object and establish a connection
$db = new ezSQL_mysqli(USER, PASS, DB, HOST);

/*--------------------------------------------------
Handle submittings
---------------------------------------------------*/
if (!empty($_POST)) {
    if ($_POST['deleteall'] == 1) {
        $registered = filter_var($_POST['registered'], FILTER_SANITIZE_STRING);
        $sql = "DELETE FROM `reg_users` WHERE registered <= '$registered'";
        if ($db->query($sql)) {
            $allDeleted = "Hepsi silindi!\n";
        } else {
            $allDeleted = "Bir sorun mu var? Herhalde silecek birşey yoktu!\n";
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
        <title>UUBF Öğrenci Listesi Kayıt Sistemi</title>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
        <!-- The main CSS file -->
        <link href="assets/css/style.css" rel="stylesheet" />
    </head>
    <body>
        <div id="login-register">
            <h1><?=TITLE?></h1>
            <textarea style="color: black; background-color: transparent; border: 1px solid #333; padding: 6px;" rows="14" cols="65"><?php
                if (isset($allDeleted)) {
                    echo $allDeleted;
                }
                if ($all = $db->get_results("SELECT * FROM reg_users")) {
                    foreach ($all as $user) {
                        echo $user->name . " &#60;" . $user->email . "&#62;\n";
                    }
                } else {
                    echo "BOŞ";
                }
            ?></textarea>
            <span></span>
            <div class="sub_div"><a href="liste.php"><input type="submit" value="Listeyi Güncelle" /></a></div>
        </div>

        <span></span>

        <footer>
            <form method="post" action="liste.php">
                <h1>Hepsi Silinsin mi?</h1>
                <input type="hidden" name="deleteall" value="1">
                <input type="hidden" name="registered" value="<?=$user->registered?>">
                <input type="submit" value="Sil">
                <span></span>
            </form>
	    <p><a href="<?=LIST_URL?>" target="_blank"><?=LIST_URL?> <span class="glyphicon glyphicon-new-window"></span></a></p>
            <div id="tzine-actions"></div>
            <span class="close"></span>
        </footer>

    </body>
</html>
