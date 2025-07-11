<?php
$host = 'localhost';
$dbname = 'toner_db';
$username = 'root';
$password = 'Poli@1234';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
