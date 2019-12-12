<?php

function send_email($from, $to, $subject, $message)
{
    // Helper function for sending email
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
    $headers .= 'From: '.$from . "\r\n";

    return mail($to, $subject, $message, $headers);
}

function get_page_url()
{
    // Find out the URL of a PHP file

    $url = 'http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['SERVER_NAME'];

    if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '') {
        $url.= $_SERVER['REQUEST_URI'];
    } else {
        $url.= $_SERVER['PATH_INFO'];
    }
    return $url;
}

function rate_limit($ip, $limit_hour = 20, $limit_10_min = 10)
{
    global $db;
    // The number of login attempts for the last hour by this IP address
    $w_ip = sprintf("%u", ip2long($ip));

    $count_hour = $db->get_var("SELECT count(*) FROM reg_login_attempt WHERE ip = '$w_ip' AND ts > SUBTIME(NOW(), '1:00')");

    // The number of login attempts for the last 10 minutes by this IP address
    //$w_ts = SUBTIME(NOW(), '0:10');
    $count_10_min = $db->get_var("SELECT count(*) FROM reg_login_attempt WHERE ip = '$w_ip' AND ts > SUBTIME(NOW(), '0:10')");

    if ($count_hour > $limit_hour || $count_10_min > $limit_10_min) {
        die("Kayıt yapılabilecek sayı limitlerini aştınız.");
    }
}

function rate_limit_tick($ip, $email)
{
    global $db;
    // Create a new record in the login attempt table
    $in_ip = sprintf("%u", ip2long($ip));
    $db->query("INSERT INTO reg_login_attempt (email, ip) VALUES ('$email', '$in_ip')");
}

function save_all($name, $email)
{
    global $db;
    // Create a new record in the login attempt table
    $db->query("INSERT INTO reg_users (name, email) VALUES ('$name', '$email')");
    //$db->debug();
    //die();
    if ($db->insert_id>0) {
        return true;
    } else {
        return false;
    }
}


function email_exist($email)
{
    global $db;
    if ($db->get_var("SELECT count(*) FROM reg_users WHERE email = '$email'")) {
        return true;
    } else {
        return false;
    }
}

function redirect($url)
{
    header("Location: $url");
    exit;
}
