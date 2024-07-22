<?php
include '../header.php';
require '../config.php';

// Fonction pour échapper les caractères spéciaux
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Fonction pour valider les entrées utilisateur
function validate_input($data) {
    return trim(stripslashes(htmlspecialchars($data)));
}

$author_id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = validate_input($_POST['name']);
    $bio = validate_input($_POST['bio']);
    $current_photo = validate_input($_POST['current_photo']);
    $photo = $current_photo;

    // Gérer le téléchargement de la photo
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/authors/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérifiez si le fichier est une image réelle ou une fausse image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            // Limiter les types de fichiers autorisés
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($imageFileType, $allowed_types)) {
                // Vérifiez la taille du fichier (limite à 2MB)
                if ($_FILES["photo"]["size"] <= 2000000) {
                    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                        $photo = basename($_FILES["photo"]["name"]);
                    } else {
                        echo "Désolé, il y a eu une erreur lors du téléchargement de votre fichier.";
                    }
                } else {
                    echo "Le fichier est trop volumineux. La taille maximale autorisée est de 2MB.";
                }
            } else {
                echo "Seuls les fichiers JPG, JPEG, PNG, GIF et WEBP sont autorisés.";
            }
        } else {
            echo "Le fichier n'est pas une image.";
        }
    }

    // Mise à jour de l'auteur
    if (!empty($name) && !empty($bio)) {
        $sql = "UPDATE authors SET name = ?, bio = ?, photo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $bio, $photo, $author_id]);

        header("Location: list.php");
        exit();
    } else {
        echo "Veuillez remplir tous les champs obligatoires.";
    }
} else {
    // Récupérer les détails de l'auteur
    $sql = "SELECT * FROM authors WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$author_id]);
    $author = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$author) {
        echo "Auteur non trouvé.";
        exit();
    }
}
?>

<h2>Modifier l'auteur</h2>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="current_photo" value="<?= safe_htmlspecialchars($author['photo']) ?>">
    <label for="name">Nom:</label><br>
    <input type="text" id="name" name="name" value="<?= safe_htmlspecialchars($author['name']) ?>" required><br>
    
    <label for="bio">Biographie:</label><br>
    <textarea id="bio" name="bio" required><?= safe_htmlspecialchars($author['bio']) ?></textarea><br>
    
    <label for="photo">Photo:</label><br>
    <?php if ($author['photo']): ?>
        <img src="../uploads/authors/<?= safe_htmlspecialchars($author['photo']) ?>" alt="Photo de <?= safe_htmlspecialchars($author['name']) ?>" class="img-fluid mb-2" style="max-width: 150px;"><br>
    <?php endif; ?>
    <input type="file" id="photo" name="photo" accept="image/*"><br><br>
    
    <input type="submit" value="Mettre à jour">
</form>

<?php include '../footer.php'; ?>