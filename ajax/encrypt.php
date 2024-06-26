<?php

require (__DIR__ . '/loadDotenv.php');

function secure_encrypt($data)
{
    $iv_tamanho1 = openssl_cipher_iv_length('aes-256-cbc');
    $iv_tamanho2 = openssl_cipher_iv_length('aes-128-cbc');
    $iv1 = openssl_random_pseudo_bytes($iv_tamanho1);
    $iv2 = openssl_random_pseudo_bytes($iv_tamanho2);

    $firstEncrypt = openssl_encrypt($data, 'aes-256-cbc', $_ENV['FIRSTKEY'], OPENSSL_RAW_DATA, $iv1);
    $secondEncrypt = openssl_encrypt($firstEncrypt, 'aes-128-cbc', $_ENV['SECONDKEY'], OPENSSL_RAW_DATA, $iv2);
    $thirdEncrypt = hash_hmac('sha3-512', $secondEncrypt, $_ENV['THIRDKEY'], true);

    $output = base64_encode($iv1 . $iv2 . $thirdEncrypt . $secondEncrypt);

    return $output;
}

function secure_decrypt($input)
{
    $mix = base64_decode($input);

    $iv_tamanho1 = openssl_cipher_iv_length('aes-256-cbc');
    $iv_tamanho2 = openssl_cipher_iv_length('aes-128-cbc');

    $iv1 = substr($mix, 0, $iv_tamanho1);
    $iv2 = substr($mix, $iv_tamanho1, $iv_tamanho2);
    $secondEncrypt = substr($mix, -32);
    $thirdEncrypt = substr($mix, 32, 64);

    $firstEncrypt = openssl_decrypt($secondEncrypt, 'aes-128-cbc', $_ENV['SECONDKEY'], OPENSSL_RAW_DATA, $iv2);
    $data = openssl_decrypt($firstEncrypt, 'aes-256-cbc', $_ENV['FIRSTKEY'], OPENSSL_RAW_DATA, $iv1);
    $thirdEncryptNew = hash_hmac('sha3-512', $secondEncrypt, $_ENV['THIRDKEY'], true);

    if (hash_equals($thirdEncrypt, $thirdEncryptNew) == true) {
        return $data;
    }

    return "fail";
}
