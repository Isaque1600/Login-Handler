<?php

date_default_timezone_set('America/Recife');

require_once ('conn.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (isset($_POST['checkLoginTime'])) {
    $_SESSION['last-action'] = date('Y-d-m H:i:s', time());
    $con = initiateConnect();

    alterUserLoginTime($con, $_SESSION['newsession'], $_SESSION['last-action'], 1);

    $con->close();
}
