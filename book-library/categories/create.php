<?php include '../header.php'; ?>

<?php
require '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Insertion de la catégorie
    $sql = "INSERT INTO categories (name) VALUES (?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);

    header("Location: list.php");
    exit();
}
?>

<h2>Ajouter une nouvelle catégorie</h2>
<form method="post">
    <label for="name">Nom:</label><br>
    <input type="text" id="name" name="name" required><br>
    <input type="submit" value="Ajouter">
</form>

<?php include '../footer.php'; ?>