<?php
require '../config.php';

// Fonction pour valider les entrées et échapper les caractères spéciaux
function safe_htmlspecialchars($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

if (isset($_GET['id'])) {
    $author_id = intval($_GET['id']); // Convertir en entier pour éviter les injections SQL

    if ($author_id > 0) {
        try {
            // Commencer une transaction
            $pdo->beginTransaction();

            // Supprimer les relations entre l'auteur et ses livres
            $sql = "DELETE FROM book_authors WHERE author_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$author_id]);

            // Supprimer l'auteur
            $sql = "DELETE FROM authors WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$author_id]);

            // Valider la transaction
            $pdo->commit();
            
            // Rediriger vers la liste des auteurs
            header("Location: list.php");
            exit();
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollBack();
            echo "Échec de la suppression : " . safe_htmlspecialchars($e->getMessage());
        }
    } else {
        // Si l'ID n'est pas valide, rediriger vers la liste des auteurs
        header("Location: list.php");
        exit();
    }
} else {
    // Si aucun ID n'est fourni, rediriger vers la liste des auteurs
    header("Location: list.php");
    exit();
}
?>