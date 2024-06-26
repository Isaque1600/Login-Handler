<?php

require (__DIR__ . '/loadDotenv.php');

function formatDados($arr)
{
    foreach ($arr as $key => $value) {
        $value = trim($value);
        $arr[$key] = empty($value) ? 'NULL' : "'" . $value . "'";
    }

    return $arr;
}

function returnKeyIfElementNotNull($key, $element)
{
    return ($element != 'NULL') ? strtoupper($key) : null;
}

function initiateConnect()
{
    $hostname = $_ENV['DB_HOST'];
    $username = $_ENV['DB_USERNAME'];
    $passwd = $_ENV['DB_PASSWORD'];
    $database = $_ENV['DB_DATABASE'];

    return new mysqli($hostname, $username, $passwd, $database);
}

function testConnect($con)
{
    if ($con->connect_error) {
        return $con->connect_error;
    } else {
        return "success";
    }
}

function registerUserData($con, $arrDados, $throw = 'cadastro')
{
    $arrDados = formatDados($arrDados);

    try {
        if ($throw == 'cadastro') {
            $registerLogin = $con->query("INSERT INTO USUARIOS (
                LOGIN,
                SENHA, 
                SITUACAO, 
                TIPO
                ) 
                VALUES (" . $arrDados['nome'] . ",
                    " . $arrDados['senha'] . ",
                    " . $arrDados['situacao'] . ",
                    " . $arrDados['tipo'] . ")");
        } elseif ($throw == 'alteracao') {
            $inputs = array_map('returnKeyIfElementNotNull', array_keys($arrDados), $arrDados);

            $inputs = array_filter($inputs, function ($value) {
                return $value != null && $value == 'NOME' || $value == 'SENHA' || $value == 'TIPO' || $value == 'SITUACAO';
            });

            $src = "";

            foreach ($inputs as $key => $value) {
                $value = ($value == 'NOME') ? 'LOGIN' : $value;
                if ($key == array_key_last($inputs)) {
                    $src .= " $value = " . $arrDados[strtolower($login = ($value == 'LOGIN') ? 'nome' : $value)];
                } else {
                    $src .= " $value = " . $arrDados[strtolower($login = ($value == 'LOGIN') ? 'nome' : $value)] . ",";
                }
            }

            $registerLogin = $con->query("UPDATE
            USUARIOS
            SET
                $src
            WHERE
                Login = " . $arrDados['nome']);
        }

        return $registerLogin;
    } catch (Exception $e) {
        return [$e->getMessage(), $e->getCode(), $src];
    }
}

function registerPersonData($con, $arrDados, $throw = 'cadastro')
{
    $arrDados = formatDados($arrDados);

    try {

        if ($throw == 'cadastro') {
            $registerPessoa = $con->query("INSERT INTO PESSOAS (
                NOME,
                RAZAO,
                LOGRADOURO,
                NUMERO,
                BAIRRO,
                CIDADE,
                CEP,
                UF,
                CNPJ,
                IE,
                CONTATO,
                SISTEMA,
                SERIAL,
                OBS,
                VEN_CERT,
                EMAIL,
                SITUACAO,
                TEF,
                NFE,
                NFCE,
                CONTADOR,
                EMAIL_BACKUP,
                SENHA_BACKUP,
                TIPO
            )
            VALUES (" . $arrDados['nome'] . ",
            " . $arrDados['razao'] . ",  
            " . $arrDados['logradouro'] . ", 
            " . $arrDados['numero'] . ", 
            " . $arrDados['bairro'] . ", 
            " . $arrDados['cidade'] . ",
            " . $arrDados['cep'] . ",
            " . $arrDados['uf'] . ",
            " . $arrDados['cnpj'] . ",
            " . $arrDados['ie'] . ",
            " . $arrDados['contato'] . ",
            " . $arrDados['sistema'] . ",
            " . $arrDados['serial'] . ",
            " . $arrDados['obs'] . ",
            " . $arrDados['ven_cert'] . ",
            " . $arrDados['email'] . ",
            " . $arrDados['situacao'] . ",
            " . $arrDados['tef'] . ",
            " . $arrDados['nfe'] . ",
            " . $arrDados['nfce'] . ",
            " . $arrDados['contador'] . ",
            " . $arrDados['email_backup'] . ",
            " . $arrDados['senha_backup'] . ",
            " . $arrDados['tipo'] . ")");
        } elseif ($throw == 'alteracao') {

            $inputs = array_map('returnKeyIfElementNotNull', array_keys($arrDados), $arrDados);

            $inputs = array_filter($inputs, function ($value) {
                return $value != null && $value != 'SENHA';
            });

            $src = "";

            foreach ($inputs as $key => $value) {
                if ($key == array_key_last($inputs)) {
                    $src .= " $value = " . $arrDados[strtolower($value)];
                } else {
                    $src .= " $value = " . $arrDados[strtolower($value)] . ",";
                }
            }

            $registerPessoa = $con->query("UPDATE
            PESSOAS
            SET
                $src  
            WHERE
                NOME = " . $arrDados['nome']);
        }

        return $registerPessoa;
    } catch (Exception $e) {

        return [$e->getMessage(), $e->getCode(), $src];
    }
}

function returnUserData($con, $login)
{
    $logins = $con->query("SELECT * FROM USUARIOS WHERE Login = '$login'");
    if ($logins) {
        return $logins->fetch_row();
    }
    return null;
}

function alterUserLoginTime($con, $login, $value, $cod)
{
    $logins = $con->query('UPDATE USUARIOS SET loginTime = \'' . $value . '\', logado = \'' . $cod . '\' WHERE Login = \'' . $login . '\' ');
    if ($logins == true) {
        $resp = array('success' => true);
    } else {
        $resp = array('success' => false);
    }

    echo json_encode($resp);
}

function alterUserTry($con, $login, $request = 'update', $value = 0)
{
    if ($request == 'select') {
        $try = $con->query("SELECT Tentativa FROM USUARIOS WHERE login = '$login'");
        if ($try) {
            return $try->fetch_row();
        }
        return null;
    } else {
        $try = $con->query("UPDATE USUARIOS SET Tentativa = $value");
        return $try;
    }
}