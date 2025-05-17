<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/encrypt.php';
require_once __DIR__ . '/decrypt.php';

$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $site = $_POST['site'];
    $new_password = $_POST['new_password'];
    $updated_at = $_POST['updated_at']; // Récupération de la date saisie

    $encrypted_password = encrypt($new_password);

    $stmt = $pdo->prepare("UPDATE passwords SET site = ?, password = ?, updated_at = ? WHERE id = ?");
    $stmt->execute([$site, $encrypted_password, $updated_at, $id]);

    $updateSuccess = true;
}

// Gestion du GET pour afficher les données
$id = $_GET['id'] ?? ($_POST['id'] ?? null);
if (!$id) {
    header('Location: ../../public/index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ?");
$stmt->execute([$id]);
$password = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$password) {
    header('Location: ../../public/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Mot de Passe</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("new_password");
            var toggleIcon = document.getElementById("togglePasswordIcon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.innerHTML = "&#128065;";
            } else {
                passwordField.type = "password";
                toggleIcon.innerHTML = "&#128065;";
            }
        }
    </script>
</head>
<body class="container mx-auto mt-10">

    <h2 class="text-center text-2xl font-bold mb-5">Modifier le Mot de Passe</h2>

    <form action="update.php" method="POST" class="max-w-lg mx-auto">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($password['id'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="mb-4">
            <label for="site" class="block text-gray-700 text-sm font-bold mb-2">Site:</label>
            <input type="text" name="site" id="site" value="<?php echo htmlspecialchars($password['site'], ENT_QUOTES, 'UTF-8'); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
            <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">Mot de Passe Actuel:</label>
            <input type="password" name="current_password" id="current_password" value="<?php echo htmlspecialchars(decrypt($password['password']), ENT_QUOTES, 'UTF-8'); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
        </div>
        <div class="mb-4">
            <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">Nouveau Mot de Passe:</label>
            <div class="relative">
                <input type="password" name="new_password" id="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <span id="togglePasswordIcon" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">&#128065;</span>
            </div>
            <div class="mb-4">
                <label for="updated_at" class="block text-gray-700 text-sm font-bold mb-2">Dernière Modification:</label>
                <input type="date" name="updated_at" id="updated_at" value="<?php echo htmlspecialchars(date('Y-m-d', strtotime($password['updated_at'])), ENT_QUOTES, 'UTF-8'); ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Mettre à jour</button>
            <a href="../../public/index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Retour à l'accueil</a>
        </div>
    </form>

    <?php if ($updateSuccess): ?>
        <script>
            alert('Mot de passe mis à jour avec succès !');
            window.location.href = '/GestioMDP/public/index.php';
        </script>
    <?php endif; ?>

</body>
</html>
