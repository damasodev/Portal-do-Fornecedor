<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Lista de saídas que não têm entrada correspondente
$items = $pdo->query("
    SELECT 
        s.type, s.model, s.barcode, 
        p.model AS printer_model, 
        m.created_at, u.name AS user_name
    FROM movements m
    JOIN supplies s ON m.supply_id = s.id
    LEFT JOIN printers p ON m.printer_id = p.id
    JOIN users u ON m.user_id = u.id
    WHERE m.type = 'saida'
    AND NOT EXISTS (
        SELECT 1 FROM movements m2
        WHERE m2.type = 'entrada' 
        AND m2.supply_id = m.supply_id 
        AND m2.created_at > m.created_at
    )
    ORDER BY m.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Itens em Uso</title>
</head>
<body>
    <h2>Itens em Uso (fora da empresa)</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Tipo</th>
            <th>Modelo</th>
            <th>Barcode</th>
            <th>Impressora</th>
            <th>Data de Saída</th>
            <th>Por</th>
        </tr>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= $item['type'] ?></td>
            <td><?= $item['model'] ?></td>
            <td><?= $item['barcode'] ?></td>
            <td><?= $item['printer_model'] ?? '-' ?></td>
            <td><?= $item['created_at'] ?></td>
            <td><?= $item['user_name'] ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p><a href="index.php">Voltar</a></p>
</body>
</html>
