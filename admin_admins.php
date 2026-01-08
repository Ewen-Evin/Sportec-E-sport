<?php
require_once __DIR__ . '/config/config.php';
requireLogin();

$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $username = clean($_POST['username']);
                $email = clean($_POST['email']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                
                // Validation
                if (empty($username) || empty($email) || empty($password)) {
                    $error = "Tous les champs sont obligatoires";
                } elseif ($password !== $confirm_password) {
                    $error = "Les mots de passe ne correspondent pas";
                } elseif (strlen($password) < 8) {
                    $error = "Le mot de passe doit contenir au moins 8 caractères";
                } else {
                    // Vérifier si l'utilisateur existe déjà
                    $stmt = $pdo->prepare("SELECT id FROM sportec_admins WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $error = "Un administrateur avec ce nom d'utilisateur ou cet email existe déjà";
                    } else {
                        // Hacher le mot de passe
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        
                        // Insérer le nouvel administrateur
                        $stmt = $pdo->prepare("INSERT INTO sportec_admins (username, email, password) VALUES (?, ?, ?)");
                        if ($stmt->execute([$username, $email, $password_hash])) {
                            $message = "Administrateur ajouté avec succès !";
                        } else {
                            $error = "Erreur lors de l'ajout de l'administrateur";
                        }
                    }
                }
                break;
                
            case 'supprimer':
                $id = intval($_POST['id']);
                
                // Empêcher la suppression de l'utilisateur ID 1 (admin principal)
                if ($id == 1) {
                    $error = "Vous ne pouvez pas supprimer l'administrateur principal";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM sportec_admins WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $message = "Administrateur supprimé avec succès !";
                    } else {
                        $error = "Erreur lors de la suppression";
                    }
                }
                break;
        }
    }
}

// Récupération des administrateurs
$stmt = $pdo->query("SELECT * FROM sportec_admins ORDER BY created_at DESC");
$admins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Administrateurs - SPORTEC Admin</title>
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
        
        .back-link {
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .back-link:hover {
            color: var(--blanc);
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            color: var(--orange);
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: rgba(0, 255, 0, 0.1);
            border: 1px solid #00ff00;
            color: #00ff00;
        }
        
        .alert-error {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid #ff0000;
            color: #ff6666;
        }
        
        .form-section {
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .form-section h2 {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
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
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 5px;
            color: var(--blanc);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--orange);
        }
        
        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--orange);
            color: var(--noir);
        }
        
        .btn-primary:hover {
            background: var(--blanc);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #dc3545;
            color: var(--blanc);
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-secondary {
            background: transparent;
            color: var(--orange);
            border: 2px solid var(--orange);
        }
        
        .btn-secondary:hover {
            background: var(--orange);
            color: var(--noir);
        }
        
        .table-container {
            background: var(--gris-fonce);
            border: 1px solid rgba(255, 119, 0, 0.3);
            border-radius: 8px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: rgba(255, 119, 0, 0.1);
            padding: 15px;
            text-align: left;
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 119, 0, 0.1);
        }
        
        tr:hover {
            background: rgba(255, 119, 0, 0.05);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-primary {
            background: rgba(255, 119, 0, 0.2);
            color: var(--orange);
        }
        
        .current-user {
            background: rgba(0, 255, 0, 0.1);
        }
        
        .current-user:hover {
            background: rgba(0, 255, 0, 0.15);
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: var(--gris-fonce);
            padding: 30px;
            border-radius: 8px;
            border: 2px solid var(--orange);
            max-width: 500px;
            width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-family: 'Orbitron', sans-serif;
            color: var(--orange);
            font-size: 1.5rem;
        }
        
        .close-btn {
            background: none;
            border: none;
            color: var(--orange);
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        .close-btn:hover {
            color: var(--blanc);
        }
        
        .password-info {
            background: rgba(255, 119, 0, 0.1);
            border-left: 3px solid var(--orange);
            padding: 10px 15px;
            margin: 15px 0;
            font-size: 0.9rem;
            color: var(--gris-texte);
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            table {
                font-size: 0.9rem;
            }
            
            th, td {
                padding: 10px;
            }
            
            .modal-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-logo">SPORTEC <span>Administrateurs</span></div>
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </header>
    
    <div class="admin-container">
        <h1 class="page-title"><i class="fas fa-user-shield"></i> Gestion des Administrateurs</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <!-- Formulaire d'ajout -->
        <div class="form-section">
            <h2><i class="fas fa-user-plus"></i> Ajouter un nouvel administrateur</h2>
            <form method="POST" id="addAdminForm">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Nom d'utilisateur *</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                
                <div class="password-info">
                    <i class="fas fa-info-circle"></i> Le mot de passe doit contenir au moins 8 caractères. Il sera automatiquement haché avant d'être stocké.
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ajouter l'administrateur
                </button>
            </form>
        </div>
        
        <!-- Liste des administrateurs -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr class="<?= $admin['id'] == $_SESSION['admin_id'] ? 'current-user' : '' ?>">
                            <td><?= $admin['id'] ?></td>
                            <td>
                                <?= htmlspecialchars($admin['username']) ?>
                                <?php if ($admin['id'] == $_SESSION['admin_id']): ?>
                                    <span class="badge badge-primary">(Vous)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($admin['id'] != 1): ?>
                                        <button onclick="deleteAdmin(<?= $admin['id'] ?>, '<?= addslashes($admin['username']) ?>')" 
                                                class="btn btn-danger btn-sm"
                                                <?= $admin['id'] == $_SESSION['admin_id'] ? 'disabled title="Vous ne pouvez pas vous supprimer"' : '' ?>>
                                            <i class="fas fa-trash"></i> Supprimer
                                        </button>
                                    <?php else: ?>
                                        <span style="color: var(--gris-texte); font-size: 0.9rem;">
                                            <i class="fas fa-shield-alt"></i> Admin principal
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Supprimer l'administrateur</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 20px 0;">
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dc3545; margin-bottom: 15px;"></i>
                <h3 style="color: var(--blanc); margin-bottom: 10px;">Êtes-vous sûr de vouloir supprimer cet administrateur ?</h3>
                <p style="color: var(--gris-texte); margin-bottom: 20px;">
                    Cette action est irréversible. L'administrateur perdra immédiatement l'accès au panel d'administration.
                </p>
                <p id="deleteAdminInfo" style="color: var(--orange); font-weight: 600; font-size: 1.1rem; margin-bottom: 25px;"></p>
            </div>
            <div style="display: flex; gap: 15px; justify-content: center;">
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="supprimer">
                    <input type="hidden" name="id" id="delete_id">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Oui, supprimer
                    </button>
                </form>
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Validation du formulaire côté client
        document.getElementById('addAdminForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
                return false;
            }
            
            return true;
        });

        function deleteAdmin(id, username) {
            // Empêcher la suppression si l'utilisateur essaie de se supprimer lui-même
            const currentUserId = <?= $_SESSION['admin_id'] ?? 0 ?>;
            if (id == currentUserId) {
                alert("Vous ne pouvez pas vous supprimer vous-même !");
                return;
            }
            
            // Remplir les informations de l'admin à supprimer
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteAdminInfo').textContent = username;
            
            // Afficher le modal de suppression
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Fermer le modal avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>