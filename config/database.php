<?php
// config/database.php

require_once __DIR__ . '/../app/controllers/encrypt.php';
require_once __DIR__ . '/../app/controllers/decrypt.php';

$databasePath = __DIR__ . '/../passwords.db';

// Création explicite du fichier si inexistant
if (!file_exists($databasePath)) {
    if (!touch($databasePath)) {
        die("Impossible de créer le fichier de base de données. Vérifiez les permissions.");
    }
    chmod($databasePath, 0666);
}

$dsn = "sqlite:" . $databasePath;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérification de l'existence de la table
    $tableExists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='passwords'")->fetch();

    if (!$tableExists) {
        $pdo->exec("
            CREATE TABLE passwords (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                site TEXT NOT NULL,
                password TEXT NOT NULL,
                created_at TEXT DEFAULT (datetime('now')),
                updated_at TEXT DEFAULT (datetime('now'))
            )
        ");
        error_log("Table 'passwords' créée avec succès");
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
 // Vérifiez et ajoutez la colonne created_at si elle n'existe pas
try {
    $pdo->exec("ALTER TABLE passwords ADD COLUMN created_at TEXT DEFAULT (datetime('now'))");
} catch (PDOException $e) {
    // La colonne existe peut-être déjà, on ignore l'erreur
}
function enregistrerMotDePasse($site, $password, $created_at = null) {
    global $pdo;
    $encrypted = encrypt($password);
    $created_at = $created_at ?: date('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("INSERT INTO passwords (site, password, created_at, updated_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$site, $encrypted, $created_at, date('Y-m-d H:i:s')]);
}

function afficherMotsDePasse() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM passwords");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['site']) . "</td>
                <td>" . htmlspecialchars($row['password']) . "</td>
                <td>" . htmlspecialchars($row['updated_at']) . "</td>
                <td>
                    <a href='/app/controllers/edit.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Modifier</a>
                    <a href='delete.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Supprimer</a>
                </td>
              </tr>";
    }
}

function recupererMotDePasse($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT password FROM passwords WHERE id = ?");
    $stmt->execute([$id]);
    $encrypted = $stmt->fetchColumn();
    return decrypt($encrypted);
}

function supprimerMotDePasse($id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ?");
    $stmt->execute([$id]);
}

function modifierMotDePasse($id, $site, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE passwords SET site = ?, password = ?, updated_at = datetime('now') WHERE id = ?");
        $stmt->execute([$site, $password, $id]);
        return true;
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du mot de passe: " . $e->getMessage());
        return false;
    }
}