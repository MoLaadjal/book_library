<?php 
header('Content-Type: text/html; charset=utf-8');
include 'header.php'; 
?>

<div class="jumbotron text-center">
    <h1 class="display-4">Bienvenue à la Gestion de Bibliothèque</h1>
    <p class="lead">Gérez vos livres, auteurs et catégories facilement.</p>
</div>

<div class="row text-center">
    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Livres</h5>
                <p class="card-text">Gérez la collection de livres de la bibliothèque.</p>
                <a href="books/list.php" class="btn btn-primary">Voir les livres</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Auteurs</h5>
                <p class="card-text">Gérez les auteurs des livres.</p>
                <a href="authors/list.php" class="btn btn-primary">Voir les auteurs</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Catégories</h5>
                <p class="card-text">Gérez les catégories de livres.</p>
                <a href="categories/list.php" class="btn btn-primary">Voir les catégories</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>