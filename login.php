<?php
session_start();
require 'config/db.php';

// Redireciona se o usuário já estiver logado
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        header("Location: index.php");
        exit;
    } else {
        $error = "Email ou senha inválidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link rel="shortcut icon" href="img/Logo.ico" type="image/x-icon">
    <meta charset="UTF-8">
    <title>Login - Polistampo</title>

    <style>
       @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap');

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Outfit', sans-serif;
    background: linear-gradient(135deg, #d15427, #f5803e);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-container {
    background: #ffffff;
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
    width: 100%;
    max-width: 420px;
    position: relative;
    overflow: hidden;
}

.login-container::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(209,84,39,0.3), transparent);
    border-radius: 50%;
}

.login-container h2 {
    text-align: center;
    font-weight: 700;
    color: #d15427;
    margin-bottom: 0.3rem;
    font-size: 1.9rem;
}

h3 {
    text-align: center;
    font-weight: 400;
    font-size: 1rem;
    color: #888;
    margin-bottom: 2rem;
}

.logo {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
}

.logo img {
    max-width: 120px;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
}

input {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 1rem;
    border: 1px solid #d6d6d6;
    border-radius: 12px;
    background-color: #f9f9f9;
    color: #333;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input:focus {
    outline: none;
    background-color: #fff;
    border-color: #d15427;
    box-shadow: 0 0 0 3px rgba(209, 84, 39, 0.2);
}

button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg, #d15427, #a93b1c);
    border: none;
    border-radius: 12px;
    color: #fff;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.4s ease, transform 0.2s ease;
}

button:hover {
    background: linear-gradient(90deg, #a93b1c, #d15427);
    transform: translateY(-2px);
}

a {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: #999;
    text-decoration: none;
    font-size: 0.9rem;
}

a:hover {
    text-decoration: underline;
    color: #d15427;
}

.error {
    background: #ffdede;
    color: #a10000;
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 500;
    border: 1px solid #ff8b8b;
}

@media (max-width: 480px) {
    .login-container {
        padding: 2rem;
    }

    .login-container h2 {
        font-size: 1.6rem;
    }

    h3 {
        font-size: 0.95rem;
    }
}
    </style>
</head>
<body>
    <form method="post" class="login-container">
        <!-- LOGO -->
        <div class="logo">
            <img src="img/Logo.png" alt="Logo da Empresa" />
        </div>

        <h2>Portal dos Fornecedores</h2>
        <h3>Tecnologia da Informação</h3>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="E-mail" required />
        <input type="password" name="password" placeholder="Senha" required />
        <button type="submit">Entrar</button>
    </form>
</body>
</html>
