<?php include '../header.php'; ?>

<?php
require '../config.php';

$category_id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Mise à jour de la catégorie
    $sql = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $category_id]);

    header("Location: list.php");
    exit();
}

// Requête pour obtenir les détails de la catégorie
$sql = "SELECT * FROM categories WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Modifier la catégorie</h2>
<form method="post">
    <label for="name">Nom:</label><br>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" required><br>
    <input type="submit" value="Modifier">
</form>

<?php include '../footer.php'; ?>