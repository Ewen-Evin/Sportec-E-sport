<?php
require_once '/config/config.php';
requireLogin();

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM sportec_galerie WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($photo) {
        echo json_encode($photo);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Photo non trouvée']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant']);
}
?>