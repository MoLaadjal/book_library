<?php
include '../header.php';
require '../config.php';

// Fonction pour échapper les caractères spéciaux
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

$author_id = intval($_GET['id']);

// Requête pour obtenir les détails de l'auteur
$sql = "SELECT * FROM authors WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$author_id]);
$author = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$author) {
    echo "<div class='container'><p class='text-danger'>Auteur non trouvé.</p></div>";
    include '../footer.php';
    exit();
}

// Requête pour obtenir les livres de l'auteur
$sql = "SELECT b.id, b.title FROM books b 
        JOIN book_authors ba ON b.id = ba.book_id 
        WHERE ba.author_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$author_id]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2><?= safe_htmlspecialchars($author['name']) ?></h2>
    <?php if ($author['photo']): ?>
        <img src="../uploads/authors/<?= safe_htmlspecialchars($author['photo']) ?>" alt="Photo de <?= safe_htmlspecialchars($author['name']) ?>" class="img-fluid mb-3" style="max-width: 300px;">
    <?php endif; ?>
    <p><strong>Biographie:</strong> <?= nl2br(safe_htmlspecialchars($author['bio'])) ?></p>
    
    <h3>Œuvres</h3>
    <?php if (!empty($books)): ?>
        <ul>
            <?php foreach ($books as $book): ?>
                <li><a href="../books/view.php?id=<?= intval($book['id']) ?>"><?= safe_htmlspecialchars($book['title']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun livre trouvé pour cet auteur.</p>
    <?php endif; ?>
</div>

<?php include '../footer.php'; ?>