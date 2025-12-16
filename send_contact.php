<?php
// send_contact.php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
    
    // Ici vous pouvez ajouter le code pour envoyer l'email
    // Par exemple avec la fonction mail() ou une librairie comme PHPMailer
    
    // Pour l'instant, on va simplement rediriger avec un message de succès
    $_SESSION['contact_message'] = "Merci $name ! Votre message a été envoyé. Nous vous répondrons bientôt à $email.";
    
    header('Location: index.php#contact');
    exit;
}
?>