-- ================================================
-- SCRIPT DE CRIAÇÃO DO BANCO DE DADOS: prova_saep
-- Sistema: Controle de Estoque Serjão Materiais
-- Tecnologias: PHP, MySQL, HTML, CSS
-- ================================================

-- Remove o banco existente (para recriar do zero)
DROP DATABASE IF EXISTS prova_saep;

-- Cria o banco
CREATE DATABASE prova_saep CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE prova_saep;

-- ================================================
-- TABELA DE USUÁRIOS
-- ================================================
CREATE TABLE usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL
);

-- Usuários iniciais
INSERT INTO usuarios (nome, email, senha) VALUES
('Administrador', 'admin@gmail.com', MD5('12345')),
('João Silva', 'joao@gmail.com', MD5('12345')),
('Maria Souza', 'maria@gmail.com', MD5('12345')),
('Aluno', 'aluno@gmail.com', MD5('123')),
('Sergio Luiz', 'sergio@gmail.com', MD5('123'));

-- ================================================
-- TABELA DE PRODUTOS
-- ================================================
CREATE TABLE produtos (
  id_produto INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT,
  categoria VARCHAR(50),
  tensao VARCHAR(20),
  dimensoes VARCHAR(50),
  resolucao_tela VARCHAR(50),
  capacidade_armazenamento VARCHAR(50),
  conectividade VARCHAR(50),
  quantidade_minima INT DEFAULT 0,
  quantidade_atual INT DEFAULT 0
);

-- Produtos iniciais (opcional)
INSERT INTO produtos (nome, descricao, categoria, tensao, dimensoes, resolucao_tela, capacidade_armazenamento, conectividade, quantidade_minima, quantidade_atual) VALUES
('Smartphone X', 'Smartphone de última geração', 'Eletrônicos', '5V', '150x70x8mm', '1080x2400', '128GB', 'Wi-Fi, Bluetooth', 10, 50),
('Notebook Y', 'Notebook para uso profissional', 'Informática', '110V', '350x250x20mm', '1920x1080', '512GB', 'Wi-Fi', 5, 20),
('TV Z', 'Smart TV 4K', 'Eletrônicos', '220V', '1200x700x50mm', '3840x2160', 'N/A', 'Wi-Fi, HDMI', 2, 10);

-- ================================================
-- TABELA DE MOVIMENTAÇÕES
-- ================================================
CREATE TABLE movimentacoes (
  id_movimentacao INT AUTO_INCREMENT PRIMARY KEY,
  id_produto INT NOT NULL,
  tipo ENUM('entrada', 'saida') NOT NULL,
  quantidade INT NOT NULL,
  data_movimentacao DATE NOT NULL,
  id_usuario INT NOT NULL,
  FOREIGN KEY (id_produto) REFERENCES produtos(id_produto) ON DELETE CASCADE,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Movimentações iniciais (opcional)
INSERT INTO movimentacoes (id_produto, tipo, quantidade, data_movimentacao, id_usuario) VALUES
(1, 'entrada', 20, '2023-01-01', 1),
(2, 'saida', 5, '2023-01-02', 2),
(3, 'entrada', 10, '2023-01-03', 3);

-- ================================================
-- CONSULTAS DE TESTE (opcional)
-- ================================================
-- Listar todos os produtos
SELECT * FROM produtos;

-- Listar histórico de movimentações com nomes
SELECT m.id_movimentacao, p.nome AS produto, m.tipo, m.quantidade, 
       m.data_movimentacao, u.nome AS usuario
FROM movimentacoes m
INNER JOIN produtos p ON m.id_produto = p.id_produto
INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
ORDER BY m.data_movimentacao DESC;