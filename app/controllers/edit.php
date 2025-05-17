<?php
include 'database.php';

define('ENCRYPTION_KEY', 'rf3MUpnafsflihCNNMrlyDtKnI91LlDN_UmTV9VcEgo=');

function encryptPassword($password) {
    $iv = openssl_random_pseudo_bytes(16);
    $encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    return base64_encode($iv . $encryptedPassword);
}

function decryptPassword($encryptedPassword) {
    $data = base64_decode($encryptedPassword);
    $iv = substr($data, 0, 16);
    $encryptedPassword = substr($data, 16);
    return openssl_decrypt($encryptedPassword, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $site = $_POST['site'];
    $decryptedPassword = $_POST['decryptedPassword'];
    $encryptedPassword = encryptPassword($decryptedPassword);
    
    $stmt = $pdo->prepare("UPDATE passwords SET password = ? WHERE id = ?");
    $stmt->execute([$encryptedPassword, $id]);
    
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    $decryptedPassword = decryptPassword($row['password']);
}
?>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Mot de Passe</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="container mx-auto mt-10">
    <h2 class="text-center text-2xl font-bold mb-5">Modifier le Mot de Passe</h2>
    <form method="POST" action="edit.php" class="max-w-lg mx-auto bg-white p-8 rounded shadow">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="mb-4">
            <label for="site" class="block text-gray-700 font-bold mb-2">Site</label>
            <input type="text" id="site" name="site" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $row['site']; ?>" readonly>
        </div>
        <div class="mb-4">
            <label for="decryptedPassword" class="block text-gray-700 font-bold mb-2">Mot de passe décrypté</label>
            <input type="text" id="decryptedPassword" name="decryptedPassword" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $decryptedPassword; ?>" required>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Modifier</button>
            <a href="index.php" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Retour à l'accueil</a>
        </div>
    </form>
</body>
</html>