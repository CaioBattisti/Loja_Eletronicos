<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

$usuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel - Caio Eletronicos</title>
<link rel="stylesheet" href="style.css">

<style>

    /* Centralização na tela */
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;  /* horizontal */
        align-items: center;      /* vertical */
        background-color: #f2f2f2;
        font-family: Arial, sans-serif;
    }

    .container {
        text-align: center;
    }

    .btn-link {
        display: block;
        margin: 10px 0;
        text-decoration: none;
    }

    .btn-link button {
        width: 250px;
        padding: 12px;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        background-color: green;
        color: #fff;
        transition: 0.2s;
    }

    .btn-link button:hover {
        background-color: #2980b9;
    }

    .btn-sair button {
        background-color: #e74c3c;
    }

    .btn-sair button:hover {
        background-color: #c0392b;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Bem-vindo, <?php echo $usuario; ?>!</h2>

    <a href="cadastro_produto.php" class="btn-link">
        <button>📦 Cadastro de Produtos</button>
    </a>

    <a href="estoque.php" class="btn-link">
        <button>📊 Gestão de Estoque</button>
    </a>

    <a href="logout.php" class="btn-link btn-sair">
        <button>🚪 Sair</button>
    </a>
</div>

</body>
</html>