<?php
// Exibir erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/db.php';
session_start();

// Verificação de permissão
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// Verificar se conexão com PDO foi estabelecida
if (!$pdo) {
    die("Erro de conexão com o banco de dados.");
}

// Processar envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = trim($_POST['model'] ?? '');
    $status = $_POST['status'] ?? 'Ativa';
    $ip = trim($_POST['ip'] ?? '');
    $sector = trim($_POST['sector'] ?? '');
    $warehouse_id = $_POST['warehouse_id'] ?? '';

    if ($model && $ip && $warehouse_id && $sector) {
        try {
            $stmt = $pdo->prepare("INSERT INTO printers (model, status, ip, sector, warehouse_id) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$model, $status, $ip, $sector, $warehouse_id])) {
                $success = "Impressora cadastrada com sucesso.";
            } else {
                $errorInfo = $stmt->errorInfo();
                $error = "Erro ao cadastrar: " . $errorInfo[2];
            }
        } catch (PDOException $e) {
            $error = "Erro no banco de dados: " . $e->getMessage();
        }
    } else {
        $error = "Todos os campos são obrigatórios.";
    }
}

// Buscar galpões
try {
    $warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar galpões: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Impressora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 2rem;
        }
        form {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            background: #1e88e5;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-voltar {
            display: inline-block;
            margin-bottom: 2rem;
            background: #ccc;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            color: #000;
        }
        .alert {
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 6px;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<a href="../index.php" class="btn-voltar">← Voltar ao Painel</a>

<form method="post">
    <h2>Cadastro de Impressora</h2>

    <?php if ($success): ?>
        <div class="alert success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <label for="model">Modelo:</label>
    <input type="text" name="model" id="model" required>

    <label for="status">Status:</label>
    <select name="status" id="status" required>
        <option value="Ativa">Ativa</option>
        <option value="Inativa">Inativa</option>
        <option value="Manutenção">Manutenção</option>
        <option value="Com defeito">Com defeito</option>
        <option value="Desligada">Desligada</option>
        <option value="Sem conexão">Sem conexão</option>
    </select>

    <label for="ip">Endereço IP:</label>
    <input type="text" name="ip" id="ip" placeholder="192.168.0.100" required pattern="\b(?:\d{1,3}\.){3}\d{1,3}\b" title="Digite um IP válido">

    <label for="sector">Setor:</label>
    <input type="text" name="sector" id="sector" required placeholder="Ex: Expedição">

    <label for="warehouse_id">Galpão:</label>
    <select name="warehouse_id" id="warehouse_id" required>
        <option value="">Selecione o Galpão</option>
        <?php foreach ($warehouses as $w): ?>
            <option value="<?= htmlspecialchars($w['id']) ?>"><?= htmlspecialchars($w['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Cadastrar Impressora</button>
</form>

</body>
</html>
