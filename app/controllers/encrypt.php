<?php
// app/controllers/encrypt.php

function encrypt($data) {
    // Clé et vecteur d'initialisation à personnaliser mais identiques en encrypt et decrypt
    $key = 'votre_cle_secrete_1234567890';
    $iv = substr(hash('sha256', 'votre_iv_unique'), 0, 16); // AES-256-CBC nécessite 16 octets pour l'IV
    $encrypted = openssl_encrypt($data, "AES-256-CBC", $key, 0, $iv);
    if ($encrypted === false) {
        // En cas d'erreur, vous pouvez logguer ou gérer l'erreur
        return null;
    }
    return $encrypted;
}
?>
