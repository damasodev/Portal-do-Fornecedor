<?php
require_once '../config/db.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $model = $_POST['model'];
    $stock = $_POST['stock'];

    // Gerar um código único (pode ser melhorado com UUIDs)
    $barcode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));

    $stmt = $pdo->prepare("INSERT INTO supplies (type, model, stock, barcode) VALUES (?, ?, ?, ?)");
    $stmt->execute([$type, $model, $stock, $barcode]);

    header("Location: supplies.php?success=1");
    exit;
}

$supplies = $pdo->query("SELECT * FROM supplies")->fetchAll();
?>
  <head>
     <style>
         @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
 
 
 :root {
    --primary: #004080;
    --accent: #d15427;
    --background: #f4f6f9;
    --white: #ffffff;
    --text: #1c1c1c;
    --border: #e0e0e0;
    --shadow: rgba(0, 0, 0, 0.05);
    --radius: 1.5rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
     font-family: "Outfit", sans-serif;
    background-color: var(--background);
    color: var(--text);
    padding: 2rem;
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

h1, h2 {
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--primary);
}

h1 {
    font-size: 2rem;
}

h2 {
    font-size: 1.5rem;
    border-bottom: 2px solid var(--primary);
    padding-bottom: 0.3rem;
}

a {
    color: #000000;
    text-decoration: none;
    transition: 0.2s ease;
}

a:hover {
    text-decoration: underline;
}

.status {
    font-weight: bold;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.9em;
    display: inline-block;
    text-transform: capitalize;
}

.status.ativo {
    background-color: #d4edda;
    color: #155724;
}

.status.inativo {
    background-color: #f8d7da;
    color: #721c24;
}

.status.manutenção {
    background-color: #fff3cd;
    color: #856404;
}

.status.sem-conexão {
    background-color: #f5c6cb;
    color: #721c24;
}



/* From Uiverse.io by vinodjangid07 */ 
.Btn {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  width: 45px;
  height: 45px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  position: relative;
  color: white;
  overflow: hidden;
  transition-duration: .3s;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: #d15427;
}

/* plus sign */
.sign {
  width: 100%;
  transition-duration: .3s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sign svg {
  width: 17px;
}

.sign svg path {
  fill: white;
  transition: 1.0s;
}



.section, table {
    background-color: var(--white);
    border-radius: var(--radius);
    padding: 1.5rem;
    box-shadow: 0 10px 20px var(--shadow);
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95rem;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--border);
}

th {
    background-color: var(--primary);
    color: var(--white);
    position: sticky;
    top: 0;
}

select {
    padding: 0.6rem 1rem;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    background-color: var(--white);
    transition: 0.3s;
    font-weight: 500;
}

select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(209, 84, 39, 0.3);
}

form {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: center;
}

canvas {
    max-width: 100%;
    border-radius: var(--radius);
    background-color: var(--white);
    padding: 1rem;
    box-shadow: 0 5px 15px var(--shadow);
}

.btn {
  align-items: center;
  appearance: none;
  background-color: #fcfcfd;
  border-radius: 4px;
  border-width: 0;
  box-shadow:
    rgba(45, 35, 66, 0.2) 0 2px 4px,
    rgba(45, 35, 66, 0.15) 0 7px 13px -3px,
    #d6d6e7 0 -3px 0 inset;
  box-sizing: border-box;
  color: #36395a;
  cursor: pointer;
  display: inline-flex;
  height: 48px;
  justify-content: center;
  line-height: 1;
  list-style: none;
  overflow: hidden;
  padding-left: 16px;
  padding-right: 16px;
  position: relative;
  text-align: left;
  text-decoration: none;
  transition:
    box-shadow 0.15s,
    transform 0.15s;
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  white-space: nowrap;
  will-change: box-shadow, transform;
  font-size: 18px;
}

.btn:focus {
  box-shadow:
    #d6d6e7 0 0 0 1.5px inset,
    rgba(45, 35, 66, 0.4) 0 2px 4px,
    rgba(45, 35, 66, 0.3) 0 7px 13px -3px,
    #d6d6e7 0 -3px 0 inset;
}

.btn:hover {
  box-shadow:
    rgba(45, 35, 66, 0.3) 0 4px 8px,
    rgba(45, 35, 66, 0.2) 0 7px 13px -3px,
    #d6d6e7 0 -3px 0 inset;
  transform: translateY(-2px);
}

.btn:active {
  box-shadow: #d6d6e7 0 3px 7px inset;
  transform: translateY(2px);
}

.admin-only ul {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.admin-only li {
    display: flex;
    justify-content: center;
}

.logout {
    position: absolute;
    right: 2rem;
    top: 2rem;
}

@media (max-width: 768px) {
    body {
        padding: 1rem;
    }

    table, th, td {
        font-size: 0.85rem;
    }

    .btn {
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
    }
}
.filter-form {
    margin: 20px 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    max-width: 300px;
    background: #f9fafb;
    padding: 16px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    font-family: 'Segoe UI', sans-serif;
}

.filter-form label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #374151;
}

.custom-select-wrapper select {
    appearance: none;
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background-color: #fff;
    font-size: 0.95rem;
    color: #111827;
    transition: border 0.3s, box-shadow 0.3s;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 20 20' fill='%236B7280' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.08 1.04l-4.25 4.25a.75.75 0 01-1.08 0L5.21 8.27a.75.75 0 01.02-1.06z' clip-rule='evenodd'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}

.custom-select-wrapper select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
    outline: none;
}

     </style>
</head>
<h2>Gerenciar Suprimentos</h2>
<a href="http://189.44.239.10:8081/index.php" class="btn-voltar">← Voltar ao Painel</a>
<form method="post">
    <input type="text" name="model" placeholder="Modelo" required>
    <select name="type">
        <option value="Toner">Toner</option>
        <option value="Fotocondutor">Fotocondutor</option>
    </select>
    <input type="number" name="stock" placeholder="Estoque inicial">
    <button type="submit">Cadastrar</button>
</form>

<?php if (isset($barcode)): ?>
    <h3>Etiqueta Gerada:</h3>
    <img src="../includes/barcode.php?code=<?= urlencode($barcode) ?>" alt="Código de Barras">
    <p><button onclick="window.print()">Imprimir</button></p>
<?php endif; ?>

<table>
    <tr><th>Tipo</th><th>Modelo</th><th>Estoque</th><th>Código</th></tr>
    <?php foreach ($supplies as $s): ?>
    <tr>
        <td><?= $s['type'] ?></td>
        <td><?= $s['model'] ?></td>
        <td><?= $s['stock'] ?></td>
        <td><?= $s['barcode'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>