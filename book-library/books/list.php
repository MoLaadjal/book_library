<?php 
header('Content-Type: text/html; charset=utf-8');
include '../header.php'; 
?>

<?php
require '../config.php';

// Récupérer les auteurs et catégories pour les filtres
$stmt_authors = $pdo->query("SELECT * FROM authors");
$authors = $stmt_authors->fetchAll(PDO::FETCH_ASSOC);

$stmt_categories = $pdo->query("SELECT * FROM categories");
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Initialiser les filtres
$selected_author = isset($_GET['author']) ? intval($_GET['author']) : '';
$selected_category = isset($_GET['category']) ? intval($_GET['category']) : '';

// Construire la requête SQL avec les filtres
$sql = "SELECT b.id, b.title, b.publication_year, b.description, b.cover_image, 
               GROUP_CONCAT(DISTINCT a.name SEPARATOR ', ') AS authors, 
               GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS categories,
               GROUP_CONCAT(DISTINCT a.id SEPARATOR ',') AS author_ids,
               GROUP_CONCAT(DISTINCT c.id SEPARATOR ',') AS category_ids
        FROM books b
        LEFT JOIN book_authors ba ON b.id = ba.book_id
        LEFT JOIN authors a ON ba.author_id = a.id
        LEFT JOIN book_categories bc ON b.id = bc.book_id
        LEFT JOIN categories c ON bc.category_id = c.id
        WHERE 1";

$params = [];
if ($selected_author) {
    $sql .= " AND a.id = :author_id";
    $params['author_id'] = $selected_author;
}
if ($selected_category) {
    $sql .= " AND c.id = :category_id";
    $params['category_id'] = $selected_category;
}

$sql .= " GROUP BY b.id";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
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

// Debug: Vérifiez si des livres sont récupérés
if (empty($books)) {
    echo "<p class='text-center text-danger'>Aucun livre trouvé.</p>";
}
?>

<h2 class="text-center my-4">Liste des livres</h2>

<div class="filters mb-4 text-center">
    <form method="get" action="" class="form-inline justify-content-center">
        <label for="author" class="mr-2">Auteur:</label>
        <select name="author" id="author" class="form-control mr-3">
            <option value="">Tous les auteurs</option>
            <?php foreach ($authors as $author): ?>
                <option value="<?= $author['id'] ?>" <?= $selected_author == $author['id'] ? 'selected' : '' ?>><?= safe_htmlspecialchars($author['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="category" class="mr-2">Genre:</label>
        <select name="category" id="category" class="form-control mr-3">
            <option value="">Tous les genres</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>" <?= $selected_category == $category['id'] ? 'selected' : '' ?>><?= safe_htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
        </select>
        
        <button type="submit" class="btn btn-primary">Filtrer</button>
    </form>
</div>

<div class="actions mb-4 text-center">
    <a href="create.php" class="btn btn-success">Ajouter un nouveau livre</a>
</div>
<div class="container">
    <div class="row justify-content-center">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 d-flex align-items-stretch">
                <div class="card h-100 shadow-sm">
                    <a href="view.php?id=<?= $book['id'] ?>">
                        <img src="../uploads/<?= safe_htmlspecialchars($book['cover_image']) ?>" class="card-img-top img-fluid" style="height: 300px; object-fit: contain;" alt="Couverture de <?= safe_htmlspecialchars($book['title']) ?>">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= safe_htmlspecialchars($book['title']) ?></h5>
                        <p class="card-text mb-1"><strong>Auteur(s):</strong> 
                            <?php
                            $authorNames = explode(', ', $book['authors']);
                            $authorIds = explode(',', $book['author_ids']);
                            foreach ($authorNames as $index => $name) {
                                $id = $authorIds[$index];
                                echo '<a href="../authors/view.php?id=' . intval($id) . '">' . safe_htmlspecialchars($name) . '</a>';
                                if ($index < count($authorNames) - 1) {
                                    echo ', ';
                                }
                            }
                            ?>
                        </p>
                        <p class="card-text mb-1"><strong>Année de publication:</strong> <?= safe_htmlspecialchars($book['publication_year']) ?></p>
                        <p class="card-text mb-1"><strong>Catégories:</strong> 
                            <?php
                            $categoryNames = explode(', ', $book['categories']);
                            $categoryIds = explode(',', $book['category_ids']);
                            foreach ($categoryNames as $index => $name) {
                                $id = $categoryIds[$index];
                                echo '<a href="../categories/view.php?id=' . intval($id) . '">' . safe_htmlspecialchars($name) . '</a>';
                                if ($index < count($categoryNames) - 1) {
                                    echo ', ';
                                }
                            }
                            ?>
                        </p>
                        <p class="card-text mb-1"><strong>Description:</strong> <?= nl2br(safe_htmlspecialchars(truncate_string($book['description'], 100))) ?></p>
                    </div>
                    <div class="card-footer text-center">
                        <a href="view.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-primary mb-2">Voir plus</a>
                        <a href="update.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-warning mb-2">Modifier</a>
                        <a href="delete.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-danger mb-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre?');">Supprimer</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-danger">Aucun livre trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../footer.php'; ?>