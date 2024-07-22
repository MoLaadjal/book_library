<?php
include '../header.php';
require '../config.php';

$category_id = intval($_GET['id']); // Assurez-vous que l'ID est un entier

// Requête pour obtenir les détails de la catégorie
$sql = "SELECT name FROM categories WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// Requête pour obtenir les livres de cette catégorie
$sql = "SELECT b.id, b.title, b.description, b.cover_image, 
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors,
               GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories,
               GROUP_CONCAT(DISTINCT a.id SEPARATOR ',') AS author_ids
        FROM books b
        LEFT JOIN book_authors ba ON b.id = ba.book_id
        LEFT JOIN authors a ON ba.author_id = a.id
        LEFT JOIN book_categories bc ON b.id = bc.book_id
        LEFT JOIN categories c ON bc.category_id = c.id
        WHERE bc.category_id = ?
        GROUP BY b.id";
$stmt = $pdo->prepare($sql);
$stmt->execute([$category_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour éviter les erreurs de htmlspecialchars avec des valeurs nulles
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Fonction pour tronquer une chaîne à une longueur spécifique et ajouter des points de suspension
function truncate_string($string, $length = 100) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...';
    } else {
        return $string;
    }
}
?>

<div class="container">
    <h2 class="text-center my-4"><?= safe_htmlspecialchars($category['name']) ?></h2>
    <div class="row justify-content-center">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
                    <div class="card h-100 shadow-sm">
                        <a href="../books/view.php?id=<?= intval($book['id']) ?>">
                            <img src="../uploads/<?= safe_htmlspecialchars($book['cover_image']) ?>" class="card-img-top img-fluid" style="height: 300px; object-fit: contain;" alt="Couverture de <?= safe_htmlspecialchars($book['title']) ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= safe_htmlspecialchars($book['title']) ?></h5>
                            <p class="card-text mb-1"><strong>Auteur(s):</strong>
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
                            <p class="card-text mb-1"><strong>Genre(s):</strong> <?= safe_htmlspecialchars($book['categories']) ?></p>
                            <p class="card-text mb-1"><strong>Description:</strong> <?= nl2br(safe_htmlspecialchars(truncate_string($book['description'], 100))) ?></p>
                            <div class="mt-auto text-center">
                                <a href="../books/view.php?id=<?= intval($book['id']) ?>" class="btn btn-primary">Voir plus</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-danger">Aucun livre trouvé dans cette catégorie.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; ?>