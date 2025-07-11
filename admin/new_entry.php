<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $model = $_POST['model'];
    $quantity = intval($_POST['quantity']);
    
    // Gera um código de barras único (poderia usar UUID)
    $barcode = strtoupper(uniqid('SUP_'));

    $stmt = $pdo->prepare("INSERT INTO supplies (type, model, stock, barcode) VALUES (?, ?, ?, ?)");
    $stmt->execute([$type, $model, $quantity, $barcode]);

    $success = true;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
      <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <meta charset="UTF-8">
    <title>Nova Entrada de Suprimento</title>
    <style>
        body { font-family: Arial; padding: 2rem; }
        form { max-width: 400px; margin: auto; }
        label { display: block; margin-top: 1rem; }
        input, select { width: 100%; padding: 8px; }
        .btn { margin-top: 1rem; background: #020087; color: white; padding: 10px; border: none; cursor: pointer; }
        .btn-voltar {
    display: inline-block;
    padding: 8px 16px;
    background-color: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    margin-bottom: 1.5rem;
    transition: background 0.3s ease;
}

.btn-voltar:hover {
    background-color: #000066;
}

    </style>
</head>
<body>
<h2>Nova Entrada de Suprimento</h2>

<?php if ($success): ?>
    <p><strong>Suprimento cadastrado com sucesso!</strong></p>
<?php endif; ?>

<a href="http://189.44.239.10:8081/index.php" class="btn-voltar">← Voltar ao Painel</a>

<form method="post">
    <label>Tipo:</label>
    <select name="type" required>
        <option value="toner">Toner</option>
        <option value="fotocondutor">Fotocondutor</option>
    </select>

    <label>Modelo:</label>
    <input type="text" name="model" required>

    <label>Quantidade:</label>
    <input type="number" name="quantity" min="1" required>

    <button class="btn" type="submit">Cadastrar e Gerar Código</button>
</form>
</body>
</html>
