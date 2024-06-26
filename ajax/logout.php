<?php

require_once("conn.php");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

try {
    if (isset($_SESSION['last-action'])) {
        $con = initiateConnect();

        alterUserLoginTime($con, $_SESSION['newsession'], date('Y-d-m H:i:s', time()), 0);

        $con->close();
    }
} catch (Exception $e) {
    die($e);
}

// Apaga todas as variáveis da sessão
$_SESSION = [];

// Se é preciso matar a sessão, então os cookies de sessão também devem ser apagados.
// Nota: Isto destruirá a sessão, e não apenas os dados!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Por último, destrói a sessão
session_destroy();

if (isset($_GET['cod'])) {
    header('location: /Login/?cod=' . $_GET['cod']);
}

header('location: /');