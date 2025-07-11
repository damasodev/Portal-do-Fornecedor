<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

$printers = $pdo->query("
    SELECT p.*, w.name AS warehouse_name
    FROM printers p
    JOIN warehouses w ON p.warehouse_id = w.id
")->fetchAll();

$warehouses = $pdo->query("SELECT * FROM warehouses")->fetchAll();

$filter_warehouse = $_GET['warehouse'] ?? '';
if ($filter_warehouse) {
    $printers = $pdo->prepare("
        SELECT p.*, w.name AS warehouse_name
        FROM printers p
        JOIN warehouses w ON p.warehouse_id = w.id
        WHERE w.id = ?
    ");
    $printers->execute([$filter_warehouse]);
    $printers = $printers->fetchAll();
}

$supplies = $pdo->query("SELECT * FROM supplies")->fetchAll();

$movements = $pdo->query("
    SELECT m.*, s.model AS supply_model, p.model AS printer_model, u.name AS user_name
    FROM movements m
    JOIN supplies s ON m.supply_id = s.id
    LEFT JOIN printers p ON m.printer_id = p.id
    JOIN users u ON m.user_id = u.id
    ORDER BY m.created_at DESC
    LIMIT 100
")->fetchAll();

// Agrupamento por data
$groupedData = [];
foreach ($movements as $m) {
    $date = date('d/m', strtotime($m['created_at']));
    if (!isset($groupedData[$date])) {
        $groupedData[$date] = ['entrada' => 0, 'saida' => 0];
    }
    $groupedData[$date][$m['type']] += $m['quantity'];
}
$labels = array_keys($groupedData);
$entradas = array_column($groupedData, 'entrada');
$saidas = array_column($groupedData, 'saida');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Monitoramento</title>
    <link rel="shortcut icon" href="img/Logo.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
</head>
<body>

<h1>Bem-vindo, <?= htmlspecialchars($user['name']) ?></h1>
<a href="logout.php" class="Btn" title="Sair">
  <div class="sign">
    <svg viewBox="0 0 512 512">
      <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
    </svg>
  </div>
</a>


<!-- Filtro de Galpão -->
<form method="GET" class="filter-form">
    <label for="warehouse">Filtrar por Galpão:</label>
    <div class="custom-select-wrapper">
        <select name="warehouse" id="warehouse" onchange="this.form.submit()">
            <option value="">Todos</option>
            <?php foreach ($warehouses as $w): ?>
                <option value="<?= $w['id'] ?>" <?= $filter_warehouse == $w['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($w['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<!-- Impressoras -->
<h2>Status das Impressoras</h2>
<table>
    <tr>
        <th>Modelo</th>
        <th>Galpão</th>
        <th>Status</th>
        <th>Setor</th>
        <?php if ($role == 'fornecedor' || $role == 'admin'): ?>
            <th>IP</th>
        <?php endif; ?>
        <?php if ($role == 'admin'): ?><th>Ações</th><?php endif; ?>
    </tr>
    <?php foreach ($printers as $printer): ?>
        <tr>
            <td><?= htmlspecialchars($printer['model']) ?></td>
            <td><?= htmlspecialchars($printer['warehouse_name']) ?></td>
            <td>
                <span class="status <?= strtolower(str_replace(' ', '-', $printer['status'])) ?>">
                    <?= htmlspecialchars($printer['status']) ?>
                </span>
            </td>
            <td><?= htmlspecialchars($printer['sector'] ?? '-') ?></td>
            <?php if ($role == 'fornecedor' || $role == 'admin'): ?>
                <td>
                    <?php if (!empty($printer['ip'])): ?>
                        <a href="http://<?= htmlspecialchars($printer['ip']) ?>" target="_blank">
                            <?= htmlspecialchars($printer['ip']) ?>
                        </a>
                    <?php else: ?>
                        <span style="color: #999;">Sem IP</span>
                    <?php endif; ?>
                </td>
            <?php endif; ?>

            <?php if ($role == 'admin'): ?>
                <td>
                    <a href="admin/edit_printer.php?id=<?= $printer['id'] ?>">Editar</a> |
                    <a href="admin/delete_printer.php?id=<?= $printer['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta impressora?')">Excluir</a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Estoque -->
<h2>Estoque de Suprimentos</h2>
<table>
    <tr>
        <th>Tipo</th>
        <th>Modelo</th>
        <th>Estoque</th>
        <?php if ($role == 'admin'): ?><th>Barcode</th><?php endif; ?>
    </tr>
    <?php foreach ($supplies as $s): ?>
        <tr>
            <td><?= $s['type'] ?></td>
            <td><?= $s['model'] ?></td>
            <td><?= $s['stock'] ?></td>
            <?php if ($role == 'admin'): ?>
                <td><img src="includes/barcode.php?code=<?= urlencode($s['barcode']) ?>" height="50"></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Movimentações -->
<h2>Últimas Movimentações</h2>
<table>
    <tr>
        <th>Data</th>
        <th>Tipo</th>
        <th>Suprimento</th>
        <th>Impressora</th>
        <th>Qtd</th>
        <th>Por</th>
    </tr>
    <?php foreach ($movements as $m): ?>
        <tr>
            <td><?= $m['created_at'] ?></td>
            <td><?= $m['type'] ?></td>
            <td><?= $m['supply_model'] ?></td>
            <td><?= $m['printer_model'] ?? '-' ?></td>
            <td><?= $m['quantity'] ?></td>
            <td><?= $m['user_name'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<section style="background:#fff; border-radius:12px; padding:1.5rem; margin-top:2rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06)">
    <h2 style="color:#1e293b; font-size:1.3rem; margin-bottom:1rem;">Resumo de Entradas e Saídas</h2>
    <canvas id="movementsChart" height="100"></canvas>
</section>

<script>

    const ctx = document.getElementById('movementsChart').getContext('2d');
    const movementsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Entradas',
                    data: <?= json_encode($entradas) ?>,
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Saídas',
                    data: <?= json_encode($saidas) ?>,
                    backgroundColor: 'rgba(239, 68, 68, 0.7)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                x: { stacked: false },
                y: {
                    beginAtZero: true,
                    stacked: false,
                    ticks: { precision: 0 }
                }
            }
        }
    });
</script>

<?php if ($role == 'admin'): ?>
    <div class="admin-only">
        <h2>Acesso Administrativo</h2>
        <ul>
            <li><a class="btn" href="admin/add_printer.php">Cadastrar Impressora</a></li>
            <li><a class="btn" href="admin/supplies.php">Gerenciar Suprimentos</a></li>
            <li><a class="btn" href="admin/movements.php">Registrar Movimentações</a></li>
        </ul>
    </div>
<?php endif; ?>

</body>
</html>
