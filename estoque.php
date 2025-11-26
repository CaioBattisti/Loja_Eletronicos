<?php
// --- 1. Controle de Sessão e Acesso ---
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit;
}

include('conexao.php');
$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];

$msg = "";
$tipoMsg = "";

// ===============================================
// REGISTRO DE MOVIMENTAÇÃO (entrada / saída)
// ===============================================
if (isset($_POST['mover'])) {
    $id_produto = $_POST['produto'];
    $tipo = $_POST['tipo'];
    $quantidade = (int)$_POST['quantidade'];
    $data = $_POST['data'];

    // Atualizar o estoque atual na tabela 'produtos'
    if ($tipo == 'entrada') {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual + $quantidade WHERE id_produto=$id_produto");
    } else {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual - $quantidade WHERE id_produto=$id_produto");
    }

    // Registrar movimentação na tabela 'movimentacoes'
    if ($conn->query("INSERT INTO movimentacoes (id_produto, tipo, quantidade, data_movimentacao, id_usuario)
                      VALUES ($id_produto, '$tipo', $quantidade, '$data', $id_usuario)")) {

        // Verificar estoque mínimo
        $produto = $conn->query("SELECT nome, quantidade_atual, quantidade_minima FROM produtos WHERE id_produto=$id_produto")->fetch_assoc();
        if ($produto['quantidade_atual'] < $produto['quantidade_minima']) {
            $msg = "Atenção: O estoque do produto '{$produto['nome']}' está abaixo do mínimo!";
            $tipoMsg = "alerta";
        } else {
            $msg = "Movimentação registrada com sucesso!";
            $tipoMsg = "sucesso";
        }
    } else {
        $msg = "Erro ao registrar movimentação.";
        $tipoMsg = "erro";
    }
}

// ===============================================
// LISTAGEM DE PRODUTOS
// ===============================================
$produtos = $conn->query("SELECT * FROM produtos");

// ===============================================
// LISTAGEM DO HISTÓRICO DE MOVIMENTAÇÕES
// ===============================================
$movimentacoes = $conn->query("
    SELECT m.*, p.nome AS produto_nome, u.nome AS usuario_nome
    FROM movimentacoes m
    JOIN produtos p ON m.id_produto = p.id_produto
    JOIN usuarios u ON m.id_usuario = u.id_usuario
    ORDER BY m.data_movimentacao DESC
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Estoque</title>
<link rel="stylesheet" href="style.css">
<style>
.msg {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
  text-align: center;
  font-weight: bold;
}
.msg.sucesso {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}
.msg.erro {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
.msg.alerta {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeeba;
}
</style>
</head>
<body>
<div class="container">
<h2>Gestão de Estoque</h2>

<!-- Mensagem de feedback -->
<?php if (!empty($msg)): ?>
  <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
<?php endif; ?>

<!-- Formulário de movimentação -->
<form method="post">
  <label>Produto:</label>
  <select name="produto" required>
    <option value="">Selecione um produto</option>
    <?php while ($p = $produtos->fetch_assoc()): ?>
      <option value="<?= $p['id_produto'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
    <?php endwhile; ?>
  </select><br>

  <label>Tipo de Movimentação:</label>
  <select name="tipo" required>
    <option value="entrada">Entrada</option>
    <option value="saida">Saída</option>
  </select><br>

  <label>Quantidade:</label>
  <input type="number" name="quantidade" min="1" required><br>

  <label>Data:</label>
  <input type="date" name="data" required><br>

  <button type="submit" name="mover">Registrar Movimentação</button>
</form>

<h3>Histórico de Movimentações</h3>
<table border="1">
<tr>
    <th>ID</th>
    <th>Produto</th>
    <th>Tipo</th>
    <th>Quantidade</th>
    <th>Data</th>
    <th>Usuário</th>
</tr>
<?php if ($movimentacoes && $movimentacoes->num_rows > 0): ?>
<?php while ($m = $movimentacoes->fetch_assoc()): ?>
<tr>
    <td><?= $m['id_movimentacao'] ?></td>
    <td><?= htmlspecialchars($m['produto_nome']) ?></td>
    <td><?= htmlspecialchars($m['tipo']) ?></td>
    <td><?= $m['quantidade'] ?></td>
    <td><?= date('d/m/Y', strtotime($m['data_movimentacao'])) ?></td>
    <td><?= htmlspecialchars($m['usuario_nome'] ?? 'Usuário não encontrado') ?></td> <!-- Exibe o nome do usuário -->
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="6">Nenhuma movimentação registrada.</td></tr>
<?php endif; ?>
</table>

<h3>Produtos em Estoque</h3>
<table border="1">
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Categoria</th>
    <th>Qtd Atual</th>
    <th>Qtd Mínima</th>
    <th>Tensão</th>
    <th>Dimensões</th>
    <th>Resolução Tela</th>
    <th>Capacidade Armazenamento</th>
    <th>Conectividade</th>
</tr>
<?php
$produtos = $conn->query("SELECT * FROM produtos");
if ($produtos->num_rows > 0):
    while ($p = $produtos->fetch_assoc()):
?>
<tr>
    <td><?= $p['id_produto'] ?></td>
    <td><?= htmlspecialchars($p['nome']) ?></td>
    <td><?= htmlspecialchars($p['categoria']) ?></td>
    <td><?= $p['quantidade_atual'] ?></td>
    <td><?= $p['quantidade_minima'] ?></td>
    <td><?= htmlspecialchars($p['tensao']) ?></td>
    <td><?= htmlspecialchars($p['dimensoes']) ?></td>
    <td><?= htmlspecialchars($p['resolucao_tela']) ?></td>
    <td><?= htmlspecialchars($p['capacidade_armazenamento']) ?></td>
    <td><?= htmlspecialchars($p['conectividade']) ?></td>
</tr>
<?php
    endwhile;
else:
?>
<tr><td colspan="10">Nenhum produto encontrado.</td></tr>
<?php endif; ?>
</table>

<br>
<a href="Painel.php">⬅ Voltar ao menu principal</a>
</div>
</body>
</html>