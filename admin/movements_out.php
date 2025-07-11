<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barcode = $_POST['barcode'];
    $printer_id = $_POST['printer_id'] ?? null;
    $user_id = $_SESSION['user']['id'];

    // Buscar o item
    $stmt = $pdo->prepare("SELECT * FROM supplies WHERE barcode = ?");
    $stmt->execute([$barcode]);
    $supply = $stmt->fetch();

    if ($supply && $supply['stock'] > 0) {
        // Atualizar estoque
        $pdo->prepare("UPDATE supplies SET stock = stock - 1 WHERE id = ?")
            ->execute([$supply['id']]);

        // Registrar movimentação
        $pdo->prepare("
            INSERT INTO movements (supply_id, printer_id, quantity, type, user_id)
            VALUES (?, ?, ?, ?, ?)
        ")->execute([$supply['id'], $printer_id, 1, 'saida', $user_id]);

        header('Location: index.php?saida=ok');
        exit;
    } else {
        $error = "Código não encontrado ou estoque zerado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Saída de Suprimento</title>
</head>
<body>
    <h2>Registrar Saída</h2>
    <a href="http://189.44.239.10:8081/index.php" class="btn-voltar">← Voltar ao Painel</a>
    <?php if (isset($error)): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Código de Barras:</label><br>
        <input type="text" name="barcode" required autofocus><br><br>

        <label>Impressora (opcional):</label><br>
        <select name="printer_id">
            <option value="">-- Nenhuma --</option>
            <?php
            $printers = $pdo->query("SELECT id, model FROM printers")->fetchAll();
            foreach ($printers as $p) {
                echo "<option value='{$p['id']}'>{$p['model']}</option>";
            }
            ?>
        </select><br><br>

        <button type="submit">Registrar Saída</button>
    </form>

    <p><a href="index.php">Voltar</a></p>
</body>
</html>
