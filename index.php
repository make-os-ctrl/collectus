<?php
// Configuration de la base de données
$host = '192.168.1.11';
$dbname = 'collectus';        // Remplacez par le nom de votre base
$username = 'collectus_user';      // Remplacez par votre utilisateur MySQL
$password = 'SecurePass2025!';  // Remplacez par votre mot de passe

$message = '';
$messageType = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connexion PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Protection XSS avec htmlspecialchars
        $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
        $prenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8');
        $telephone = htmlspecialchars($_POST['telephone'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $adresse = htmlspecialchars($_POST['adresse'], ENT_QUOTES, 'UTF-8');
        $menu = htmlspecialchars($_POST['Menu'], ENT_QUOTES, 'UTF-8');
        $rgpd = isset($_POST['RGPD']) ? 1 : 0;
        
        // Validation email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Adresse email invalide.");
        }
        
        // Préparation de la requête SQL
        $sql = "INSERT INTO contacts (nom, prenom, telephone, email, adresse, type_encombrement, rgpd_accepte, date_creation) 
                VALUES (:nom, :prenom, :telephone, :email, :adresse, :type_encombrement, :rgpd_accepte, NOW())";
        
        $stmt = $pdo->prepare($sql);
        
        // Exécution avec les paramètres
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':telephone' => $telephone,
            ':email' => $email,
            ':adresse' => $adresse,
            ':type_encombrement' => $menu,
            ':rgpd_accepte' => $rgpd
        ]);
        
        $message = "Votre demande a été envoyée avec succès ! Nous vous contacterons bientôt.";
        $messageType = "success";
        
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données. Veuillez réessayer plus tard.";
        $messageType = "danger";
        error_log("Erreur PDO: " . $e->getMessage());
        
    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = "warning";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - CollectUS</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <a href="listev2.php" class="btn btn-primary">Afficher la liste des demandes</a>
</head>
<body>
    <div class="container">
        <h1>Formulaire de contact</h1>
        <h4>Veuillez entrer vos coordonnées</h4>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show mt-3" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-5">
        <div class="container">
            <form method="post" action="">
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" 
                           value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="tel" class="form-control" id="telephone" name="telephone" 
                           value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="adresse">Adresse :</label>
                    <input type="text" class="form-control" id="adresse" name="adresse" 
                           value="<?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?>" required>
                </div>
                
                <div class="mt-4 mb-4">
                    <div class="form-group">
                        <label for="Menu">Type d'encombrement :</label>
                        <select class="form-control" id="Menu" name="Menu" required>
                            <option value="">Choisir...</option>
                            <option value="Meubles" <?php echo (isset($_POST['Menu']) && $_POST['Menu'] === 'Meubles') ? 'selected' : ''; ?>>Meubles</option>
                            <option value="Electromenager" <?php echo (isset($_POST['Menu']) && $_POST['Menu'] === 'Electromenager') ? 'selected' : ''; ?>>Électroménager</option>
                            <option value="Dechets verts" <?php echo (isset($_POST['Menu']) && $_POST['Menu'] === 'Dechets verts') ? 'selected' : ''; ?>>Déchets verts</option>
                            <option value="Autres" <?php echo (isset($_POST['Menu']) && $_POST['Menu'] === 'Autres') ? 'selected' : ''; ?>>Autres</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-check mb-4">
                    <input type="checkbox" class="form-check-input" id="RGPD" name="RGPD" required>
                    <label class="form-check-label" for="RGPD">
                        J'ai lu et j'accepte le traitement de mes données. *
                    </label>
                </div>
                
                <div class="mt-5 mb-5 text-center">
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer class="bg-light mt-5 py-4">
        <div class="container text-center">
            <p>&copy; 2025 CollectUS. Tous droits réservés.</p>
            <div class="footer-socials">
                <a href="https://facebook.com" class="text-decoration-none">Facebook</a> |
                <a href="https://twitter.com" class="text-decoration-none">Twitter</a> |
                <a href="https://instagram.com" class="text-decoration-none">Instagram</a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS et dépendances -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>