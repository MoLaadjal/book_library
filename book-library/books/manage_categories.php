<?php include '../header.php'; ?>

<?php
require '../config.php';

$book_id = intval($_GET['id']); // Assurez-vous que l'ID est un entier

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_ids = isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : [];

    try {
        // Débuter une transaction
        $pdo->beginTransaction();

        // Supprimer les anciennes catégories
        $sql = "DELETE FROM book_categories WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        // Ajouter les nouvelles catégories
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
        die("Erreur lors de la mise à jour des catégories : " . $e->getMessage());
    }
}

// Requête pour obtenir les détails du livre
$sql = "SELECT * FROM books WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête pour obtenir les catégories sélectionnées
$selected_categories_stmt = $pdo->prepare("SELECT category_id FROM book_categories WHERE book_id = :book_id");
$selected_categories_stmt->execute([':book_id' => $book_id]);
$selected_categories = $selected_categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Requête pour obtenir toutes les catégories
$categories_stmt = $pdo->query("SELECT * FROM categories");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gérer les catégories pour le livre: <?= htmlspecialchars($book['title'], ENT_QUOTES, 'UTF-8') ?></h2>
<form method="post">
    <label for="categories">Catégories:</label><br>
    <select id="categories" name="category_ids[]" multiple required>
        <?php foreach ($categories as $category): ?>
            <option value="<?= htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8') ?>" <?= in_array($category['id'], $selected_categories) ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
        <?php endforeach; ?>
    </select><br>
    <input type="submit" value="Mettre à jour">
</form>

<a href="../books/list.php">Retour à la liste des livres</a>

<?php include '../footer.php'; ?>