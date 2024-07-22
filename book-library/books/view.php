<?php
include '../header.php';
require '../config.php';

$book_id = intval($_GET['id']); // Assurez-vous que l'ID est un entier

// Requête pour obtenir les détails du livre
$sql = "SELECT b.title, b.publication_year, b.description, b.cover_image, 
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors, 
               GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories,
               GROUP_CONCAT(DISTINCT a.id SEPARATOR ',') AS author_ids,
               GROUP_CONCAT(DISTINCT c.id SEPARATOR ',') AS category_ids
        FROM books b
        LEFT JOIN book_authors ba ON b.id = ba.book_id
        LEFT JOIN authors a ON ba.author_id = a.id
        LEFT JOIN book_categories bc ON b.id = bc.book_id
        LEFT JOIN categories c ON bc.category_id = c.id
        WHERE b.id = ?
        GROUP BY b.id";
$stmt = $pdo->prepare($sql);
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

// Fonction pour éviter les erreurs de htmlspecialchars avec des valeurs nulles
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

?>

<div class="container">
    <h2><?= safe_htmlspecialchars($book['title']) ?></h2>
    <?php if ($book['cover_image']): ?>
        <img src="../uploads/<?= safe_htmlspecialchars($book['cover_image']) ?>" alt="Couverture de <?= safe_htmlspecialchars($book['title']) ?>" class="img-fluid mb-3" style="max-width: 300px;">
    <?php endif; ?>
    <p><strong>Année de publication:</strong> <?= safe_htmlspecialchars($book['publication_year']) ?></p>
    <p><strong>Auteur(s):</strong> 
        <?php
        $authorNames = explode(', ', $book['authors']);
        $authorIds = explode(',', $book['author_ids']);
        foreach ($authorNames as $index => $name) {
            $id = intval($authorIds[$index]);
            echo '<a href="../authors/view.php?id=' . $id . '">' . safe_htmlspecialchars($name) . '</a>';
            if ($index < count($authorNames) - 1) {
                echo ', ';
            }
        }
        ?>
    </p>
    <p><strong>Catégorie(s):</strong> 
        <?php
        $categoryNames = explode(', ', $book['categories']);
        $categoryIds = explode(',', $book['category_ids']);
        foreach ($categoryNames as $index => $name) {
            $id = intval($categoryIds[$index]);
            echo '<a href="../categories/view.php?id=' . $id . '">' . safe_htmlspecialchars($name) . '</a>';
            if ($index < count($categoryNames) - 1) {
                echo ', ';
            }
        }
        ?>
    </p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(safe_htmlspecialchars($book['description'])) ?></p>
</div>

<?php include '../footer.php'; ?>