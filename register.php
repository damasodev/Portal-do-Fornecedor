<?php
session_start();
require 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'fornecedor';

    $check = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->rowCount() > 0) {
        $error = "E-mail já cadastrado!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role]);
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');

        body {
            font-family: "Outfit", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #fff;
            padding: 2.5rem 3rem;
            border-radius: 1rem;
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-container input,
        .form-container select {
            width: 100%;
            padding: 0.8rem;
            margin: 0.6rem 0;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
            font-size: 1rem;
        }

        .form-container button {
            width: 100%;
            padding: 0.8rem;
            background-color: #2d89ef;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background-color: #1b5fbf;
        }

        .form-container a {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #2d89ef;
            text-decoration: none;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .form-container .error {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Cadastro de Usuário</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="name" placeholder="Nome completo" required />
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Senha" required />

        <label for="role">Tipo de usuário:</label>
        <select name="role" id="role" required>
            <option value="admin">Admin (T.I)</option>
            <option value="gerente">Gerente</option>
            <option value="fornecedor" selected>Fornecedor</option>
        </select>

        <button type="submit">Cadastrar</button>
    </form>

    <a href="login.php">Voltar ao login</a>
</div>

</body>
</html>
