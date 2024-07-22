<?php
require '../config.php';

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Supprimer les relations entre la catégorie et ses livres
    $sql = "DELETE FROM book_categories WHERE category_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);

    // Supprimer la catégorie
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id]);

    // Rediriger vers la liste des catégories
    header("Location: list.php");
    exit();
} else {
    // Si aucun ID n'est fourni, rediriger vers la liste des catégories
    header("Location: list.php");
    exit();
}
?>