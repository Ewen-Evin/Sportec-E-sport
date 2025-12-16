<?php
require_once '/config/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Récupérer les statuts de recrutement
$recrutements = $pdo->query("SELECT * FROM sportec_recrutement ORDER BY pole")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['statut'] as $pole => $statut) {
        $description = $_POST['description'][$pole] ?? '';
        $conditions = $_POST['conditions'][$pole] ?? '';
        $contact = $_POST['contact'][$pole] ?? '';
        
        $stmt = $pdo->prepare("UPDATE sportec_recrutement SET statut = ?, description = ?, conditions = ?, contact = ? WHERE pole = ?");
        $stmt->execute([$statut, $description, $conditions, $contact, $pole]);
    }
    
    $_SESSION['success_message'] = "Statuts de recrutement mis à jour avec succès!";
    header('Location: admin_recrutement.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Recrutement - SPORTEC Admin</title>
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
        
        .recrutement-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .recrutement-card {
            background: var(--gris-fonce);
            border: 2px solid;
            border-radius: 8px;
            padding: 25px;
            transition: all 0.3s ease;
        }
        
        .recrutement-card.ouvert {
            border-color: var(--vert);
        }
        
        .recrutement-card.ferme {
            border-color: var(--rouge);
        }
        
        .recrutement-card.limite {
            border-color: var(--jaune);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .pole-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
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
        
        .statut-ouvert { color: var(--vert); }
        .statut-ferme { color: var(--rouge); }
        .statut-limite { color: var(--jaune); }
        
        .statut-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .dot-ouvert { background: var(--vert); }
        .dot-ferme { background: var(--rouge); }
        .dot-limite { background: var(--jaune); }
        
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
            padding: 10px;
            background: var(--noir);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--blanc);
            border-radius: 4px;
            font-family: 'Rajdhani', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--orange);
        }
        
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        
        select.form-control {
            cursor: pointer;
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
        
        .btn-block {
            display: block;
            width: 100%;
            text-align: center;
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
        
        @media (max-width: 768px) {
            .recrutement-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-logo">SPORTEC <span>Admin</span></div>
        <div class="user-info">
            <a href="admin.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
            <span>Connecté en tant que: <strong><?= $_SESSION['admin_username'] ?></strong></span>
            <a href="logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </header>
    
    <div class="admin-container">
        <h1 class="page-title"><i class="fas fa-user-plus"></i> Gestion du Recrutement</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?= $_SESSION['success_message'] ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <form method="POST">
            <div class="recrutement-grid">
                <?php foreach ($recrutements as $recrutement): ?>
                    <?php
                    $cardClass = strtolower($recrutement['statut']);
                    $statutText = [
                        'OUVERT' => 'Ouvert',
                        'FERME' => 'Fermé', 
                        'LIMITE' => 'Limité'
                    ][$recrutement['statut']];
                    $poleText = [
                        'PC' => 'Pôle PC',
                        'PS' => 'Pôle PlayStation',
                        'LES_DEUX' => 'Multi-Plateformes'
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
                        
                        <div class="form-group">
                            <label>Statut du recrutement</label>
                            <select name="statut[<?= $recrutement['pole'] ?>]" class="form-control" onchange="updateCardClass(this)">
                                <option value="OUVERT" <?= $recrutement['statut'] === 'OUVERT' ? 'selected' : '' ?>>🟢 Ouvert</option>
                                <option value="FERME" <?= $recrutement['statut'] === 'FERME' ? 'selected' : '' ?>>🔴 Fermé</option>
                                <option value="LIMITE" <?= $recrutement['statut'] === 'LIMITE' ? 'selected' : '' ?>>🟡 Limité</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description[<?= $recrutement['pole'] ?>]" class="form-control" placeholder="Description du recrutement..."><?= htmlspecialchars($recrutement['description']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Conditions</label>
                            <textarea name="conditions[<?= $recrutement['pole'] ?>]" class="form-control" placeholder="Conditions requises..."><?= htmlspecialchars($recrutement['conditions']) ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Contact</label>
                            <input type="text" name="contact[<?= $recrutement['pole'] ?>]" class="form-control" value="<?= htmlspecialchars($recrutement['contact']) ?>" placeholder="Lien Discord, email...">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn btn-block">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>

    <script>
        function updateCardClass(select) {
            const card = select.closest('.recrutement-card');
            const value = select.value.toLowerCase();
            
            // Retirer toutes les classes de statut
            card.classList.remove('ouvert', 'ferme', 'limite');
            // Ajouter la nouvelle classe
            card.classList.add(value);
            
            // Mettre à jour l'indicateur de statut
            const indicator = card.querySelector('.statut-indicator');
            const dot = card.querySelector('.statut-dot');
            const statutText = {
                'ouvert': 'Ouvert',
                'ferme': 'Fermé',
                'limite': 'Limité'
            }[value];
            
            // Retirer toutes les classes de couleur
            indicator.classList.remove('statut-ouvert', 'statut-ferme', 'statut-limite');
            dot.classList.remove('dot-ouvert', 'dot-ferme', 'dot-limite');
            
            // Ajouter les nouvelles classes
            indicator.classList.add('statut-' + value);
            dot.classList.add('dot-' + value);
            indicator.querySelector('span:last-child').textContent = statutText;
        }
    </script>
</body>
</html>