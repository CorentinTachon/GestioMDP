<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/decrypt.php';

$passwords = [];
$showPopup = false;

try {
    // Modification de la requête pour inclure created_at
    $stmt = $pdo->prepare("SELECT id, site, password, created_at, updated_at FROM passwords");
    $stmt->execute();
    $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    if (empty($passwords)) {
        echo "<tr><td colspan='4' class='text-center py-4'>Aucun mot de passe trouvé.</td></tr>";
        return; // Sortir si aucun mot de passe n'est trouvé
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4' class='text-center py-4'>Erreur de base de données : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
    return; // Sortir en cas d'erreur
}

function joursDepuis($date) {
    if (empty($date)) {
        return 0; // Retourne 0 si la date est vide ou nulle
    }
    try {
        $lastUpdate = new DateTime($date);
        $now = new DateTime();
        return $lastUpdate->diff($now)->days;
    } catch (Exception $e) {
        return 0; // Retourne 0 en cas d'erreur
    }
}

// Détermine s'il faut afficher la popup (basé sur updated_at)
foreach ($passwords as $password) {
    if (joursDepuis($password['updated_at']) > 30) {
        $showPopup = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SecurePass</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function showPassword(button) {
            const input = button.parentElement.querySelector('input');
            if (input.type === "password") {
                input.type = "text";
                button.textContent = "Cacher";
            } else {
                input.type = "password";
                button.textContent = "Voir";
            }
        }

        window.onload = function () {
            <?php if ($showPopup): ?>
                alert("⚠️ Certains mots de passe n'ont pas été changés depuis plus de 30 jours. Pensez à les mettre à jour !");
            <?php endif; ?>
        };
    </script>
</head>
<body class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-5">Vos Mots de Passe</h1>
    <div class="mb-4">
        <a href="../app/controllers/add.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Ajouter un Mot de Passe
        </a>
    </div>
    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-2 px-4">Site</th>
                <th class="py-2 px-4">Mot de Passe</th>
                <th class="py-2 px-4">Historique</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($passwords as $password): ?>
    <?php
        $crypted = $password['password'];
        $decrypted = decrypt($crypted);
        $jours_creation = joursDepuis($password['created_at']);
        $jours_maj = joursDepuis($password['updated_at']);
        
    ?>
    <tr class="border-b">
        <td class="border px-4 py-2"><?php echo htmlspecialchars($password['site']); ?></td>
        <td class="border px-4 py-2">
            <input type="password" value="<?php echo htmlspecialchars($decrypted ?: 'Erreur de déchiffrement'); ?>" class="bg-gray-100 p-2 rounded w-full" readonly>
            <button onclick="showPassword(this)" class="text-sm text-blue-600 underline ml-2">Voir</button>
            <div class="mt-1 text-xs text-gray-500 break-all">
                <span class="font-medium">Crypté :</span> 
                <?php echo htmlspecialchars($crypted); ?>
            </div>
        </td>
        <td class="border px-4 py-2">
            <div class="mb-1">
                <span class="text-sm font-medium">Créé il y a <?php echo $jours_creation; ?> jour<?php echo $jours_creation > 1 ? 's' : ''; ?></span>
            </div>
            <div>
                <span class="<?php echo $jours_maj > 30 ? 'text-red-600 font-bold' : 'text-gray-600'; ?>">
                    Modifié il y a <?php echo $jours_maj; ?> jour<?php echo $jours_maj > 1 ? 's' : ''; ?>
                </span>
                <?php if ($jours_maj > 30): ?>
                    <div class="text-xs text-red-500 mt-1">🔔 Pensez à le changer !</div>
                <?php endif; ?>
            </div>
        </td>
        <td class="border px-4 py-2">
            <a href="../app/controllers/update.php?id=<?php echo $password['id']; ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Modifier</a>
            <a href="../app/controllers/delete.php?id=<?php echo $password['id']; ?>" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Supprimer</a>
        </td>
    </tr>
            <?php endforeach; ?>
            <?php if (empty($passwords)): ?>
                <tr>
                    <td colspan="4" class="text-center py-4">Aucun mot de passe trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>