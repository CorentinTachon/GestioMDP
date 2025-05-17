<?php
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site = $_POST['site'] ?? '';
    $password = $_POST['password'] ?? '';
    $created_at = $_POST['created_at'] ?? date('Y-m-d H:i:s'); // Date actuelle par défaut
    
    if (!empty($site) && !empty($password)) {
        // Modification de la fonction d'enregistrement pour inclure la date de création
        $encrypted = encrypt($password);
        $stmt = $pdo->prepare("INSERT INTO passwords (site, password, created_at, updated_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$site, $encrypted, $created_at, date('Y-m-d H:i:s')]);
        
        header('Location: ../../public/index.php²&');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="container mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-5">Ajouter un mot de passe</h1>
    <form method="POST" class="max-w-md">
        <div class="mb-4">
            <label for="site" class="block text-gray-700">Site :</label>
            <input type="text" id="site" name="site" required class="w-full px-3 py-2 border rounded">
        </div>
        
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Mot de passe :</label>
            <input type="password" id="password" name="password" required class="w-full px-3 py-2 border rounded">
        </div>
        
        <div class="mb-4">
            <label for="created_at" class="block text-gray-700">Date de création réelle :</label>
            <input type="datetime-local" id="created_at" name="created_at" 
                   value="<?= date('Y-m-d\TH:i') ?>" class="w-full px-3 py-2 border rounded">
            <p class="text-xs text-gray-500 mt-1">Spécifiez quand vous avez réellement créé ce mot de passe</p>
        </div>
        
        <div class="flex items-center justify-between">
            <button  a href="../../public/index.php"type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Enregistrer
            </button>
            <a href="../../public/index.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Annuler
            </a>
        </div>
    </form>
</body>
</html>