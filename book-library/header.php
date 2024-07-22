<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestion de Bibliothèque</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        .btn-sm {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white text-center py-3">
        <h1>Gestion de Bibliothèque</h1>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/books/list.php">Livres</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/authors/list.php">Auteurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories/list.php">Catégories</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container mt-4">