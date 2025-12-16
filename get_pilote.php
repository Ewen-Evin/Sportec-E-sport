<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM sportec_pilotes WHERE id = ?");
    $stmt->execute([$id]);
    $pilote = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pilote) {
        echo json_encode($pilote);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pilote non trouvé']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
}
?>