<?php
define('BASE_URL', '/GestioMDP');
require_once __DIR__ . '../../../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: ' . BASE_URL . '/public/index.php');
    exit;
}

$stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ?");
$stmt->execute([$id]);

header('Location: ' . BASE_URL . '/public/index.php');
exit;
?>