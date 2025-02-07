-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 27/11/2024 às 03:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `banco_exart`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `postagens`
--

CREATE TABLE `postagens` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `poema` text NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `data_postagem` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aprovado') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `postagens`
--

INSERT INTO `postagens` (`id`, `usuario_id`, `titulo`, `poema`, `imagem`, `data_postagem`, `status`) VALUES
(41, 26, 'Menino e seu Dragão', 'a', 'uploads/pintura da lily.jpg', '2024-11-27 01:48:42', ''),
(42, 26, 'Sra. Ramsay', 'ivana te odeio', 'uploads/pintura da lily.jpg', '2024-11-27 01:49:30', 'aprovado'),
(43, 26, 'O farol', 'aa', 'uploads/images.jpeg', '2024-11-27 01:50:38', 'aprovado'),
(44, 26, 'Menino e seu Dragão', 'aa', 'uploads/dragão.png', '2024-11-27 01:51:11', 'aprovado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `senha` text NOT NULL,
  `created` datetime NOT NULL,
  `imagem_perfil` varchar(255) DEFAULT NULL,
  `admin` tinyint(1) DEFAULT 0,
  `tipo_usuario` enum('admin','comum') DEFAULT 'comum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `created`, `imagem_perfil`, `admin`, `tipo_usuario`) VALUES
(13, 'Matheus', 'matheus@gmail.com', '$2y$10$EDFppYLoyRfBbtmOmcsuPOn4qscf7CHSFR1SWD0QwCZpHIaOQmQK6', '2024-11-12 16:03:44', 'uploads/Clash of Clans Series - Pop Mart.jpeg', 0, 'comum'),
(26, 'adm', 'admin@email.com', '$2y$10$hcNmKxzxi21/i15.XYO.fuL42rdX0cMbvMs1aMPsPonWvGR2nLON.', '2024-11-27 02:14:38', 'uploads/perfil.jpg', 1, 'admin');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `postagens`
--
ALTER TABLE `postagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `postagens`
--
ALTER TABLE `postagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `postagens`
--
ALTER TABLE `postagens`
  ADD CONSTRAINT `postagens_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
