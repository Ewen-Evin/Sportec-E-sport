<?php
require_once '/home/ewenevh/config/config.php';

// Vérification manuelle de la connexion
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - SPORTEC Admin</title>
    <link rel="shortcut icon" href="./logos/icon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Gardez le même CSS que précédemment */
        :root {
            --noir: #000000;
            --orange: #FF7700;
            --blanc: #FFFFFF;
            --gris-fonce: #0A0A0A;
            --gris-texte: #CCCCCC;
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
        }
        
        .admin-header {
            background: rgba(0, 0, 0, 0.9);
            padding: 20px 40px;
            border-bottom: 2px solid var(--orange);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-logo {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--orange);
            letter-spacing: 2px;
        }
        
        .admin-logo span {
            color: var(--blanc);
            font-weight: 400;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logout-link, .back-link {
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .logout-link:hover, .back-link:hover {
            color: var(--blanc);
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--orange);
            margin-bottom: 40px;
            text-transform: uppercase;
            text-align: center;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .dashboard-card {
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .dashboard-card:hover {
            border-color: var(--orange);
            transform: translateY(-5px);
        }
        
        .card-icon {
            font-size: 3rem;
            color: var(--orange);
            margin-bottom: 15px;
        }
        
        .card-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--blanc);
        }
        
        .card-description {
            color: var(--gris-texte);
            margin-bottom: 20px;
        }
        
        .btn {
            display: inline-block;
            background: var(--orange);
            color: var(--noir);
            padding: 12px 25px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 5px;
        }
        
        .btn:hover {
            background: var(--blanc);
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-logo">SPORTEC <span>Admin</span></div>
        <div class="user-info">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au site principal
            </a>
            <span>Connecté en tant que: <strong><?= $_SESSION['admin_username'] ?></strong></span>
            <a href="logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </header>
    
    <div class="admin-container">
        <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Tableau de bord</h1>
        
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="card-title">Gestion des Pilotes</h3>
                <p class="card-description">Ajouter, modifier ou supprimer les membres de l'équipe</p>
                <a href="./admin_pilotes.php" class="btn">Gérer les pilotes</a>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="card-title">Gestion des Événements</h3>
                <p class="card-description">Créer et organiser les événements et compétitions</p>
                <a href="admin_events.php" class="btn">Gérer les événements</a>
            </div>
            
            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="card-title">Galerie Photos</h3>
                <p class="card-description">Gérer les photos de la galerie du site</p>
                <a href="admin_galerie.php" class="btn">Gérer la galerie</a>
            </div>

            <div class="dashboard-card">
                <div class="card-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="card-title">Gestion Recrutement</h3>
                <p class="card-description">Gérer les statuts de recrutement par pôle</p>
                <a href="admin_recrutement.php" class="btn">Gérer le recrutement</a>
            </div>
        </div>
        
    </div>
</body>
</html>