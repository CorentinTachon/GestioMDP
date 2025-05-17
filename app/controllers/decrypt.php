<?php
// app/controllers/decrypt.php

function decrypt($data) {
    $key = 'votre_cle_secrete_1234567890'; // Doit être identique à celui utilisé pour l'encrypt
    $iv = substr(hash('sha256', 'votre_iv_unique'), 0, 16);
    $decrypted = openssl_decrypt($data, "AES-256-CBC", $key, 0, $iv);
    if ($decrypted === false) {
        // Gestion d'erreur de décryptage éventuel
        return null;
    }
    return $decrypted;
}
?>
