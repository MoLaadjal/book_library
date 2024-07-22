<?php include '../header.php'; ?>

<?php
require '../config.php';

$book_id = intval($_GET['id']); // Assurez-vous que l'ID est un entier

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $publication_year = intval($_POST['publication_year']);
    $description = trim($_POST['description']);
    $author_ids = isset($_POST['author_ids']) ? array_map('intval', $_POST['author_ids']) : [];
    $category_ids = isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : [];
    $cover_image = trim($_POST['current_cover_image']); // Utiliser l'image actuelle par défaut

    // Upload de la nouvelle image de couverture si fournie
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

    try {
        // Débuter une transaction
        $pdo->beginTransaction();

        // Mise à jour du livre
        $sql = "UPDATE books SET title = :title, publication_year = :publication_year, description = :description, cover_image = :cover_image WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':publication_year' => $publication_year,
            ':description' => $description,
            ':cover_image' => $cover_image,
            ':id' => $book_id
        ]);

        // Suppression des anciens auteurs et insertion des nouveaux
        $sql = "DELETE FROM book_authors WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        $sql = "INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($author_ids as $author_id) {
            $stmt->execute([':book_id' => $book_id, ':author_id' => $author_id]);
        }

        // Suppression des anciennes catégories et insertion des nouvelles
        $sql = "DELETE FROM book_categories WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        $sql = "INSERT INTO book_categories (book_id, category_id) VALUES (:book_id, :category_id)";
        $stmt = $pdo->prepare($sql);
        foreach ($category_ids as $category_id) {
            $stmt->execute([':book_id' => $book_id, ':category_id' => $category_id]);
        }

        // Valider la transaction
        $pdo->commit();

        header("Location: list.php");
        exit();
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        die("Erreur lors de la mise à jour du livre : " . $e->getMessage());
    }
}

// Requête pour obtenir les détails du livre
$sql = "SELECT * FROM books WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête pour obtenir les auteurs du livre
$sql = "SELECT author_id FROM book_authors WHERE book_id = :book_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':book_id' => $book_id]);
$author_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Requête pour obtenir les catégories du livre
$sql = "SELECT category_id FROM book_categories WHERE book_id = :book_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':book_id' => $book_id]);
$category_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Requête pour obtenir tous les auteurs
$authors = $pdo->query("SELECT * FROM authors")->fetchAll(PDO::FETCH_ASSOC);

// Requête pour obtenir toutes les catégories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour éviter les erreurs de htmlspecialchars avec des valeurs nulles
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<h2>Modifier le livre</h2>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="current_cover_image" value="<?= safe_htmlspecialchars($book['cover_image']) ?>">
    
    <label for="title">Titre:</label><br>
    <input type="text" id="title" name="title" value="<?= safe_htmlspecialchars($book['title']) ?>" required><br>
    
    <label for="publication_year">Année de publication:</label><br>
    <input type="number" id="publication_year" name="publication_year" value="<?= safe_htmlspecialchars($book['publication_year']) ?>" required><br>
    
    <label for="description">Description:</label><br>
    <textarea id="description" name="description" required><?= safe_htmlspecialchars($book['description']) ?></textarea><br>
    
    <label for="cover_image">Image de couverture:</label><br>
    <?php if ($book['cover_image']): ?>
        <img src="../uploads/<?= safe_htmlspecialchars($book['cover_image']) ?>" alt="Couverture actuelle" width="150"><br>
    <?php endif; ?>
    <input type="file" id="cover_image" name="cover_image" accept="image/*"><br>
    
    <label for="authors">Auteurs:</label><br>
    <select id="authors" name="author_ids[]" multiple required>
        <?php foreach ($authors as $author): ?>
            <option value="<?= safe_htmlspecialchars($author['id']) ?>" <?= in_array($author['id'], $author_ids) ? 'selected' : '' ?>><?= safe_htmlspecialchars($author['name']) ?></option>
        <?php endforeach; ?>
    </select><br>
    
    <label for="categories">Catégories:</label><br>
    <select id="categories" name="category_ids[]" multiple required>
        <?php foreach ($categories as $category): ?>
            <option value="<?= safe_htmlspecialchars($category['id']) ?>" <?= in_array($category['id'], $category_ids) ? 'selected' : '' ?>><?= safe_htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
    </select><br>
    
    <input type="submit" value="Modifier">
</form>

<?php include '../footer.php'; ?>