<?php

date_default_timezone_set('America/Recife');

require_once ("conn.php");
require_once ("encrypt.php");

$login = trim(strtolower($_POST['Usuário']));
$passwd = trim(strtolower($_POST['Senha']));

$con = initiateConnect();
$check = returnUserData($con, $login);

if ($check !== null && secure_decrypt($check[2]) == $passwd) {
    session_start();
} else {
    if ($check == null) {
        $con->close();
        header('location:../Login/?cod=incorrectLogin');
    } elseif (secure_decrypt($check[2]) !== $passwd) {
        if (alterUserTry($con, $login, 'select')[0] == null) {
            alterUserTry($con, $login, 0);
        } else {
            alterUserTry($con, $login, alterUserTry($con, $login, 'select')[0] + 1);
        }

        $con->close();
        header("location:../Login/?cod=incorrectPasswd");
    } elseif (alterUserTry($con, $login, 'select')[0] == 5) {
        $con->close();
        header("location:../Login/?cod=loginTriesExceeded");
    }
}

if (session_status() == PHP_SESSION_ACTIVE) {
    setcookie('userTimeout', 1, 0, '/');
    $_SESSION['user'] = $check[0];
    $_SESSION['newsession'] = $login;
    $_SESSION['last-action'] = date("Y-m-d H:i:s", time());
    $_SESSION['is_superuser'] = $check[9];
    alterUserLoginTime($con, $login, $_SESSION['last-action'], 1);
    alterUserTry($con, $login, 0);

    $con->close();

    if ($check[9] == 1) {
        header("location:../../admin/");
    } else {
        header("location:../../cliente/");
    }

    // if ($login == "admin") {
    //     
    // } else {
    //     header("location:../../cliente/");
    // }

}