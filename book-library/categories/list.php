<?php include '../header.php'; ?>

<?php
require '../config.php';

// Requête pour obtenir les catégories
$sql = "SELECT * FROM categories";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour éviter les erreurs de htmlspecialchars avec des valeurs nulles
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<div class="container my-5">
    <h2 class="text-center my-4">Liste des catégories</h2>
    <div class="actions mb-4 text-center">
        <a href="create.php" class="btn btn-success">Ajouter une nouvelle catégorie</a>
    </div>
    <div class="mb-4">
        <input type="text" id="search-category" class="form-control" placeholder="Rechercher une catégorie...">
    </div>
    <div class="row">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-center"><?= safe_htmlspecialchars($category['name']) ?></h5>
                            <div class="mt-auto text-center">
                                <a href="view.php?id=<?= $category['id'] ?>" class="btn btn-primary btn-sm mb-2">Voir les livres</a>
                                <a href="update.php?id=<?= $category['id'] ?>" class="btn btn-warning btn-sm mb-2">Modifier</a>
                                <a href="delete.php?id=<?= $category['id'] ?>" class="btn btn-danger btn-sm mb-2" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?');">Supprimer</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-danger">Aucune catégorie trouvée.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#search-category").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "search_suggestions.php",
                type: "GET",
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function(data) {
                    response($.map(data, function(item) {
                        return {
                            label: item.name,
                            value: item.name,
                            id: item.id
                        };
                    }));
                }
            });
        },
        select: function(event, ui) {
            window.location.href = 'view.php?id=' + ui.item.id;
        }
    });
});
</script>

<?php include '../footer.php'; ?>