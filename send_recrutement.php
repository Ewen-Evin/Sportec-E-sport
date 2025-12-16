<?php
require_once '/home/ewenevh/config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupération des données
    $pole = $_POST['pole'] ?? '';
    $pseudo = $_POST['pseudo'] ?? '';
    $age = $_POST['age'] ?? '';
    $pays = $_POST['pays'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $email = $_POST['email'] ?? '';
    $discord = $_POST['discord'] ?? '';
    $experience = $_POST['experience'] ?? '';
    $jeux = $_POST['jeux'] ?? '';
    $disponibilite = $_POST['disponibilite'] ?? '';
    $presentation = $_POST['presentation'] ?? '';
    $objectifs = $_POST['objectifs'] ?? '';
    
    // Validation basique
    if (empty($pseudo) || empty($age) || empty($pays) || empty($email) || empty($discord)) {
        throw new Exception('Tous les champs obligatoires doivent être remplis');
    }
    
    // Préparation de l'email
    $to = "contact@ewenevin.fr"; // Remplacez par votre email
    $subject = "Nouvelle candidature SPORTEC - " . $pseudo;
    
    $message = "
    Nouvelle candidature reçue pour le pôle : " . $pole . "
    
    Informations du candidat :
    -------------------------
    Pseudo : " . $pseudo . "
    Âge : " . $age . "
    Pays : " . $pays . "
    Ville : " . $ville . "
    Email : " . $email . "
    Discord : " . $discord . "
    
    Expérience : " . $experience . "
    Jeux pratiqués : " . $jeux . "
    Disponibilités : " . $disponibilite . "
    
    Présentation et motivation :
    " . $presentation . "
    
    Objectifs :
    " . $objectifs . "
    
    Date de candidature : " . date('d/m/Y à H:i') . "
    ";
    
    $headers = "From: " . $email . "\r\n" .
               "Reply-To: " . $email . "\r\n" .
               "X-Mailer: PHP/" . phpversion();
    
    // Envoi de l'email
    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Candidature envoyée avec succès']);
    } else {
        throw new Exception('Erreur lors de l\'envoi de l\'email');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}