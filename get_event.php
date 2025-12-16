<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM sportec_events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($event) {
        echo json_encode($event);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Événement non trouvé']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
}
?>