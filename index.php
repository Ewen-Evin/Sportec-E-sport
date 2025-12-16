<?php
// index.php
require_once '/config/config.php';

// Récupération des données depuis la base de données
try {
    // Pilotes actifs
    $stmt_pilotes = $pdo->query("SELECT * FROM sportec_pilotes WHERE actif = 1 ORDER BY ordre ASC");
    $pilotes = $stmt_pilotes->fetchAll();
    
    // Événements à venir
    $stmt_events = $pdo->query("SELECT * FROM sportec_events WHERE actif = 1 AND date_event >= CURDATE() ORDER BY date_event ASC");
    $events = $stmt_events->fetchAll();
    
    // Galerie photos
    $stmt_galerie = $pdo->query("SELECT * FROM sportec_galerie WHERE actif = 1 ORDER BY ordre ASC, created_at DESC");
    $galerie = $stmt_galerie->fetchAll();
    
} catch(PDOException $e) {
    // En cas d'erreur, initialiser des tableaux vides
    $pilotes = [];
    $events = [];
    $galerie = [];
    $actualites = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sportec eSports</title>
    <link rel="shortcut icon" href="./logos/icon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --noir: #000000;
            --orange: #FF7700;
            --blanc: #FFFFFF;
            --gris-fonce: #0A0A0A;
            --gris-texte: #CCCCCC;
            --vert: #00FF00;
            --rouge: #FF0000;
            --jaune: #FFFF00;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--noir);
            color: var(--blanc);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Header et Navigation */
        header {
            background: rgba(0, 0, 0, 0.9);
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 119, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .header-container {
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 900;
            color: var(--orange);
            text-decoration: none;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
        }
        
        .logo span {
            color: var(--blanc);
            font-weight: 400;
        }
        
        .nav-container {
            display: flex;
            align-items: center;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav li {
            margin-left: 20px;
        }
        
        nav a {
            color: var(--blanc);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s ease;
            position: relative;
            white-space: nowrap;
        }
        
        nav a:hover {
            color: var(--orange);
        }
        
        nav a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--orange);
            transition: width 0.3s ease;
        }
        
        nav a:hover:after {
            width: 100%;
        }
        
        nav a.active {
            color: var(--orange);
        }
        
        nav a.active:after {
            width: 100%;
        }
        
        .social-icons {
            display: flex;
            margin-left: 30px;
        }
        
        .social-icons a {
            color: var(--blanc);
            font-size: 1.2rem;
            margin-left: 15px;
            transition: color 0.3s ease;
        }
        
        .social-icons a:hover {
            color: var(--orange);
        }
        
        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 25px;
            cursor: pointer;
        }
        
        .menu-toggle span {
            width: 100%;
            height: 3px;
            background-color: var(--blanc);
            transition: all 0.3s ease;
        }
        
        /* Styles pour le menu déroulant */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: var(--noir);
            min-width: 200px;
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-top: none;
            display: none;
            flex-direction: column;
            z-index: 1000;
            padding: 10px 0;
        }

        .dropdown.active .dropdown-menu {
            display: flex;
        }

        .dropdown-menu li {
            margin: 0;
            width: 100%;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: var(--blanc);
            text-decoration: none;
            transition: all 0.3s ease;
            width: 100%;
            border: none;
            font-size: 0.9rem;
        }

        .dropdown-menu a:hover {
            background: rgba(255, 119, 0, 0.1);
            color: var(--orange);
        }

        .dropdown-menu a:after {
            display: none;
        }

        /* Animation pour le menu déroulant */
        .dropdown-menu {
            animation: fadeInDown 0.3s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Styles responsifs pour le menu déroulant */
        @media (max-width: 768px) {
            .dropdown-menu {
                position: static;
                display: none;
                background: var(--gris-fonce);
                border: 1px solid rgba(255, 119, 0, 0.3);
                margin-top: 10px;
                padding: 0;
                width: 100%;
                height: 20vh;

            }
            
            .dropdown.active .dropdown-menu {
                display: flex;
            }
            
            .dropdown-menu li {
                margin: 0;
            }
            
            .dropdown-menu a {
                padding: 15px 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                font-size: 1rem;
            }
            
            .dropdown-menu a:last-child {
                border-bottom: none;
            }
            
            /* Amélioration de l'indicateur du menu déroulant sur mobile */
            .dropdown-toggle {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            
            .dropdown-toggle i {
                transition: transform 0.3s ease;
            }
            
            .dropdown.active .dropdown-toggle i {
                transform: rotate(180deg);
            }
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9)), url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect width="100" height="100" fill="black"/><path d="M0 0L100 100" stroke="%23333" stroke-width="1"/><path d="M100 0L0 100" stroke="%23333" stroke-width="1"/></svg>');
            background-size: cover;
            position: relative;
            text-align: center;
            padding: 0 20px;
        }
        
        .hero-content {
            max-width: 900px;
            z-index: 2;
            animation: fadeInUp 1s ease-out;
        }
        
        .hero-subtitle {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            font-size: 1.2rem;
            letter-spacing: 3px;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 30px;
            text-transform: uppercase;
            color: var(--blanc);
            line-height: 1.2;
        }
        
        .hero-title span {
            color: var(--orange);
        }
        
        .hero-description {
            font-size: 1.2rem;
            color: var(--gris-texte);
            margin-bottom: 40px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn {
            display: inline-block;
            background: var(--orange);
            color: var(--noir);
            padding: 12px 30px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 2px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            background: var(--blanc);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 119, 0, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
            box-shadow: 0 5px 10px rgba(255, 119, 0, 0.3);
        }
        
        /* Renaissance Section */
        .renaissance {
            padding: 100px 40px;
            background-color: var(--gris-fonce);
            position: relative;
        }
        
        .renaissance-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        .renaissance-content h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--orange);
            margin-bottom: 30px;
            text-transform: uppercase;
            position: relative;
        }
        
        .renaissance-content h2:after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: var(--orange);
            margin: 15px 0;
        }
        
        .renaissance-content p {
            font-size: 1.1rem;
            color: var(--gris-texte);
            margin-bottom: 20px;
            line-height: 1.8;
        }
        
        .renaissance-image {
            position: relative;
            border: 2px solid var(--orange);
            padding: 10px;
            background: var(--noir);
            animation: pulse 2s infinite ease-in-out;
            overflow: hidden;
        }
        
        .renaissance-image:before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            border: 1px solid var(--orange);
            opacity: 0.5;
            animation: pulse 3s infinite ease-in-out;
        }
        
        .renaissance-image img {
            width: 100%;
            display: block;
            height: auto;
            transition: transform 0.5s ease;
        }
        
        .renaissance-image:hover img {
            transform: scale(1.05);
        }
        
        /* Valeurs Section */
        .valeurs {
            padding: 100px 40px;
            background-color: var(--noir);
        }
        
        .valeurs-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-title {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            color: var(--orange);
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
        }
        
        .section-title:after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--orange);
            margin: 15px auto;
        }
        
        .valeurs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        
        .valeur-card {
            background: var(--gris-fonce);
            border-left: 4px solid var(--orange);
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .valeur-card:hover {
            transform: translateY(-5px);
        }
        
        .valeur-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--orange);
        }
        
        .valeur-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--blanc);
        }
        
        .objectifs-text {
            background: rgba(255, 119, 0, 0.1);
            border-left: 4px solid var(--orange);
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
        }
        
        .objectifs-text p {
            margin-bottom: 15px;
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        .signature {
            text-align: right;
            font-style: italic;
            color: var(--orange);
            margin-top: 20px;
            font-family: 'Orbitron', sans-serif;
        }
        
        /* Partenaires Section */
        .partenaires {
            padding: 80px 40px;
            background-color: var(--gris-fonce);
        }
        
        .partenaires-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .partenaires-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            align-items: center;
        }
        
        .partenaire-logo {
            background: var(--noir);
            padding: 20px;
            border: 1px solid rgba(255, 119, 0, 0.3);
            text-align: center;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .partenaire-logo:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
        }
        
        .partenaire-logo span {
            color: var(--blanc);
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
        }

        .partenaire-logo img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        .partenaire-logo a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            text-decoration: none;
        }

        /* Histoire Section */
        .histoire {
            padding: 100px 40px;
            background-color: var(--noir);
        }
        
        .histoire-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .histoire-timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .histoire-timeline:before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--orange);
            transform: translateX(-50%);
        }
        
        .timeline-item {
            margin-bottom: 50px;
            position: relative;
            width: 50%;
            padding: 20px;
        }
        
        .timeline-item:nth-child(odd) {
            left: 0;
        }
        
        .timeline-item:nth-child(even) {
            left: 50%;
        }
        
        .timeline-item:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--orange);
            border-radius: 50%;
            top: 30px;
        }
        
        .timeline-item:nth-child(odd):after {
            right: -10px;
        }
        
        .timeline-item:nth-child(even):after {
            left: -10px;
        }
        
        .timeline-content {
            background: var(--gris-fonce);
            padding: 20px;
            border-left: 4px solid var(--orange);
        }
        
        .timeline-year {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        /* Équipe Section */
        .equipe {
            padding: 100px 40px;
            background-color: var(--gris-fonce);
        }
        
        .equipe-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .equipe-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .membre-card {
            background: var(--noir);
            border: 1px solid rgba(255, 119, 0, 0.3);
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .membre-card:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
        }
        
        .membre-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: var(--gris-fonce);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 3rem;
            border: 2px solid var(--orange);
            overflow: hidden;
        }
        
        .membre-nom {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.3rem;
            margin-bottom: 5px;
            color: var(--blanc);
        }
        
        .membre-role {
            color: var(--orange);
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* Section Recrutement */
        .recrutement {
            padding: 100px 40px;
            background-color: var(--noir);
        }

        .recrutement-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .recrutement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 50px;
        }

        .recrutement-card {
            background: var(--gris-fonce);
            border: 3px solid;
            border-radius: 10px;
            padding: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .recrutement-card.ouvert {
            border-color: var(--vert);
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.2);
        }

        .recrutement-card.ferme {
            border-color: var(--rouge);
            box-shadow: 0 0 20px rgba(255, 0, 0, 0.2);
        }

        .recrutement-card.limite {
            border-color: var(--jaune);
            box-shadow: 0 0 20px rgba(255, 255, 0, 0.2);
        }

        .recrutement-card:hover {
            transform: translateY(-10px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .pole-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--blanc);
        }

        .statut-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .statut-ouvert { 
            color: var(--vert); 
        }
        .statut-ferme { 
            color: var(--rouge); 
        }
        .statut-limite { 
            color: var(--jaune); 
        }

        .statut-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        .dot-ouvert { background: var(--vert); }
        .dot-ferme { background: var(--rouge); }
        .dot-limite { background: var(--jaune); }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .recrutement-content {
            margin-bottom: 25px;
        }

        .recrutement-content p {
            margin-bottom: 15px;
            color: var(--gris-texte);
            line-height: 1.6;
        }

        .conditions {
            background: rgba(255, 119, 0, 0.1);
            border-left: 3px solid var(--orange);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }

        .conditions h4 {
            color: var(--orange);
            margin-bottom: 10px;
            font-family: 'Orbitron', sans-serif;
        }

        .contact-info {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .contact-info a {
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .contact-info a:hover {
            color: var(--blanc);
        }

        /* Formulaire de recrutement */
        .recrutement-form-container {
            background: var(--gris-fonce);
            border: 2px solid var(--orange);
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .recrutement-form h3 {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--blanc);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            background: var(--noir);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--blanc);
            border-radius: 5px;
            font-family: 'Rajdhani', sans-serif;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--orange);
        }

        select.form-control {
            cursor: pointer;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
        }

        .checkbox-group input[type="checkbox"] {
            margin-top: 3px;
        }

        .checkbox-group label {
            font-size: 0.9rem;
            color: var(--gris-texte);
            line-height: 1.4;
        }

        .submit-btn {
            display: block;
            width: 100%;
            background: var(--orange);
            color: var(--noir);
            padding: 15px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .submit-btn:hover {
            background: var(--blanc);
            transform: translateY(-2px);
        }

        .submit-btn:disabled {
            background: var(--gris-texte);
            cursor: not-allowed;
            transform: none;
        }

        .success-message {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid var(--vert);
            color: var(--vert);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid var(--rouge);
            color: var(--rouge);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .recrutement-grid {
                grid-template-columns: 1fr;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .recrutement-form-container {
                padding: 20px;
                margin: 20px;
            }
        }
        
        /* Galerie Section */
        .galerie {
            padding: 100px 40px;
            background-color: var(--noir);
        }
        
        .galerie-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .galerie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .galerie-item {
            position: relative;
            overflow: hidden;
            aspect-ratio: 1;
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 119, 0, 0.3);
        }
        
        .galerie-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .galerie-item:hover img {
            transform: scale(1.1);
        }
        
        /* Events Section */
        .events {
            padding: 100px 40px;
            background-color: var(--gris-fonce);
        }
        
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
        }
                
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .event-card {
            background: var(--noir);
            border: 1px solid rgba(255, 119, 0, 0.3);
            padding: 0;
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .event-card:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
        }

        .event-image {
            position: relative;
            width: 100%;
            height: 250px; /* Augmenté la hauteur */
            overflow: hidden;
            margin-bottom: 15px;
            flex-shrink: 0;
            background: var(--gris-fonce); /* Fond pour les images avec transparence */
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Changé de 'cover' à 'contain' pour voir l'image entière */
            transition: transform 0.3s ease;
        }

        .event-card:hover .event-image img {
            transform: scale(1.05);
        }

        .event-content {
            padding: 0 20px 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .event-date {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            font-size: 1.2rem;
            margin-bottom: 10px;
            padding-top: 15px;
        }

        .event-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--blanc);
            line-height: 1.3;
        }

        .event-card p {
            color: var(--gris-texte);
            margin-bottom: 15px;
            flex-grow: 1;
        }

        /* Styles pour différents nombres d'événements */
        .events-grid.single-event {
            grid-template-columns: 1fr;
            max-width: 800px; /* Augmenté la largeur max */
            margin-left: auto;
            margin-right: auto;
        }

        .events-grid.single-event .event-image {
            height: 350px; /* Hauteur encore plus grande pour un seul événement */
        }

        .events-grid.two-events {
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); /* Plus large pour 2 événements */
        }

        .events-grid.two-events .event-image {
            height: 280px; /* Hauteur moyenne pour 2 événements */
        }

        .events-grid.three-events {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .events-grid.three-events .event-image {
            height: 220px; /* Hauteur standard pour 3+ événements */
        }

        /* Fallback pour les événements sans image */
        .event-card.no-image .event-content {
            padding-top: 20px;
        }

        /* Responsive pour les événements */
        @media (max-width: 768px) {
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .events-grid.single-event,
            .events-grid.two-events,
            .events-grid.three-events {
                grid-template-columns: 1fr;
            }
            
            .event-image {
                height: 200px; /* Hauteur réduite sur mobile */
            }
            
            .events-grid.single-event .event-image {
                height: 250px; /* Moins haut sur mobile pour un seul événement */
            }
            
            .events-grid.two-events .event-image {
                height: 200px; /* Hauteur standard sur mobile */
            }
            
            .event-content {
                padding: 0 15px 15px;
            }
            
            .event-title {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .event-image {
                height: 180px;
            }
            
            .events-grid.single-event .event-image {
                height: 220px;
            }
            
            .event-date {
                font-size: 1.1rem;
            }
            
            .event-title {
                font-size: 1.1rem;
            }
        }

        .tabs-container {
            max-width: 1200px;
            margin: 0 auto 40px;
            text-align: center;
        }
        
        .tabs {
            display: inline-flex;
            background: var(--gris-fonce);
            border-radius: 8px;
            padding: 5px;
            border: 1px solid rgba(255, 119, 0, 0.3);
        }
        
        .tab {
            padding: 12px 30px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 600;
            color: var(--gris-texte);
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .tab.active {
            background: var(--orange);
            color: var(--noir);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeInUp 0.5s ease-out;
        }

        /* Styles pour la grille Facebook */
        .facebook-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .facebook-post {
            background: var(--noir);
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .facebook-post:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 119, 0, 0.2);
        }

        .facebook-post-image {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
        }

        .facebook-post-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .facebook-post:hover .facebook-post-image img {
            transform: scale(1.05);
        }

        .facebook-post-content {
            padding: 15px;
        }

        .facebook-post-date {
            color: var(--orange);
            font-size: 0.9rem;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .facebook-post-text {
            font-family: 'Rajdhani', sans-serif;
            color: var(--blanc);
            margin-bottom: 10px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            display: -moz-box;
            -moz-box-orient: vertical;
            line-clamp: 3;
            box-orient: vertical;
        }

        .facebook-post-link {
            display: inline-block;
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 10px;
            transition: color 0.3s ease;
        }

        .facebook-post-link:hover {
            color: var(--blanc);
        }

        .facebook-post-link i {
            margin-right: 5px;
        }

        .facebook-loading {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .facebook-error {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
            color: var(--orange);
        }

        /* Responsive pour la grille Facebook */
        @media (max-width: 768px) {
            .facebook-grid {
                grid-template-columns: 1fr;
            }
            
            .facebook-post-content {
                padding: 12px;
            }
            
            .facebook-post-text {
                font-size: 0.9rem;
            }
        }
        
        /* TV Section */
        .tv {
            padding: 100px 40px;
            background-color: var(--noir);
        }
        
        .tv-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .tv-video {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            height: 0;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .tv-video iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Section des dernières vidéos */
        .dernieres-videos {
            margin-top: 60px;
        }

        .videos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .video-item {
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .video-item:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 119, 0, 0.2);
        }

        .video-thumbnail {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
        }

        .video-thumbnail img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .video-item:hover .video-thumbnail img {
            transform: scale(1.05);
        }

        .video-play-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 119, 0, 0.9);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .video-item:hover .video-play-icon {
            opacity: 1;
        }

        .video-play-icon i {
            color: var(--blanc);
            font-size: 1.5rem;
            margin-left: 3px;
        }

        .video-info {
            padding: 15px;
        }

        .video-title {
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            color: var(--blanc);
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            display: -moz-box;
            -moz-box-orient: vertical;
            line-clamp: 2;
            box-orient: vertical;
        }

        .video-date {
            color: var(--orange);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .video-loading {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
        }

        .video-error {
            text-align: center;
            padding: 40px;
            grid-column: 1 / -1;
            color: var(--orange);
        }

        .loading-spinner {
            border: 3px solid var(--gris-fonce);
            border-top: 3px solid var(--orange);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive pour la grille de vidéos */
        @media (max-width: 768px) {
            .videos-grid {
                grid-template-columns: 1fr;
            }
            
            .video-info {
                padding: 12px;
            }
            
            .video-title {
                font-size: 0.9rem;
            }
        }

        /* Contact Section */
        .contact {
            padding: 100px 40px;
            background-color: var(--gris-fonce);
        }
        
        .contact-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .contact-form {
            background: var(--noir);
            padding: 30px;
            border: 1px solid rgba(255, 119, 0, 0.3);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--blanc);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--blanc);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--orange);
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        /* Footer */
        footer {
            background: var(--gris-fonce);
            padding: 40px 20px;
            text-align: center;
            border-top: 1px solid rgba(255, 119, 0, 0.3);
        }
        
        .footer-social {
            margin-bottom: 20px;
        }
        
        .footer-social a {
            color: var(--blanc);
            font-size: 1.5rem;
            margin: 0 10px;
            transition: color 0.3s ease;
        }
        
        .footer-social a:hover {
            color: var(--orange);
        }
        
        .footer-text {
            color: var(--gris-texte);
            font-size: 0.9rem;
        }
        
        /* Pages cachées par défaut - CORRECTION */
        .page {
            display: none;
        }
        
        .page.active {
            display: block;
            padding-top: 80px;
            min-height: 100vh;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 119, 0, 0.2);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(255, 119, 0, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 119, 0, 0);
            }
        }
        
        /* Responsive */
        @media (max-width: 1100px) {
            nav a {
                font-size: 0.9rem;
            }
            
            nav li {
                margin-left: 15px;
            }
        }
        
        @media (max-width: 992px) {
            .renaissance-container {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .hero-title {
                font-size: 3rem;
            }
            
            .social-icons {
                margin-left: 20px;
            }
            
            .social-icons a {
                margin-left: 10px;
            }
            
            .histoire-timeline:before {
                left: 30px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 70px;
                padding-right: 0;
            }
            
            .timeline-item:nth-child(even) {
                left: 0;
            }
            
            .timeline-item:after {
                left: 20px !important;
            }
        }
        
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
            }
            
            .logo {
                font-size: 1.5rem;
            }
            
            .menu-toggle {
                display: flex;
                z-index: 1001;
            }
            
            .menu-toggle.active span:nth-child(1) {
                transform: rotate(45deg) translate(5px, 5px);
            }
            
            .menu-toggle.active span:nth-child(2) {
                opacity: 0;
            }
            
            .menu-toggle.active span:nth-child(3) {
                transform: rotate(-45deg) translate(7px, -6px);
            }
            
            nav ul {
                flex-direction: column;
                background: var(--noir);
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                padding: 80px 20px 20px;
                display: none;
                z-index: 999;
                justify-content: center;
                align-items: center;
            }
            
            nav ul.active {
                display: flex;
            }
            
            nav li {
                margin: 15px 0;
            }
            
            .nav-container {
                position: static;
            }
            
            .social-icons {
                display: none;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .renaissance, .valeurs, .histoire, .equipe, .galerie, .events, .tv, .contact {
                padding: 80px 20px;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .valeurs-grid, .equipe-grid {
                grid-template-columns: 1fr;
            }
            
            .footer-social a {
                margin: 0 8px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header avec navigation -->
    <header>
        <div class="header-container">
            <a href="#" class="logo nav-link" data-page="accueil">SPORTEC <span>eSports</span></a>
            
            <div class="nav-container">
                <nav>
                    <ul id="menu">
                        <li><a href="#" class="nav-link active" data-page="accueil">Accueil</a></li>
                        <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle">
                                L'Équipe <i class="fas fa-chevron-down" style="margin-left: 5px; font-size: 0.8rem;"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="nav-link" data-page="histoire">Notre Histoire</a></li>
                                <li><a href="#" class="nav-link" data-page="equipe">Membres</a></li>
                                <li><a href="#" class="nav-link" data-page="recrutement">Recrutement</a></li>
                            </ul>
                        </li>
                        <li><a href="#" class="nav-link" data-page="galerie">Photos</a></li>
                        <li><a href="#" class="nav-link" data-page="events">Events & Actualités</a></li>
                        <li><a href="#" class="nav-link" data-page="tv">SPORTEC Sim-Racing TV</a></li>
                        <li><a href="forum/index.php" class="nav-link" target="_blank">Forum</a></li>
                        <li><a href="#" class="nav-link" data-page="contact">Contact</a></li>
                        <!-- Lien vers l'administration -->
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                            <li><a href="admin.php" class="nav-link"><i class="fas fa-cog"></i></a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                
                <div class="social-icons">
                    <a href="https://www.instagram.com/sportec_esports/" aria-label="Instagram" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com/SportecEsport" aria-label="Facebook" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.youtube.com/@SPORTECSimRacingTV" aria-label="YouTube" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="https://discord.gg/VynDTveV3F" aria-label="Discord" target="_blank"><i class="fab fa-discord"></i></a>
                </div>
                
                <div class="menu-toggle" id="mobile-menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Accueil -->
    <section id="accueil" class="page active">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <div class="hero-subtitle">SINCE 2010</div>
                <h1 class="hero-title">SPORTEC <span>eSports</span></h1>
                <p class="hero-description">Une équipe historique de simracing qui renaît de ses cendres pour dominer les circuits virtuels.</p>
                <a href="#" class="btn nav-link" data-page="histoire">Découvrir notre histoire</a>
            </div>
        </section>

        <!-- Renaissance Section -->
        <section class="renaissance">
            <div class="renaissance-container">
                <div class="renaissance-content">
                    <h2>LA RENAISSANCE</h2>
                    <p>Équipe eSport créée en 2010 sur Forza Motorsport 3 (XBOX 360) et ayant participé au Championnat de France FM3 qui réunissait plus de 5000 joueurs durant 8 mois à une époque où l'eSport Sim-Racing n'en était qu'à ses premiers balbutiements.</p>
                    <p>10 années plus tard, nous voilà à nouveau dans le monde virtuel sur Gran Turismo 7 (PS5) et Assetto Corsa Competizione (PC).</p>
                    <a href="#" class="btn nav-link" data-page="contact">Rejoindre l'aventure</a>
                </div>
                <div class="renaissance-image">
                    <!-- Image de l'équipe -->
                    <img src="./sportec.png" alt="Équipe Sportec eSports">
                </div>
            </div>
        </section>

        <!-- Valeurs Section -->
        <section class="valeurs">
            <div class="valeurs-container">
                <h2 class="section-title">Valeurs & Objectifs</h2>
                
                <div class="valeurs-grid">
                    <div class="valeur-card">
                        <div class="valeur-icon">🎮</div>
                        <h3 class="valeur-title">PLAISIR</h3>
                        <p>Le jeu avant tout, toujours</p>
                    </div>
                    
                    <div class="valeur-card">
                        <div class="valeur-icon">😄</div>
                        <h3 class="valeur-title">FUN</h3>
                        <p>Ambiance détendue et positive</p>
                    </div>
                    
                    <div class="valeur-card">
                        <div class="valeur-icon">🤝</div>
                        <h3 class="valeur-title">FAIR-PLAY</h3>
                        <p>Compétition loyale et respectueuse</p>
                    </div>
                    
                    <div class="valeur-card">
                        <div class="valeur-icon">✨</div>
                        <h3 class="valeur-title">RESPECT</h3>
                        <p>Envers tous les membres et adversaires</p>
                    </div>
                </div>
                
                <div class="objectifs-text">
                    <p>Nous nous dépasserons toujours pour entretenir et transmettre ces principes fondateurs de l'équipe.</p>
                    <p>Nous contribuerons à notre modeste niveau au monde du jeu avec une communauté pleine de talents.</p>
                    <p>Les résultats seront secondaires car le jeu reste avant toutes choses une merveilleuse passion.</p>
                    
                    <div class="signature">
                        <p>Bon jeu !</p>
                        <p>Good Luck & Have Fun !</p>
                        <p>KaKaShI</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Partenaires Section -->
        <section class="partenaires">
            <div class="partenaires-container">
                <h2 class="section-title">Nos Partenaires</h2>
                
                <div class="partenaires-grid">
                    <div class="partenaire-logo">
                        <a href="https://ninjersey.com/" target="_blank">
                            <img src="./logos/ninjersey.png" alt="Ninjersey">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://www.nsh-racing.com/" target="_blank">
                            <img src="./logos/nsh-racing.png" alt="NSH-Racing">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://hp-performances.com/fr/" target="_blank">
                            <img src="./logos/hp-performances.png" alt="HP-Performances">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://netauto39.fr/" target="_blank">
                            <img src="./logos/net-auto-39.png" alt="Net-Auto-39">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://esportrecrutement.fr/" target="_blank">
                            <img src="./logos/esport-recrutement.png" alt="eSport-Recrutement">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://www.instant-gaming.com/fr/" target="_blank">
                            <img src="./logos/instant-gaming.png" alt="Instant-Gaming">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://www.facebook.com/MadDesign74" target="_blank">
                            <img src="./logos/mad-design.png" alt="Mad-Design">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://www.facebook.com/profile.php?id=100086676094310" target="_blank">
                            <img src="./logos/g-creation.png" alt="G-Creation">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <img src="./logos/KS_DESIGN.png" alt="KS-Design">
                    </div>
                    <div class="partenaire-logo">
                        <a href="http://esport.granturismo-fr.com/" target="_blank">
                            <img src="./logos/portail-gt-esport.png" alt="Portail-GT-eSport">
                        </a>
                    </div>
                    <div class="partenaire-logo">
                        <a href="https://www.youtube.com/channel/UClqAOWd1ZapLETfCL58ooew" target="_blank">
                            <img src="./logos/el-loco-tv.png" alt="El-Loco-TV">
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- Page Histoire -->
    <section id="histoire" class="page">
        <section class="histoire">
            <div class="histoire-container">
                <h2 class="section-title">Notre Histoire</h2>
                
                <div class="histoire-timeline">
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-year">2009</div>
                            <p>Création de l'équipe SporTec par AbysseRoseNoire sur Forza Motorsport 3 (XBOX 360).</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-year">2010</div>
                            <p>Participation au Championnat de France FM3 avec plus de 5000 joueurs. L'équipe atteint la 5ème place au classement général.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-year">2010-2020</div>
                            <p>L'équipe participe à diverses compétitions et termine souvent en tête des classements. Plusieurs membres sont recrutés par les meilleures équipes mondiales.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-year">2020</div>
                            <p>Renaissance de la SPORTEC sur Gran Turismo Sport (PS4) en vue de la sortie de Gran Turismo 7.</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <div class="timeline-year">2023</div>
                            <p>L'équipe évolue désormais sur Gran Turismo 7 (PS5) et Assetto Corsa Competizione (PC) avec les mêmes valeurs fondatrices.</p>
                        </div>
                    </div>
                </div>
                
                <div class="objectifs-text">
                    <p>La SporTec Team est une équipe ayant vu le jour sur Forza Motorsport 3 (XBOX 360) en fin d'année 2009 mais connaîtra vraiment ses premières heures officielles en début d'année 2010.</p>
                    <p>Le fondateur originel est AbysseRoseNoire qui a eu l'idée de créer une équipe pour préparer la sortie du nouvel opus de la franchise Forza Motorsport.</p>
                    <p>Malheureusement, quelques jours à peine après la sortie du jeu, le fondateur apprend qu'il présente de sérieux problèmes de santé et doit abandonner la gestion de l'équipe.</p>
                    <p>AbysseRoseNoire demande donc à Zaraki43, xX Gefou Xx et ObiKakaShI, joueurs présents depuis quasi les premières heures de l'équipe d'en devenir les Managers à sa place.</p>
                    
                    <div class="signature">
                        <p>SPORTEC Story - KaKaShI</p>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- Page Équipe -->
    <section id="equipe" class="page">
        <section class="equipe">
            <div class="equipe-container">
                <h2 class="section-title">Notre Équipe</h2>
                
                <div class="equipe-grid">
                    <?php if (count($pilotes) > 0): ?>
                        <?php foreach ($pilotes as $pilote): ?>
                            <div class="membre-card">
                                <div class="membre-photo">
                                    <?php if (!empty($pilote['photo'])): ?>
                                        <img src="<?= htmlspecialchars($pilote['photo']) ?>" alt="<?= htmlspecialchars($pilote['pseudo']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-user"></i>
                                    <?php endif; ?>
                                </div>
                                <h3 class="membre-nom"><?= htmlspecialchars($pilote['pseudo']) ?></h3>
                                <div class="membre-role"><?= htmlspecialchars($pilote['role']) ?></div>
                                <p><?= htmlspecialchars($pilote['plateforme']) ?></p>
                                <?php if (!empty($pilote['bio'])): ?>
                                    <p style="margin-top: 10px; font-size: 0.9rem; color: var(--gris-texte);"><?= htmlspecialchars($pilote['bio']) ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                            <p>Aucun pilote n'est actuellement disponible.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </section>

    <!-- Page Recrutement -->
    <section id="recrutement" class="page">
        <section class="recrutement">
            <div class="recrutement-container">
                <h2 class="section-title">Rejoindre SPORTEC eSports</h2>
                
                <div class="objectifs-text">
                    <p>Vous souhaitez intégrer notre équipe et participer à nos compétitions ? Consultez les statuts de recrutement pour chaque pôle et postulez !</p>
                    <p>Nous recherchons des pilotes motivés, fair-play et compétitifs pour renforcer nos rangs.</p>
                </div>
                
                <div class="recrutement-grid">
                    <?php 
                    // Récupérer les statuts de recrutement
                    try {
                        $stmt_recrutement = $pdo->query("SELECT * FROM sportec_recrutement WHERE actif = 1 ORDER BY pole");
                        $recrutements = $stmt_recrutement->fetchAll();
                    } catch(PDOException $e) {
                        $recrutements = [];
                    }
                    
                    foreach ($recrutements as $recrutement): 
                        $cardClass = strtolower($recrutement['statut']);
                        $statutText = [
                            'OUVERT' => 'Recrutement Ouvert',
                            'FERME' => 'Recrutement Fermé', 
                            'LIMITE' => 'Recrutement Limité'
                        ][$recrutement['statut']];
                        $poleText = [
                            'PC' => 'Pôle PC',
                            'PS' => 'Pôle PS', 
                            'LES_DEUX' => 'Pôle PC/PS'
                        ][$recrutement['pole']];
                    ?>
                        
                        <div class="recrutement-card <?= $cardClass ?>">
                            <div class="card-header">
                                <h3 class="pole-title"><?= $poleText ?></h3>
                                <div class="statut-indicator statut-<?= $cardClass ?>">
                                    <span class="statut-dot dot-<?= $cardClass ?>"></span>
                                    <?= $statutText ?>
                                </div>
                            </div>
                            
                            <div class="recrutement-content">
                                <p><?= nl2br(htmlspecialchars($recrutement['description'])) ?></p>
                                
                                <?php if (!empty($recrutement['conditions'])): ?>
                                    <div class="conditions">
                                        <h4>Conditions requises :</h4>
                                        <p><?= nl2br(htmlspecialchars($recrutement['conditions'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($recrutement['statut'] === 'OUVERT'): ?>
                                <div class="contact-info">
                                    <button type="button" class="btn postuler-btn" data-pole="<?= $recrutement['pole'] ?>">
                                        Postuler maintenant
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="contact-info">
                                    <p><strong>Contact :</strong> <a href="<?= htmlspecialchars($recrutement['contact']) ?>" target="_blank"><?= htmlspecialchars($recrutement['contact']) ?></a></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Formulaire de recrutement (caché par défaut) -->
                <div id="formulaire-recrutement" class="recrutement-form-container" style="display: none;">
                    <form id="recrutementForm" class="recrutement-form" method="POST" action="send_recrutement.php">
                        <h3>Formulaire de candidature - <span id="form-pole-name"></span></h3>
                        <input type="hidden" id="pole" name="pole">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pseudo" id="pseudo-label">Pseudo *</label>
                                <input type="text" id="pseudo" name="pseudo" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="age">Âge *</label>
                                <input type="number" id="age" name="age" class="form-control" min="16" max="99" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="pays">Pays *</label>
                                <input type="text" id="pays" name="pays" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" id="ville" name="ville" class="form-control">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="discord">Pseudo Discord *</label>
                                <input type="text" id="discord" name="discord" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="experience">Expérience en simracing *</label>
                            <select id="experience" name="experience" class="form-control" required>
                                <option value="">Sélectionnez votre niveau</option>
                                <option value="debutant">Débutant</option>
                                <option value="intermediaire">Intermédiaire</option>
                                <option value="confirme">Confirmé</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="jeux">Jeux pratiqués *</label>
                            <input type="text" id="jeux" name="jeux" class="form-control" placeholder="Ex: Gran Turismo 7, Assetto Corsa Competizione, iRacing..." required>
                        </div>
                        
                        <div class="form-group">
                            <label for="disponibilite">Disponibilités (soirées/week-end) *</label>
                            <input type="text" id="disponibilite" name="disponibilite" class="form-control" placeholder="Ex: Soirs en semaine, weekends..." required>
                        </div>
                        
                        <div class="form-group">
                            <label for="presentation">Présentation et motivation *</label>
                            <textarea id="presentation" name="presentation" class="form-control" placeholder="Parlez-nous de vous, votre expérience, vos motivations pour rejoindre SPORTEC..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="objectifs">Vos objectifs dans l'équipe *</label>
                            <textarea id="objectifs" name="objectifs" class="form-control" placeholder="Que souhaitez-vous apporter à l'équipe ? Quels sont vos objectifs sportifs ?" required></textarea>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="reglement" name="reglement" required>
                            <label for="reglement">Je certifie avoir pris connaissance du règlement interne de l'équipe et m'engage à le respecter *</label>
                        </div>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" id="donnees" name="donnees" required>
                            <label for="donnees">J'accepte que mes données personnelles soient utilisées dans le cadre de ma candidature *</label>
                        </div>
                        
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Envoyer ma candidature
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </section>

    <!-- Page Galerie -->
    <section id="galerie" class="page">
        <div class="galerie">
            <div class="galerie-container">
                <h2 class="section-title">Galerie Photos</h2>
                
                <div class="galerie-grid">
                    <?php if (count($galerie) > 0): ?>
                        <?php foreach ($galerie as $photo): ?>
                            <div class="galerie-item">
                                <img src="<?= htmlspecialchars($photo['image']) ?>" alt="<?= htmlspecialchars($photo['titre'] ?? 'Photo SPORTEC') ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                            <p>Aucune photo n'est actuellement disponible dans la galerie.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Page Events -->
    <section id="events" class="page">
        <section class="events">
            <div class="events-container">
                <h2 class="section-title">Événements & Actualités</h2>

                <div class="objectifs-text">
                    <p>Retrouvez ici tous les événements à venir et les actualités de l'équipe SPORTEC eSports.</p>
                    <p>Suivez-nous sur nos réseaux sociaux pour ne rien manquer de notre actualité !</p>
                </div>

                <div class="tabs-container">
                    <div class="tabs">
                        <div class="tab active" data-tab="events">Événements</div>
                        <div class="tab" data-tab="actualites">Actualités Facebook</div>
                    </div>
                </div>
                
                <div class="tab-content active" id="events-content">
                    <div class="events-grid" id="events-grid">
                        <?php if (count($events) > 0): ?>
                            <?php foreach ($events as $event): ?>
                                <div class="event-card <?= empty($event['image']) ? 'no-image' : '' ?>">
                                    <?php if (!empty($event['image'])): ?>
                                        <div class="event-image">
                                            <img src="<?= htmlspecialchars($event['image']) ?>" 
                                                alt="<?= htmlspecialchars($event['titre']) ?>"
                                                loading="lazy">
                                        </div>
                                    <?php endif; ?>
                                    <div class="event-content">
                                        <div class="event-date">
                                            <?php
                                            setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR', 'fr', 'French');
                                            echo strftime('%d %B %Y', strtotime($event['date_event']));
                                            ?>
                                        </div>
                                        <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                                        <?php if (!empty($event['description'])): ?>
                                            <p><?= htmlspecialchars($event['description']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($event['lien'])): ?>
                                            <a href="<?= htmlspecialchars($event['lien']) ?>" 
                                            class="btn" 
                                            target="_blank" 
                                            style="margin-top: auto; align-self: flex-start;">
                                                Voir plus
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                                <p>Aucun événement à venir pour le moment.</p>
                                <a href="#" class="btn nav-link" data-page="contact" style="margin-top: 15px;">
                                    Nous contacter
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="tab-content" id="actualites-content">
                    <div class="facebook-container">
                        <div class="facebook-header">
                            <h3>Dernières Actualités SPORTEC</h3>
                            <p>Suivez-nous sur Facebook pour ne rien manquer</p>
                        </div>
                        
                        <!-- Nouvelle grille Facebook -->
                        <div id="facebook-grid" class="facebook-grid">
                            <div class="facebook-loading">
                                <div class="loading-spinner"></div>
                                <p>Chargement des actualités Facebook...</p>
                            </div>
                        </div>
                        
                        <!-- Fallback si le chargement échoue -->
                        <div class="facebook-fallback" style="display: none;" id="facebook-fallback">
                            <p>Impossible de charger les actualités Facebook.</p>
                            <a href="https://www.facebook.com/SportecEsport" class="btn" target="_blank">
                                <i class="fab fa-facebook"></i> Voir sur Facebook
                            </a>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <a href="https://www.facebook.com/SportecEsport" class="btn" target="_blank">
                            <i class="fab fa-facebook"></i> Voir toutes nos actualités sur Facebook
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- Page TV -->
    <section id="tv" class="page">
        <section class="tv">
            <div class="tv-container">
                <h2 class="section-title">SPORTEC Sim-Racing TV</h2>
                
                <!-- Nouveaux boutons pour les réseaux sociaux -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <a href="https://www.youtube.com/@SPORTECSimRacingTV" target="_blank" class="btn" style="margin: 0;">
                        <i class="fab fa-youtube"></i> Chaîne YouTube
                    </a>
                    <a href="https://www.facebook.com/SportecSimRacingTV/" target="_blank" class="btn" style="margin: 0;">
                        <i class="fab fa-facebook"></i> Page Facebook
                    </a>
                </div>
                
                <div class="tv-video">
                    <iframe width="560" height="315" src="https://www.youtube.com/embed/Yd_eqHCaeNY?si=xi4l9SAtzY-_qxJp" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                
                <div class="objectifs-text">
                    <p>Découvrez notre chaîne YouTube dédiée au simracing avec des vidéos de nos courses, des tutoriels, des présentations de réglages et bien plus encore.</p>
                    <p>Abonnez-vous à notre chaîne <a href="https://www.youtube.com/@SPORTECSimRacingTV" target="_blank" style="color: var(--orange);">SPORTEC Sim-Racing TV</a> pour ne manquer aucune de nos vidéos !</p>
                </div>

                <!-- Section des dernières vidéos -->
                <div class="dernieres-videos">
                    <h3 class="section-title" style="font-size: 2rem; margin-top: 60px;">Nos Dernières Vidéos</h3>
                    
                    <div id="videos-grid" class="videos-grid">
                        <!-- Les vidéos seront chargées dynamiquement ici -->
                        <div class="video-loading">
                            <div class="loading-spinner"></div>
                            <p>Chargement des vidéos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <!-- Page Contact -->
    <section id="contact" class="page">
        <div class="contact">
            <div class="contact-container">
                <h2 class="section-title">Contactez-Nous</h2>
                
                <div class="contact-form">
                    <form id="contactForm" method="POST" action="send_contact.php">
                        <div class="form-group">
                            <label for="name">Nom</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Sujet</label>
                            <input type="text" id="subject" name="subject" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn">Envoyer le message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-social">
            <a href="https://www.instagram.com/sportec_esports/" aria-label="Instagram" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.facebook.com/SportecEsport" aria-label="Facebook" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://www.youtube.com/@SPORTECSimRacingTV" aria-label="YouTube" target="_blank"><i class="fab fa-youtube"></i></a>
            <a href="https://discord.gg/VynDTveV3F" aria-label="Discord" target="_blank"><i class="fab fa-discord"></i></a>
        </div>
        <div class="footer-text">
            <p>&copy; <?= date('Y') ?> SPORTEC eSports. Tous droits réservés.</p>
            <p>Design by SPORTEC Team</p>
        </div>
    </footer>

<script>
    // Gestion du menu déroulant desktop et mobile
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.dropdown');
        const mobileMenu = document.getElementById('mobile-menu');
        const menu = document.getElementById('menu');
        
        // Fonction pour fermer tous les menus déroulants
        function closeAllDropdowns() {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
        
        // Gestion des dropdowns
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fermer tous les autres dropdowns
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('active');
                    }
                });
                
                // Basculer l'état du dropdown actuel
                dropdown.classList.toggle('active');
            });
        });
        
        // Fermer les menus déroulants en cliquant ailleurs sur le document
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                closeAllDropdowns();
            }
        });
        
        // Fermer les menus déroulants quand on change de page
        const navLinks = document.querySelectorAll('.nav-link[data-page]');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                closeAllDropdowns();
            });
        });
        
        // Menu mobile
        mobileMenu.addEventListener('click', function() {
            this.classList.toggle('active');
            menu.classList.toggle('active');
            
            // Fermer les dropdowns quand on ouvre/ferme le menu mobile
            if (!menu.classList.contains('active')) {
                closeAllDropdowns();
            }
        });
        
        // Fermer le menu mobile quand on clique sur un lien
        menu.addEventListener('click', function(e) {
            if (e.target.classList.contains('nav-link') && window.innerWidth <= 768) {
                menu.classList.remove('active');
                mobileMenu.classList.remove('active');
                closeAllDropdowns();
            }
        });
        
        // Fermer le menu mobile en redimensionnant la fenêtre
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                menu.classList.remove('active');
                mobileMenu.classList.remove('active');
                closeAllDropdowns();
            }
        });

        // Navigation entre les pages
        const pages = document.querySelectorAll('.page');
        
        // Fonction pour optimiser l'affichage des événements
        function optimizeEventsDisplay() {
            const eventsGrid = document.getElementById('events-grid');
            if (!eventsGrid) return;
            
            const eventCards = eventsGrid.querySelectorAll('.event-card');
            const eventCount = eventCards.length;
            
            // Réinitialiser les classes
            eventsGrid.classList.remove('single-event', 'two-events', 'three-events');
            
            // Appliquer les classes selon le nombre d'événements
            if (eventCount === 1) {
                eventsGrid.classList.add('single-event');
            } else if (eventCount === 2) {
                eventsGrid.classList.add('two-events');
            } else if (eventCount >= 3) {
                eventsGrid.classList.add('three-events');
            }
            
            // Optimiser le chargement des images
            eventCards.forEach(card => {
                const image = card.querySelector('img');
                if (image) {
                    // S'assurer que l'image est bien chargée
                    image.addEventListener('load', function() {
                        this.style.opacity = '1';
                    });
                    
                    image.addEventListener('error', function() {
                        // Fallback si l'image ne charge pas
                        this.style.display = 'none';
                        card.classList.add('no-image');
                    });
                    
                    // Forcer le rechargement si nécessaire
                    if (image.complete && image.naturalWidth === 0) {
                        const src = image.src;
                        image.src = '';
                        setTimeout(() => {
                            image.src = src;
                        }, 100);
                    }
                }
            });
        }
        
        // Fonction pour changer de page
        function changePage(targetPage) {
            // Masquer toutes les pages
            pages.forEach(page => {
                page.classList.remove('active');
            });
            
            // Afficher la page cible
            document.getElementById(targetPage).classList.add('active');
            
            // Mettre à jour la navigation active
            navLinks.forEach(navLink => {
                navLink.classList.remove('active');
                if (navLink.getAttribute('data-page') === targetPage) {
                    navLink.classList.add('active');
                }
            });
            
            // Fermer le menu mobile si ouvert
            if (menu.classList.contains('active')) {
                menu.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
            
            // Fermer tous les dropdowns
            closeAllDropdowns();
            
            // Charger les vidéos si on arrive sur la page TV
            if (targetPage === 'tv') {
                loadLatestVideos();
            }
            
            // Optimiser l'affichage des événements si on arrive sur la page events
            if (targetPage === 'events') {
                setTimeout(() => {
                    optimizeEventsDisplay();
                }, 100);
            }
            
            // Recharger le widget Facebook si on arrive sur la page events et que l'onglet actualités est actif
            if (targetPage === 'events') {
                setTimeout(() => {
                    const actualitesTab = document.querySelector('.tab[data-tab="actualites"].active');
                    if (actualitesTab) {
                        initFacebookWidget();
                    }
                }, 100);
            }
            
            // Scroll vers le haut
            window.scrollTo(0, 0);
        }
        
        // Navigation au clic sur les liens de la navbar
        navLinks.forEach(link => {
            if (link.getAttribute('data-page')) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetPage = this.getAttribute('data-page');
                    changePage(targetPage);
                });
            }
        });
        
        // Navigation pour les boutons "Découvrir notre histoire" et "Rejoindre l'aventure"
        document.querySelectorAll('.btn.nav-link').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetPage = this.getAttribute('data-page');
                changePage(targetPage);
            });
        });
        
        // Gestion du formulaire de contact
        const contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                // La soumission est gérée par PHP
                // On laisse le formulaire se soumettre normalement
            });
        }
        
        // Initialiser les onglets Events/Actualités
        initTabs();
        
        // Initialiser le widget Facebook
        initFacebookWidget();
        
        // Optimiser l'affichage des événements
        optimizeEventsDisplay();
        
        // Charger les vidéos si on arrive directement sur la page TV
        if (document.getElementById('tv').classList.contains('active')) {
            loadLatestVideos();
        }

        // Gestion du formulaire de recrutement
        const postulerBtns = document.querySelectorAll('.postuler-btn');
        const formulaireContainer = document.getElementById('formulaire-recrutement');
        const formPoleName = document.getElementById('form-pole-name');
        const poleInput = document.getElementById('pole');
        const recrutementForm = document.getElementById('recrutementForm');
        
        // Fonction pour mettre à jour le label du pseudo
        function updatePseudoLabel(pole) {
            const pseudoLabel = document.getElementById('pseudo-label');
            if (pole === 'PS') {
                pseudoLabel.textContent = 'ID PSN *';
            } else {
                pseudoLabel.textContent = 'Pseudo *';
            }
        }

        // Gestion des boutons "Postuler maintenant"
        postulerBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const pole = this.getAttribute('data-pole');
                const poleNames = {
                    'PC': 'Pôle PC',
                    'PS': 'Pôle PS', 
                    'LES_DEUX': 'Pôle PC/PS'
                };
                
                // Mettre à jour le formulaire
                formPoleName.textContent = poleNames[pole];
                poleInput.value = pole;
                
                // → AJOUTE CETTE LIGNE ←
                updatePseudoLabel(pole);
                
                // Afficher le formulaire
                formulaireContainer.style.display = 'block';
                
                // Scroll vers le formulaire
                formulaireContainer.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });
        
        // Gestion de la soumission du formulaire
        if (recrutementForm) {
            recrutementForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('.submit-btn');
                const originalText = submitBtn.innerHTML;
                
                // Désactiver le bouton
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
                
                // Simuler l'envoi (à remplacer par votre logique d'envoi réelle)
                setTimeout(() => {
                    // Ici vous enverrez les données à votre script PHP
                    const formData = new FormData(this);
                    
                    fetch('send_recrutement.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Afficher message de succès
                            showFormMessage('Votre candidature a été envoyée avec succès ! Nous vous recontacterons rapidement.', 'success');
                            recrutementForm.reset();
                            formulaireContainer.style.display = 'none';
                        } else {
                            showFormMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showFormMessage('Une erreur est survenue. Veuillez réessayer.', 'error');
                    })
                    .finally(() => {
                        // Réactiver le bouton
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                    
                }, 1000);
            });
        }
        
        function showFormMessage(message, type) {
            // Supprimer les messages existants
            const existingMessages = document.querySelectorAll('.success-message, .error-message');
            existingMessages.forEach(msg => msg.remove());
            
            // Créer le nouveau message
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
            messageDiv.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check' : 'fa-exclamation-triangle'}"></i> ${message}`;
            
            // Insérer le message avant le formulaire
            recrutementForm.parentNode.insertBefore(messageDiv, recrutementForm);
            
            // Supprimer le message après 5 secondes
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    });

    // Initialisation des onglets
    function initTabs() {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab');
                
                // Mettre à jour les onglets actifs
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Afficher le contenu correspondant
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    if (content.id === `${targetTab}-content`) {
                        content.classList.add('active');
                    }
                });
                
                // Initialiser le widget Facebook si on active l'onglet actualités
                if (targetTab === 'actualites') {
                    setTimeout(() => {
                        initFacebookWidget();
                    }, 100);
                }
            });
        });
    }

    // Initialisation du widget Facebook
    function initFacebookWidget() {
        // Vérifier si Facebook SDK est déjà chargé
        if (window.FB) {
            FB.XFBML.parse(); // Re-parser les éléments Facebook
            return;
        }
        
        // Charger le SDK Facebook
        window.fbAsyncInit = function() {
            FB.init({
                xfbml: true,
                version: 'v18.0'
            });
            
            // Vérifier si le widget a chargé
            setTimeout(() => {
                const fbWidget = document.querySelector('.fb-page');
                if (fbWidget && fbWidget.offsetHeight === 0) {
                    const fallback = document.getElementById('facebook-fallback');
                    if (fallback) {
                        fallback.style.display = 'block';
                    }
                }
            }, 3000);
        };

        // Charger le SDK Facebook asynchrone
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "https://connect.facebook.net/fr_FR/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    }

    // Configuration YouTube
    const YOUTUBE_CONFIG = {
        API_KEY: 'AIzaSyD73jS0cZISyhvXvt5DT9CHjb8s6oTJG-0',
        CHANNEL_ID: 'SPORTECSimRacingTV',
        MAX_RESULTS: 12
    };

    // Fonction pour charger les dernières vidéos YouTube
    async function loadLatestVideos() {
        const videosGrid = document.getElementById('videos-grid');
        
        try {
            // Récupérer l'ID de la chaîne d'abord
            const channelResponse = await fetch(
                `https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername=${YOUTUBE_CONFIG.CHANNEL_ID}&key=${YOUTUBE_CONFIG.API_KEY}`
            );
            
            const channelData = await channelResponse.json();
            
            if (!channelData.items || channelData.items.length === 0) {
                // Essayer avec une autre méthode si la première échoue
                const searchResponse = await fetch(
                    `https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=${YOUTUBE_CONFIG.CHANNEL_ID}&key=${YOUTUBE_CONFIG.API_KEY}`
                );
                
                const searchData = await searchResponse.json();
                
                if (!searchData.items || searchData.items.length === 0) {
                    throw new Error('Chaîne non trouvée');
                }
                
                const channelId = searchData.items[0].id.channelId;
                
                // Récupérer les vidéos de la chaîne
                const videosResponse = await fetch(
                    `https://www.googleapis.com/youtube/v3/search?part=snippet&channelId=${channelId}&maxResults=${YOUTUBE_CONFIG.MAX_RESULTS}&order=date&type=video&key=${YOUTUBE_CONFIG.API_KEY}`
                );
                
                const videosData = await videosResponse.json();
                
                if (!videosData.items || videosData.items.length === 0) {
                    throw new Error('Aucune vidéo trouvée');
                }
                
                displayVideos(videosData.items);
                
            } else {
                const uploadsPlaylistId = channelData.items[0].contentDetails.relatedPlaylists.uploads;
                
                // Récupérer les vidéos de la playlist "uploads"
                const videosResponse = await fetch(
                    `https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId=${uploadsPlaylistId}&maxResults=${YOUTUBE_CONFIG.MAX_RESULTS}&key=${YOUTUBE_CONFIG.API_KEY}`
                );
                
                const videosData = await videosResponse.json();
                
                if (!videosData.items || videosData.items.length === 0) {
                    throw new Error('Aucune vidéo trouvée');
                }
                
                displayVideos(videosData.items);
            }
            
        } catch (error) {
            console.error('Erreur YouTube API:', error);
            displayError('Impossible de charger les vidéos. Veuillez réessayer plus tard.');
        }
    }

    // Fonction pour afficher les vidéos
    function displayVideos(videos) {
        const videosGrid = document.getElementById('videos-grid');
        
        let videosHTML = '';
        
        videos.forEach(video => {
            // Gérer les deux types de réponses (search vs playlistItems)
            const videoId = video.id?.videoId || video.snippet?.resourceId?.videoId;
            const title = video.snippet.title;
            const thumbnail = video.snippet.thumbnails.medium?.url || video.snippet.thumbnails.default.url;
            const publishedAt = new Date(video.snippet.publishedAt).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            if (videoId) {
                // Échappement correct des caractères spéciaux pour l'attribut onclick
                const escapedTitle = title.replace(/'/g, "\\'").replace(/"/g, '\\"');
                
                videosHTML += `
                    <div class="video-item" data-video-id="${videoId}" data-video-title="${escapedTitle}">
                        <div class="video-thumbnail">
                            <img src="${thumbnail}" alt="${title}" loading="lazy">
                            <div class="video-play-icon">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="video-info">
                            <h4 class="video-title">${title}</h4>
                            <div class="video-date">${publishedAt}</div>
                        </div>
                    </div>
                `;
            }
        });
        
        if (videosHTML === '') {
            displayError('Aucune vidéo disponible pour le moment.');
        } else {
            videosGrid.innerHTML = videosHTML;
            
            // Ajouter les écouteurs d'événements après l'insertion du HTML
            const videoItems = videosGrid.querySelectorAll('.video-item');
            videoItems.forEach(item => {
                item.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');
                    const title = this.getAttribute('data-video-title');
                    playVideo(videoId, title);
                });
            });
        }
    }

    // Fonction pour afficher une erreur
    function displayError(message) {
        const videosGrid = document.getElementById('videos-grid');
        videosGrid.innerHTML = `
            <div class="video-error">
                <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <p>${message}</p>
                <a href="https://www.youtube.com/@SPORTECSimRacingTV/videos" target="_blank" class="btn" style="margin-top: 15px;">
                    <i class="fab fa-youtube"></i> Voir sur YouTube
                </a>
            </div>
        `;
    }

    // Fonction pour jouer une vidéo
    function playVideo(videoId, title) {
        console.log('Playing video:', videoId);
        
        // Mettre à jour la vidéo principale dans la section TV active
        const activeTVSection = document.querySelector('#tv.page.active .tv-video iframe');
        if (activeTVSection) {
            activeTVSection.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
            console.log('Video source updated successfully');
        } else {
            // Fallback si la recherche précédente échoue
            const mainVideo = document.querySelector('.tv-video iframe');
            if (mainVideo) {
                mainVideo.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
            }
        }
        
        // Scroll vers la section TV
        const tvSection = document.getElementById('tv');
        if (tvSection) {
            tvSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    // Fonction de secours si l'API ne fonctionne pas
    function loadFallbackVideos() {
        const videosGrid = document.getElementById('videos-grid');
        videosGrid.innerHTML = `
            <div class="video-error">
                <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <p>Visitez directement notre chaîne YouTube pour voir toutes nos vidéos !</p>
                <a href="https://www.youtube.com/@SPORTECSimRacingTV/videos" target="_blank" class="btn" style="margin-top: 15px;">
                    <i class="fab fa-youtube"></i> Voir sur YouTube
                </a>
            </div>
        `;
    }

    // Vérifier le chargement Facebook au démarrage
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            const fbWidget = document.querySelector('.fb-page');
            if (fbWidget && fbWidget.offsetHeight === 0) {
                const fallback = document.getElementById('facebook-fallback');
                if (fallback) {
                    fallback.style.display = 'block';
                }
            }
        }, 5000);
    });
</script>
</body>
</html>