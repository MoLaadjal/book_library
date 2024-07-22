<?php
$host = 'localhost';
$db = 'book_library_db';
$user = 'root';
$password = '';

try {
    $pdo = new \PDO('mysql:host=localhost;dbname=book_library_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}
?>