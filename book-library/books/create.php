<?php 
header('Content-Type: text/html; charset=utf-8');
include '../header.php'; 
?>

<?php
require '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $publication_year = intval($_POST['publication_year']);
    $description = trim($_POST['description']);
    $author_ids = isset($_POST['author_ids']) ? array_map('intval', $_POST['author_ids']) : [];
    $category_ids = isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : [];
    $cover_image = null;

    // Upload de l'image de couverture si fournie
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $file_type = mime_content_type($_FILES['cover_image']['tmp_name']);

        if (in_array($file_type, $allowed_types)) {
            $upload_dir = '../uploads/';
            $cover_image = basename($_FILES['cover_image']['name']);
            $upload_file = $upload_dir . $cover_image;
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_file)) {
                die("Échec du téléchargement de l'image.");
            }
        } else {
            die("Le fichier téléchargé n'est pas une image valide.");
        }
    }

    // Ajout d'un nouvel auteur si fourni
    if (!empty($_POST['new_author'])) {
        $new_author = trim($_POST['new_author']);
        $sql = "INSERT INTO authors (name) VALUES (:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':name' => $new_author]);
        $new_author_id = $pdo->lastInsertId();
        $author_ids[] = $new_author_id;
    }

    // Insertion du livre
    $sql = "INSERT INTO books (title, publication_year, description, cover_image) VALUES (:title, :publication_year, :description, :cover_image)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':title' => $title,
        ':publication_year' => $publication_year,
        ':description' => $description,
        ':cover_image' => $cover_image
    ]);
    $book_id = $pdo->lastInsertId();

    // Insertion des auteurs
    $sql = "INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)";
    $stmt = $pdo->prepare($sql);
    foreach ($author_ids as $author_id) {
        $stmt->execute([
            ':book_id' => $book_id,
            ':author_id' => $author_id
        ]);
    }

    // Insertion des catégories
    $sql = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
    $stmt = $pdo->prepare($sql);
    foreach ($category_ids as $category_id) {
        $stmt->execute([
            ':book_id' => $book_id,
            ':category_id' => $category_id
        ]);
    }

    header("Location: list.php");
    exit();
}

// Requête pour obtenir les auteurs
$authors = $pdo->query("SELECT * FROM authors")->fetchAll(PDO::FETCH_ASSOC);

// Requête pour obtenir les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour éviter les erreurs de htmlspecialchars avec des valeurs nulles
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<h2>Ajouter un nouveau livre</h2>
<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Titre:</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="form-group">
        <label for="publication_year">Année de publication:</label>
        <input type="number" class="form-control" id="publication_year" name="publication_year" required>
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" id="description" name="description" required></textarea>
    </div>
    <div class="form-group">
        <label for="cover_image">Image de couverture:</label>
        <input type="file" class="form-control-file" id="cover_image" name="cover_image" accept="image/*">
    </div>
    <div class="form-group">
        <label for="authors">Auteurs existants:</label>
        <select class="form-control" id="authors" name="author_ids[]" multiple>
            <?php foreach ($authors as $author): ?>
                <option value="<?= $author['id'] ?>"><?= safe_htmlspecialchars($author['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="new_author">Ajouter un nouvel auteur:</label>
        <input type="text" class="form-control" id="new_author" name="new_author">
    </div>
    <div class="form-group">
        <label for="categories">Catégories existantes:</label>
        <select class="form-control" id="categories" name="category_ids[]" multiple required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= safe_htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="new_category">Ajouter une nouvelle catégorie:</label>
        <input type="text" class="form-control" id="new_category" name="new_category">
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>

<?php include '../footer.php'; ?>