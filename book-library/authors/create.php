<?php
include '../header.php';
require '../config.php';

// Fonction pour échapper les caractères spéciaux
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Valider et filtrer les entrées utilisateur
    $name = safe_htmlspecialchars(trim($_POST['name']));
    $bio = safe_htmlspecialchars(trim($_POST['bio']));
    $photo = '';

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

    // Insertion de l'auteur
    if (!empty($name) && !empty($bio)) {
        $sql = "INSERT INTO authors (name, bio, photo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $bio, $photo]);

        header("Location: list.php");
        exit();
    } else {
        echo "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<h2>Ajouter un nouvel auteur</h2>
<form method="post" enctype="multipart/form-data">
    <label for="name">Nom:</label><br>
    <input type="text" id="name" name="name" required><br>
    
    <label for="bio">Biographie:</label><br>
    <textarea id="bio" name="bio" required></textarea><br>
    
    <label for="photo">Photo:</label><br>
    <input type="file" id="photo" name="photo" accept="image/*"><br><br>
    
    <input type="submit" value="Ajouter">
</form>

<?php include '../footer.php'; ?>