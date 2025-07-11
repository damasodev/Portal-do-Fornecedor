<?php
session_start();
require_once '../config/db.php';

// Verifica se é admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Verifica se o ID foi passado corretamente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de impressora inválido.');
}

$printer_id = $_GET['id'];

// Verifica se a impressora existe
$stmt = $pdo->prepare("SELECT * FROM printers WHERE id = ?");
$stmt->execute([$printer_id]);
$printer = $stmt->fetch();

if (!$printer) {
    die('Impressora não encontrada.');
}

// Deleta a impressora
$delete = $pdo->prepare("DELETE FROM printers WHERE id = ?");
$delete->execute([$printer_id]);

// Redireciona de volta para o painel
header('Location: ../index.php');
exit;
