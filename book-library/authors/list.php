<?php include '../header.php'; ?>

<?php
require '../config.php';

// Requête pour obtenir les auteurs
$sql = "SELECT * FROM authors";
$stmt = $pdo->query($sql);
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Liste des auteurs</h2>
<div class="actions mb-4 text-center">
    <a href="create.php" class="btn btn-success">Ajouter un nouvel auteur</a>
</div>
<div class="row">
    <?php foreach ($authors as $author): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="view.php?id=<?= $author['id'] ?>">
                    <?php if ($author['photo']): ?>
                        <img src="../uploads/authors/<?= htmlspecialchars($author['photo']) ?>" class="card-img-top img-fluid" alt="Photo de <?= htmlspecialchars($author['name']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <img src="../default-author.jpg" class="card-img-top img-fluid" alt="Photo par défaut" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($author['name']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars(truncate_string($author['bio'], 100))) ?></p>
                </div>
                <div class="card-footer text-center">
                    <a href="view.php?id=<?= $author['id'] ?>" class="btn btn-primary mb-2">Voir plus</a>
                    <a href="update.php?id=<?= $author['id'] ?>" class="btn btn-warning mb-2">Modifier</a>
                    <a href="delete.php?id=<?= $author['id'] ?>" class="btn btn-danger mb-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet auteur?');">Supprimer</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include '../footer.php'; ?>

<?php
// Fonction pour tronquer une chaîne à une longueur spécifique et ajouter des points de suspension
function truncate_string($string, $length = 100) {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . '...';
    } else {
        return $string;
    }
}
?>