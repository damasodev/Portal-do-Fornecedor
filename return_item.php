<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = $_POST['barcode'] ?? '';

    // Buscar o suprimento pelo código de barras
    $supply = $pdo->prepare("SELECT * FROM supplies WHERE barcode = ?");
    $supply->execute([$barcode]);
    $supply = $supply->fetch();

    if ($supply) {
        // Inserir movimentação de entrada
        $insert = $pdo->prepare("
            INSERT INTO movements (type, supply_id, quantity, user_id, created_at)
            VALUES ('entrada', ?, 1, ?, NOW())
        ");
        $insert->execute([$supply['id'], $user['id']]);

        // Atualiza estoque
        $pdo->prepare("UPDATE supplies SET stock = stock + 1 WHERE id = ?")->execute([$supply['id']]);

        $message = "Entrada registrada com sucesso para o código: " . htmlspecialchars($barcode);
    } else {
        $message = "Código de barras não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registrar Retorno</title>
</head>
<body>
    <h2>Registrar Retorno de Toner/Foto Condutor</h2>
    <?php if ($message): ?>
        <p><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label for="barcode">Código de Barras:</label>
        <input type="text" name="barcode" id="barcode" autofocus required>
        <button type="submit">Registrar Retorno</button>
    </form>

    <p><a href="index.php">Voltar</a></p>
</body>
</html>
