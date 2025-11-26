<?php
// --- 1. Controle de Sessão e Inclusão de Conexão ---

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit;
}

include('conexao.php');

$msg = "";
$tipoMsg = "";

// ===============================================
// 2. INSERIR OU ATUALIZAR PRODUTO (Processamento do Formulário)
// ===============================================

if (isset($_POST['salvar'])) {
    $id = $_POST['id_produto'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $categoria = trim($_POST['categoria']);
    $tensao = trim($_POST['tensao']);
    $dimensoes = trim($_POST['dimensoes']);
    $resolucao_tela = trim($_POST['resolucao_tela']);
    $capacidade_armazenamento = trim($_POST['capacidade_armazenamento']);
    $conectividade = trim($_POST['conectividade']);
    $minimo = (int)$_POST['minimo'];
    $quantidade = (int)$_POST['quantidade'];

    if (!empty($id)) {
        $sql = "UPDATE produtos SET 
                    nome='$nome', 
                    descricao='$descricao', 
                    categoria='$categoria',
                    quantidade_minima='$minimo',
                    tensao='$tensao',
                    dimensoes='$dimensoes',
                    resolucao_tela='$resolucao_tela',
                    capacidade_armazenamento='$capacidade_armazenamento',
                    conectividade='$conectividade'
                WHERE id_produto=$id";
        $acao = "atualizado";
    } else {
        $sql = "INSERT INTO produtos 
                (nome, descricao, categoria, quantidade_minima, quantidade_atual, tensao, dimensoes, resolucao_tela, capacidade_armazenamento, conectividade)
                VALUES ('$nome','$descricao','$categoria','$minimo','$quantidade','$tensao','$dimensoes','$resolucao_tela','$capacidade_armazenamento','$conectividade')";
        $acao = "cadastrado";
    }

    if ($conn->query($sql)) {
        // Redirecionar para limpar o formulário e voltar ao estado de "Adicionar Novo Produto"
        header("Location: cadastro_produto.php?msg=Produto $acao com sucesso!&tipoMsg=sucesso");
        exit;
    } else {
        $msg = "Erro ao salvar o produto.";
        $tipoMsg = "erro";
    }
}

// ===============================================
// 3. EXCLUSÃO DE PRODUTO (DELETE)
// ===============================================

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    if ($conn->query("DELETE FROM produtos WHERE id_produto=$id")) {
        $msg = "Produto excluído com sucesso!";
        $tipoMsg = "sucesso";
    } else {
        $msg = "Erro ao excluir produto.";
        $tipoMsg = "erro";
    }
}

// ===============================================
// 4. BUSCA E LISTAGEM DE PRODUTOS (READ)
// ===============================================

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$sql = "SELECT * FROM produtos WHERE nome LIKE '%$busca%'";
$result = $conn->query($sql);

$produtoEdit = [
    'id_produto' => '',
    'nome' => '',
    'descricao' => '',
    'categoria' => '',
    'quantidade_minima' => '',
    'quantidade_atual' => '',
    'tensao' => '',
    'dimensoes' => '',
    'resolucao_tela' => '',
    'capacidade_armazenamento' => '',
    'conectividade' => ''
];

if (isset($_GET['editar'])) {
    $idEditar = $_GET['editar'];
    $query = $conn->query("SELECT * FROM produtos WHERE id_produto=$idEditar");
    if ($query->num_rows > 0) {
        $produtoEdit = $query->fetch_assoc();
    }
}

// Exibir mensagens de redirecionamento
if (isset($_GET['msg']) && isset($_GET['tipoMsg'])) {
    $msg = $_GET['msg'];
    $tipoMsg = $_GET['tipoMsg'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Produtos</title>
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
<h2>Cadastro de Produtos</h2>

<?php if (!empty($msg)): ?>
  <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
<?php endif; ?>

<form method="get" style="margin-bottom:10px;">
  <input type="text" name="busca" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca) ?>">
  <button type="submit">Buscar</button>
</form>

<table border="1">
<tr>
    <th>ID</th>
    <th>Nome</th>
    <th>Categoria</th>
    <th>Qtd Atual</th>
    <th>Tensão</th>
    <th>Dimensões</th>
    <th>Resolução Tela</th>
    <th>Capacidade Armazenamento</th>
    <th>Conectividade</th>
    <th>Ações</th>
</tr>
<?php if ($result->num_rows > 0): ?>
<?php while($p = $result->fetch_assoc()): ?>
<tr>
    <td><?= $p['id_produto'] ?></td>
    <td><?= htmlspecialchars($p['nome']) ?></td>
    <td><?= htmlspecialchars($p['categoria']) ?></td>
    <td><?= $p['quantidade_atual'] ?></td>
    <td><?= htmlspecialchars($p['tensao']) ?></td>
    <td><?= htmlspecialchars($p['dimensoes']) ?></td>
    <td><?= htmlspecialchars($p['resolucao_tela']) ?></td>
    <td><?= htmlspecialchars($p['capacidade_armazenamento']) ?></td>
    <td><?= htmlspecialchars($p['conectividade']) ?></td>
    <td>
        <a href="?editar=<?= $p['id_produto'] ?>">✏️</a>
        <a href="?excluir=<?= $p['id_produto'] ?>" onclick="return confirm('Deseja realmente excluir este produto?')">🗑️</a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="10">Nenhum produto encontrado.</td></tr>
<?php endif; ?>
</table>

<br>
<hr>
<h3><?= $produtoEdit['id_produto'] ? "Editar Produto" : "Adicionar Novo Produto" ?></h3>

<form method="post">
  <input type="hidden" name="id_produto" value="<?= $produtoEdit['id_produto'] ?>">
  <input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($produtoEdit['nome']) ?>" required><br>
  <input type="text" name="descricao" placeholder="Descrição" value="<?= htmlspecialchars($produtoEdit['descricao']) ?>"><br>
  <input type="text" name="categoria" placeholder="Categoria" value="<?= htmlspecialchars($produtoEdit['categoria']) ?>"><br>
  <input type="text" name="tensao" placeholder="Tensão" value="<?= htmlspecialchars($produtoEdit['tensao']) ?>"><br>
  <input type="text" name="dimensoes" placeholder="Dimensões" value="<?= htmlspecialchars($produtoEdit['dimensoes']) ?>"><br>
  <input type="text" name="resolucao_tela" placeholder="Resolução da Tela" value="<?= htmlspecialchars($produtoEdit['resolucao_tela']) ?>"><br>
  <input type="text" name="capacidade_armazenamento" placeholder="Capacidade de Armazenamento" value="<?= htmlspecialchars($produtoEdit['capacidade_armazenamento']) ?>"><br>
  <input type="text" name="conectividade" placeholder="Conectividade" value="<?= htmlspecialchars($produtoEdit['conectividade']) ?>"><br>
  <input type="number" name="minimo" placeholder="Qtd Mínima" value="<?= htmlspecialchars($produtoEdit['quantidade_minima']) ?>" required><br>
  <input type="number" name="quantidade" placeholder="Qtd Atual" value="<?= htmlspecialchars($produtoEdit['quantidade_atual']) ?>" <?= $produtoEdit['id_produto'] ? 'readonly' : '' ?> required><br>
  <button type="submit" name="salvar">Salvar</button>
</form>

<br>
<a href="Painel.php">⬅ Voltar ao menu principal</a>
</div>
</body>
</html>