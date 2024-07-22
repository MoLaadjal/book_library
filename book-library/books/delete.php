<?php
require '../config.php';

if (isset($_GET['id'])) {
    $book_id = intval($_GET['id']); // Assurez-vous que l'ID est un entier

    try {
        // Débuter une transaction
        $pdo->beginTransaction();

        // Supprimer les relations entre le livre et ses auteurs
        $sql = "DELETE FROM book_authors WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        // Supprimer les relations entre le livre et ses catégories
        $sql = "DELETE FROM book_categories WHERE book_id = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':book_id' => $book_id]);

        // Supprimer le livre
        $sql = "DELETE FROM books WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $book_id]);

        // Valider la transaction
        $pdo->commit();

        // Rediriger vers la liste des livres
        header("Location: list.php");
        exit();
    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $pdo->rollBack();
        // Afficher un message d'erreur (vous pourriez également le consigner dans un journal d'erreurs)
        die("Erreur lors de la suppression du livre : " . $e->getMessage());
    }
} else {
    // Si aucun ID n'est fourni, rediriger vers la liste des livres
    header("Location: list.php");
    exit();
}
?>