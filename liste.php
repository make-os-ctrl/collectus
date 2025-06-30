<?php
// Configuration de la base de données
$host = '192.168.1.11';
$dbname = 'collectus';        // Remplacez par le nom de votre base
$username = 'collectus_user';      // Remplacez par votre utilisateur MySQL
$password = 'SecurePass2025!';  // Remplacez par votre mot de passe

// Initialisation des variables
$demandes = [];
$error_message = null;

try {
    // Connexion PDO à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Récupération de toutes les demandes
    $sql = "SELECT id, nom, prenom, telephone, email, adresse, type_encombrant, consentement_rgpd, date_soumission 
            FROM demandes 
            ORDER BY date_soumission DESC";
    $stmt = $pdo->query($sql);
    $demandes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    $demandes = []; // S'assurer que $demandes est un tableau vide en cas d'erreur
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des demandes d'encombrants</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <a href="index.php"class="btn btn-primary">Retour à l'index</a>
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col">
                <h1 class="text-primary mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Liste des demandes d'encombrants
                </h1>
                <p class="text-muted">Gestion des demandes enregistrées</p>
            </div>
        </div>

        <?php if ($error_message): ?>
            <!-- Affichage erreur -->
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Erreur de connexion :</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <?php if (!$error_message): ?>
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card text-center border-primary">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Total</h5>
                            <h2 class="text-primary"><?php echo count($demandes); ?></h2>
                            <small class="text-muted">demandes</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-success">
                        <div class="card-body">
                            <h5 class="card-title text-success">Avec consentement</h5>
                            <h2 class="text-success">
                                <?php echo count(array_filter($demandes, function($d) { return $d['consentement_rgpd'] == 1; })); ?>
                            </h2>
                            <small class="text-muted">RGPD accepté</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center border-info">
                        <div class="card-body">
                            <h5 class="card-title text-info">Aujourd'hui</h5>
                            <h2 class="text-info">
                                <?php echo count(array_filter($demandes, function($d) { return date('Y-m-d', strtotime($d['date_soumission'])) == date('Y-m-d'); })); ?>
                            </h2>
                            <small class="text-muted">nouvelles</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

            <!-- Tableau des demandes -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>
                        Demandes enregistrées
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($demandes)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <h5 class="mt-3 text-muted">Aucune demande trouvée</h5>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nom</th>
                                        <th scope="col">Prénom</th>
                                        <th scope="col">Téléphone</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Adresse</th>
                                        <th scope="col">Type d'encombrant</th>
                                        <th scope="col">Consentement RGPD</th>
                                        <th scope="col">Date de soumission</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($demandes as $demande): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo htmlspecialchars($demande['id']); ?></td>
                                            <td><?php echo htmlspecialchars($demande['nom']); ?></td>
                                            <td><?php echo htmlspecialchars($demande['prenom']); ?></td>
                                            <td><?php echo htmlspecialchars($demande['telephone']); ?></td>
                                            <td><?php echo htmlspecialchars($demande['email']); ?></td>
                                            <td><?php echo htmlspecialchars($demande['adresse']); ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo htmlspecialchars($demande['type_encombrant']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($demande['consentement_rgpd'] == 1): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Oui
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Non
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $date = new DateTime($demande['date_soumission']);
                                                echo $date->format('d/m/Y H:i'); 
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <!-- Footer -->
        <div class="text-center mt-4">
            <small class="text-muted">
                Dernière mise à jour : <?php echo date('d/m/Y H:i:s'); ?>
            </small>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>