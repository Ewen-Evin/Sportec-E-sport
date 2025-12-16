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
                $titre = clean($_POST['titre']);
                $description = clean($_POST['description'] ?? '');
                $ordre = intval($_POST['ordre'] ?? 0);
                
                // Upload image avec validation
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    
                    if (!in_array($_FILES['image']['type'], $allowed_types)) {
                        $error = "Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP";
                    } elseif ($_FILES['image']['size'] > $max_size) {
                        $error = "Fichier trop volumineux (maximum 5MB)";
                    } else {
                        // Créer le dossier s'il n'existe pas
                        if (!is_dir('photos/galerie/')) {
                            mkdir('photos/galerie/', 0755, true);
                        }
                        
                        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid() . '.' . $extension;
                        $image = 'photos/galerie/' . $filename;
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                            $error = "Erreur lors de l'upload de l'image";
                        }
                    }
                } else {
                    $error = "Veuillez sélectionner une image";
                }
                
                if (empty($error)) {
                    $stmt = $pdo->prepare("INSERT INTO sportec_galerie (titre, image, description, ordre) VALUES (?, ?, ?, ?)");
                    if ($stmt->execute([$titre, $image, $description, $ordre])) {
                        $message = "Photo ajoutée à la galerie avec succès !";
                    } else {
                        $error = "Erreur lors de l'ajout de la photo.";
                    }
                }
                break;
                
            case 'modifier':
                $id = intval($_POST['id']);
                $titre = clean($_POST['titre']);
                $description = clean($_POST['description'] ?? '');
                $ordre = intval($_POST['ordre'] ?? 0);
                $actif = isset($_POST['actif']) ? 1 : 0;
                
                $image = $_POST['image_actuelle'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $max_size = 5 * 1024 * 1024;
                    
                    if (!in_array($_FILES['image']['type'], $allowed_types)) {
                        $error = "Type de fichier non autorisé. Formats acceptés: JPEG, PNG, GIF, WebP";
                    } elseif ($_FILES['image']['size'] > $max_size) {
                        $error = "Fichier trop volumineux (maximum 5MB)";
                    } else {
                        // Créer le dossier s'il n'existe pas
                        if (!is_dir('photos/galerie/')) {
                            mkdir('photos/galerie/', 0755, true);
                        }
                        
                        $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid() . '.' . $extension;
                        $image = 'photos/galerie/' . $filename;
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
                            $error = "Erreur lors de l'upload de l'image";
                        }
                    }
                }
                
                if (empty($error)) {
                    $stmt = $pdo->prepare("UPDATE sportec_galerie SET titre=?, image=?, description=?, ordre=?, actif=? WHERE id=?");
                    if ($stmt->execute([$titre, $image, $description, $ordre, $actif, $id])) {
                        $message = "Photo modifiée avec succès !";
                    } else {
                        $error = "Erreur lors de la modification.";
                    }
                }
                break;
                
            case 'supprimer':
                $id = intval($_POST['id']);
                $stmt = $pdo->prepare("DELETE FROM sportec_galerie WHERE id=?");
                if ($stmt->execute([$id])) {
                    $message = "Photo supprimée avec succès !";
                } else {
                    $error = "Erreur lors de la suppression.";
                }
                break;
        }
    }
}

// Récupération des photos
$stmt = $pdo->query("SELECT * FROM sportec_galerie ORDER BY ordre ASC, created_at DESC");
$photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Galerie - SPORTEC Admin</title>
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
        
        .photo-preview {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
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
        
        .current-image {
            text-align: center;
            margin: 15px 0;
        }
        
        .current-image img {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 5px;
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
        <div class="admin-logo">SPORTEC <span>Galerie</span></div>
        <a href="admin.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour au tableau de bord
        </a>
    </header>
    
    <div class="admin-container">
        <h1 class="page-title"><i class="fas fa-images"></i> Gestion de la Galerie</h1>
        
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
            <h2><i class="fas fa-plus-circle"></i> Ajouter une nouvelle photo</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="ajouter">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ordre">Ordre d'affichage</label>
                        <input type="number" id="ordre" name="ordre" class="form-control" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image *</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                        <small style="color: var(--gris-texte);">Formats: JPEG, PNG, GIF, WebP - Max: 5MB</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" placeholder="Description de la photo..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Ajouter la photo
                </button>
            </form>
        </div>
        
        <!-- Liste des photos -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($photos as $photo): ?>
                        <tr>
                            <td>
                                <?php if ($photo['image']): ?>
                                    <img src="<?= htmlspecialchars($photo['image']) ?>" alt="<?= htmlspecialchars($photo['titre']) ?>" class="photo-preview">
                                <?php else: ?>
                                    <div class="photo-preview" style="display: flex; align-items: center; justify-content: center; background: var(--gris-fonce);">
                                        <i class="fas fa-image" style="color: var(--orange);"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($photo['titre']) ?></td>
                            <td><?= htmlspecialchars(substr($photo['description'] ?? '', 0, 50)) ?><?= strlen($photo['description'] ?? '') > 50 ? '...' : '' ?></td>
                            <td><?= $photo['ordre'] ?></td>
                            <td>
                                <span class="badge <?= $photo['actif'] ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $photo['actif'] ? 'Visible' : 'Cachée' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($photo['created_at'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="editPhoto(<?= $photo['id'] ?>)" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Modifier
                                    </button>
                                    <button onclick="deletePhoto(<?= $photo['id'] ?>, '<?= addslashes($photo['titre']) ?>')" class="btn btn-danger btn-sm">
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
    
    <!-- Modal de modification -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-edit"></i> Modifier la photo</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="image_actuelle" id="edit_image_actuelle">
                
                <div class="current-image" id="currentImageContainer">
                    <img id="currentImage" src="" alt="Image actuelle">
                    <p style="color: var(--gris-texte); margin-top: 5px;">Image actuelle</p>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_titre">Titre *</label>
                        <input type="text" id="edit_titre" name="titre" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_ordre">Ordre d'affichage</label>
                        <input type="number" id="edit_ordre" name="ordre" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_image">Nouvelle image</label>
                        <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                        <small style="color: var(--gris-texte);">Laisser vide pour garder l'image actuelle</small>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" class="form-control"></textarea>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="edit_actif" name="actif" value="1">
                    <label for="edit_actif">Photo visible</label>
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
                <h2 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Supprimer la photo</h2>
                <button class="close-btn" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div style="text-align: center; padding: 20px 0;">
                <i class="fas fa-exclamation-circle" style="font-size: 3rem; color: #dc3545; margin-bottom: 15px;"></i>
                <h3 style="color: var(--blanc); margin-bottom: 10px;">Êtes-vous sûr de vouloir supprimer cette photo ?</h3>
                <p style="color: var(--gris-texte); margin-bottom: 20px;">
                    Cette action est irréversible. La photo sera définitivement supprimée de la galerie.
                </p>
                <p id="deletePhotoInfo" style="color: var(--orange); font-weight: 600; font-size: 1.1rem; margin-bottom: 25px;"></p>
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
        async function editPhoto(id) {
            try {
                const response = await fetch(`get_galerie.php?id=${id}`);
                
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des données');
                }
                
                const photo = await response.json();
                
                // Remplir le formulaire avec les données
                document.getElementById('edit_id').value = photo.id;
                document.getElementById('edit_titre').value = photo.titre;
                document.getElementById('edit_description').value = photo.description || '';
                document.getElementById('edit_ordre').value = photo.ordre;
                document.getElementById('edit_actif').checked = photo.actif == 1;
                document.getElementById('edit_image_actuelle').value = photo.image;
                
                // Afficher l'image actuelle
                const currentImage = document.getElementById('currentImage');
                const currentImageContainer = document.getElementById('currentImageContainer');
                
                if (photo.image) {
                    currentImage.src = photo.image;
                    currentImageContainer.style.display = 'block';
                } else {
                    currentImageContainer.style.display = 'none';
                }
                
                // Afficher le modal
                document.getElementById('editModal').style.display = 'flex';
                
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement des données de la photo');
            }
        }

        function deletePhoto(id, titre) {
            // Remplir les informations de la photo à supprimer
            document.getElementById('delete_id').value = id;
            document.getElementById('deletePhotoInfo').textContent = `"${titre}"`;
            
            // Afficher le modal de suppression
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fermer les modaux en cliquant à l'extérieur
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Empêcher la fermeture en cliquant dans les modaux
        document.querySelectorAll('.modal-content').forEach(modal => {
            modal.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Fermer les modaux avec la touche Echap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>