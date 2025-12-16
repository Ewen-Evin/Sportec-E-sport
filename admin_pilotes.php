<?php
require_once '/config/config.php';
requireLogin();

$message = '';
$error = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $pseudo = clean($_POST['pseudo']);
                $role = clean($_POST['role']);
                $plateforme = clean($_POST['plateforme']);
                $bio = clean($_POST['bio'] ?? '');
                $ordre = intval($_POST['ordre'] ?? 0);
                
                // Upload photo avec validation
                $photo = '';
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    
                    if (!in_array($_FILES['photo']['type'], $allowed_types)) {
                        $error = "Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP";
                    } elseif ($_FILES['photo']['size'] > $max_size) {
                        $error = "Fichier trop volumineux (maximum 5MB)";
                    } else {
                        // Créer le dossier s'il n'existe pas
                        if (!is_dir(UPLOAD_DIR)) {
                            mkdir(UPLOAD_DIR, 0755, true);
                        }
                        
                        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                        // Générer un nom de fichier unique avec le pseudo
                        $safe_pseudo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $pseudo);
                        $filename = $safe_pseudo . '.' . $extension;
                        $photo = UPLOAD_DIR . $filename;
                        
                        // Si le fichier existe déjà, ajouter un suffixe numérique
                        $counter = 1;
                        $original_filename = $filename;
                        while (file_exists($photo)) {
                            $filename = $safe_pseudo . '_' . $counter . '.' . $extension;
                            $photo = UPLOAD_DIR . $filename;
                            $counter++;
                        }
                        
                        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo)) {
                            $error = "Erreur lors de l'upload de l'image";
                        }
                    }
                }
                
                if (empty($error)) {
                    $stmt = $pdo->prepare("INSERT INTO sportec_pilotes (pseudo, role, plateforme, photo, bio, ordre) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$pseudo, $role, $plateforme, $photo, $bio, $ordre])) {
                        // Redirection pour éviter la resoumission
                        header('Location: admin_pilotes.php?message=' . urlencode("Pilote ajouté avec succès !"));
                        exit;
                    } else {
                        $error = "Erreur lors de l'ajout du pilote.";
                    }
                }
                break;
                
            case 'modifier':
                $id = intval($_POST['id']);
                $pseudo = clean($_POST['pseudo']);
                $role = clean($_POST['role']);
                $plateforme = clean($_POST['plateforme']);
                $bio = clean($_POST['bio'] ?? '');
                $ordre = intval($_POST['ordre'] ?? 0);
                $actif = isset($_POST['actif']) ? 1 : 0;
                
                $photo = $_POST['photo_actuelle'];
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024;
                    
                    if (!in_array($_FILES['photo']['type'], $allowed_types)) {
                        $error = "Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP";
                    } elseif ($_FILES['photo']['size'] > $max_size) {
                        $error = "Fichier trop volumineux (maximum 5MB)";
                    } else {
                        // Créer le dossier s'il n'existe pas
                        if (!is_dir(UPLOAD_DIR)) {
                            mkdir(UPLOAD_DIR, 0755, true);
                        }
                        
                        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                        // Générer un nom de fichier avec le pseudo
                        $safe_pseudo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $pseudo);
                        $filename = $safe_pseudo . '.' . $extension;
                        $new_photo = UPLOAD_DIR . $filename;
                        
                        // Si le fichier existe déjà, ajouter un suffixe numérique
                        $counter = 1;
                        $original_filename = $filename;
                        while (file_exists($new_photo) && $new_photo !== $photo) {
                            $filename = $safe_pseudo . '_' . $counter . '.' . $extension;
                            $new_photo = UPLOAD_DIR . $filename;
                            $counter++;
                        }
                        
                        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $new_photo)) {
                            $error = "Erreur lors de l'upload de l'image";
                        } else {
                            // Supprimer l'ancienne photo si elle existe et est différente de la nouvelle
                            if ($photo && $photo !== $new_photo && file_exists($photo)) {
                                unlink($photo);
                            }
                            $photo = $new_photo;
                        }
                    }
                }
                
                if (empty($error)) {
                    $stmt = $pdo->prepare("UPDATE sportec_pilotes SET pseudo=?, role=?, plateforme=?, photo=?, bio=?, ordre=?, actif=? WHERE id=?");
                    if ($stmt->execute([$pseudo, $role, $plateforme, $photo, $bio, $ordre, $actif, $id])) {
                        // Redirection pour éviter la resoumission
                        header('Location: admin_pilotes.php?message=' . urlencode("Pilote modifié avec succès !"));
                        exit;
                    } else {
                        $error = "Erreur lors de la modification.";
                    }
                }
                break;
                
            case 'supprimer':
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("DELETE FROM sportec_pilotes WHERE id=?");
                if ($stmt->execute([$id])) {
                    // Redirection pour éviter la resoumission
                    header('Location: admin_pilotes.php?message=' . urlencode("Pilote supprimé avec succès !"));
                    exit;
                } else {
                    $error = "Erreur lors de la suppression.";
                }
                break;
        }
    }
}

// Récupération des messages depuis l'URL
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Récupération des pilotes
$stmt = $pdo->query("SELECT * FROM sportec_pilotes ORDER BY ordre ASC");
$pilotes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Pilotes - SPORTEC Admin</title>
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
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
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
        
        .btn-secondary {
            background: transparent;
            color: var(--orange);
            border: 2px solid var(--orange);
        }
        
        .btn-secondary:hover {
            background: var(--orange);
            color: var(--noir);
        }
        
        .btn-danger {
            background: #dc3545;
            color: var(--blanc);
        }
        
        .btn-danger:hover {
            background: #c82333;
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
        
        .pilote-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--orange);
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: rgba(0, 255, 0, 0.2);
            color: #00ff00;
        }
        
        .badge-danger {
            background: rgba(255, 0, 0, 0.2);
            color: #ff6666;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
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
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
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
        
        .current-photo {
            text-align: center;
            margin: 15px 0;
        }
        
        .current-photo img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--orange);
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
        <div class="admin-logo">SPORTEC <span>Pilotes</span></div>
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </header>
    
    <div class="admin-container">
        <h1 class="page-title"><i class="fas fa-users"></i> Gestion des Pilotes</h1>
        
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
            <h2><i class="fas fa-plus-circle"></i> Ajouter un nouveau pilote</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-grid">                    
                    <div class="form-group">
                        <label for="pseudo">Pseudo/Gamertag *</label>
                        <input type="text" id="pseudo" name="pseudo" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Rôle *</label>
                        <input type="text" id="role" name="role" class="form-control" placeholder="Ex: Pilote, Team Manager" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="plateforme">Plateforme *</label>
                        <select id="plateforme" name="plateforme" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="PC">PC</option>
                            <option value="PS">PS</option>
                            <option value="PS/PC">PS/PC</option>
                            <option value="Xbox">Xbox</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ordre">Ordre d'affichage</label>
                        <input type="number" id="ordre" name="ordre" class="form-control" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="photo">Photo de profil</label>
                        <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
                        <small style="color: var(--gris-texte);">Formats: JPEG, PNG, GIF, WebP - Max: 5MB</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="bio">Biographie</label>
                    <textarea id="bio" name="bio" class="form-control" placeholder="Description du pilote..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ajouter le pilote
                </button>
            </form>
        </div>
        
        <!-- Liste des pilotes -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Pseudo</th>
                        <th>Rôle</th>
                        <th>Plateforme</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pilotes as $pilote): ?>
                        <tr>
                            <td>
                                <?php if ($pilote['photo']): ?>
                                    <img src="<?= htmlspecialchars($pilote['photo']) ?>" alt="<?= htmlspecialchars($pilote['pseudo']) ?>" class="pilote-photo">
                                <?php else: ?>
                                    <div class="pilote-photo" style="display: flex; align-items: center; justify-content: center; background: var(--gris-fonce);">
                                        <i class="fas fa-user" style="color: var(--orange);"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($pilote['pseudo']) ?></td>
                            <td><?= htmlspecialchars($pilote['role']) ?></td>
                            <td><?= htmlspecialchars($pilote['plateforme']) ?></td>
                            <td><?= $pilote['ordre'] ?></td>
                            <td>
                                <span class="badge <?= $pilote['actif'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $pilote['actif'] ? 'Actif' : 'Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="editPilote(<?= $pilote['id'] ?>)" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button onclick="deletePilote(<?= $pilote['id'] ?>, '<?= addslashes($pilote['pseudo']) ?>')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Modal d'édition -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-edit"></i> Modifier le pilote</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="photo_actuelle" id="edit_photo_actuelle">
                
                <div class="current-photo" id="currentPhotoContainer">
                    <img id="currentPhoto" src="" alt="Photo actuelle">
                    <p style="color: var(--gris-texte); margin-top: 5px;">Photo actuelle</p>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_pseudo">Pseudo/Gamertag *</label>
                        <input type="text" id="edit_pseudo" name="pseudo" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_role">Rôle *</label>
                        <input type="text" id="edit_role" name="role" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_plateforme">Plateforme *</label>
                        <select id="edit_plateforme" name="plateforme" class="form-control" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="PC">PC</option>
                            <option value="PS">PS</option>
                            <option value="PS/PC">PS/PC</option>
                            <option value="Xbox">Xbox</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_ordre">Ordre d'affichage</label>
                        <input type="number" id="edit_ordre" name="ordre" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_photo">Nouvelle photo</label>
                        <input type="file" id="edit_photo" name="photo" class="form-control" accept="image/*">
                        <small style="color: var(--gris-texte);">Laisser vide pour garder la photo actuelle</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_bio">Biographie</label>
                    <textarea id="edit_bio" name="bio" class="form-control"></textarea>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="edit_actif" name="actif" value="1">
                    <label for="edit_actif">Pilote actif</label>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Supprimer le pilote</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 20px 0;">
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dc3545; margin-bottom: 15px;"></i>
                <h3 style="color: var(--blanc); margin-bottom: 10px;">Êtes-vous sûr de vouloir supprimer ce pilote ?</h3>
                <p style="color: var(--gris-texte); margin-bottom: 20px;">
                    Cette action est irréversible. Le pilote sera définitivement supprimé de la base de données.
                </p>
                <p id="deletePiloteInfo" style="color: var(--orange); font-weight: 600; font-size: 1.1rem; margin-bottom: 25px;"></p>
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
        async function editPilote(id) {
            try {
                const response = await fetch(`get_pilote.php?id=${id}`);
                
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données');
                }
                
                const pilote = await response.json();
                
                // Remplir le formulaire avec les données
                document.getElementById('edit_id').value = pilote.id;
                document.getElementById('edit_pseudo').value = pilote.pseudo;
                document.getElementById('edit_role').value = pilote.role;
                document.getElementById('edit_plateforme').value = pilote.plateforme;
                document.getElementById('edit_bio').value = pilote.bio || '';
                document.getElementById('edit_ordre').value = pilote.ordre;
                document.getElementById('edit_actif').checked = pilote.actif == 1;
                document.getElementById('edit_photo_actuelle').value = pilote.photo;
                
                // Afficher la photo actuelle
                const currentPhoto = document.getElementById('currentPhoto');
                const currentPhotoContainer = document.getElementById('currentPhotoContainer');
                
                if (pilote.photo) {
                    currentPhoto.src = pilote.photo;
                    currentPhotoContainer.style.display = 'block';
                } else {
                    currentPhotoContainer.style.display = 'none';
                }
                
                // Afficher le modal
                document.getElementById('editModal').style.display = 'flex';
                
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des données du pilote');
            }
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            // Réinitialiser le formulaire
            document.getElementById('editForm').reset();
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Fermer le modal avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Empêcher la fermeture en cliquant dans le modal
        document.querySelector('.modal-content').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        function deletePilote(id, pseudo) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deletePiloteInfo').textContent = pseudo;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fermer le modal de suppression en cliquant à l'extérieur
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Empêcher la fermeture en cliquant dans le modal de suppression
        document.querySelector('#deleteModal .modal-content').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>
</body>
</html>