<?php
session_start();
require_once '../config/db.php';

// Verifica se é admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Verifica se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de impressora inválido.');
}

$printer_id = $_GET['id'];

// Buscar impressora
$stmt = $pdo->prepare("SELECT * FROM printers WHERE id = ?");
$stmt->execute([$printer_id]);
$printer = $stmt->fetch();

if (!$printer) {
    die('Impressora não encontrada.');
}

// Buscar galpões
$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();

// Atualizar impressora
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $model = $_POST['model'] ?? '';
  $status = $_POST['status'] ?? '';
  $warehouse_id = $_POST['warehouse_id'] ?? '';
  $ip = $_POST['ip'] ?? '';
  $sector = $_POST['sector'] ?? '';

  $update = $pdo->prepare("UPDATE printers SET model = ?, status = ?, warehouse_id = ?, ip = ?, sector = ? WHERE id = ?");
  $update->execute([$model, $status, $warehouse_id, $ip, $sector, $printer_id]);

  header("Location: ../index.php");
  exit;
}


?>
<head>
     <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap');

:root {
  --primary: #004080;
  --accent: #d15427;
  --background: #f4f6f9;
  --white: #ffffff;
  --text: #1c1c1c;
  --border: #e0e0e0;
  --shadow: rgba(0, 0, 0, 0.05);
  --radius: 1rem;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Outfit', sans-serif;
  background-color: var(--background);
  color: var(--text);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 2rem;
  min-height: 100vh;
}

/* Headers */
h1, h2 {
  color: var(--primary);
  font-weight: 600;
  margin-bottom: 1.5rem;
}

/* Form */
form {
  background-color: var(--white);
  padding: 2rem;
  border-radius: var(--radius);
  box-shadow: 0 4px 20px var(--shadow);
  display: flex;
  flex-direction: column;
  gap: 1rem;
  width: 100%;
  max-width: 500px;
}

form label {
  font-weight: 500;
  color: var(--primary);
}

form input,
form select {
  padding: 0.75rem 1rem;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  background-color: #fff;
  font-size: 1rem;
  transition: 0.3s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

form input:focus,
form select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(209, 84, 39, 0.3);
}

button[type="submit"] {
  background-color: var(--accent);
  color: white;
  padding: 0.8rem 1.5rem;
  border: none;
  border-radius: var(--radius);
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.2s;
  box-shadow: 0 4px 12px rgba(209, 84, 39, 0.25);
}

button[type="submit"]:hover {
  background-color: #c0471e;
  transform: translateY(-2px);
}

button[type="submit"]:active {
  transform: translateY(1px);
  box-shadow: 0 2px 6px rgba(209, 84, 39, 0.3);
}

a.btn-voltar {
  align-self: flex-start;
  margin-bottom: 1rem;
  color: var(--primary);
  font-weight: 500;
  text-decoration: none;
  transition: 0.3s;
}

a.btn-voltar:hover {
  color: var(--accent);
  text-decoration: underline;
}

.status {
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: bold;
    display: inline-block;
    text-transform: capitalize;
}

.status.ativa {
    background: #22c55e;
    color: white;
}

.status.inativa {
    background: #6b7280;
    color: white;
}

.status.manutenção {
    background: #f97316;
    color: white;
}

.status.com-defeito {
    background: #ef4444;
    color: white;
}

.status.desligada {
    background: #64748b;
    color: white;
}

.status.sem-conexão {
    background: #a855f7;
    color: white;
}

/* Responsive */
@media (max-width: 600px) {
  form {
    padding: 1.5rem;
  }

  button[type="submit"] {
    width: 100%;
  }
}

     </style>
</head>
 
<h2>Editar Impressora</h2>
<a href="http://189.44.239.10:8081/index.php" class="btn-voltar">← Voltar ao Painel</a>

<form method="post">
    <label>Modelo:</label>
    <input type="text" name="model" value="<?= htmlspecialchars($printer['model']) ?>" required>

    <label>Status:</label>
    <select name="status" required>
      <option value="Ativa">Ativa</option>
      <option value="Inativa">Inativa</option>
      <option value="Manutenção">Manutenção</option>
      <option value="Com defeito">Com defeito</option>
      <option value="Desligada">Desligada</option>
      <option value="Sem conexão">Sem conexão</option>
    </select>


    <label for="ip">IP:</label>
<input type="text" name="ip" id="ip" value="<?= htmlspecialchars($printer['ip'] ?? '') ?>" placeholder="192.168.0.100" />


<label for="sector">Setor:</label>
<input type="text" name="sector" id="sector" value="<?= htmlspecialchars($printer['sector'] ?? '') ?>" required placeholder="Ex: TI, Expedição, RH">


    <label>Galpão:</label>
    <select name="warehouse_id">
        <?php foreach ($warehouses as $w): ?>
            <option value="<?= $w['id'] ?>" <?= $w['id'] == $printer['warehouse_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($w['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>
    <button type="submit">Salvar Alterações</button>
    <a href="../index.php">Cancelar</a>
</form>
