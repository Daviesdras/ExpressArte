-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12/11/2024 às 16:18
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
  `data_postagem` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `postagens`
--

INSERT INTO `postagens` (`id`, `usuario_id`, `titulo`, `poema`, `imagem`, `data_postagem`) VALUES
(36, 13, 'mago', 'aaaaaaaaaaaaaaaaaaaa', 'uploads/Clash of Clans Series - Pop Mart.jpeg', '2024-11-12 15:04:47');

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
  `imagem_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `created`, `imagem_perfil`) VALUES
(5, 'matheus', 'matheus@email.com', '$2y$10$tFhPdiFfv6vL17f.Ng6V2ubk0FajZAO5IZ4KKsu6z2gv9phV5cuYW', '2024-08-14 13:15:37', NULL),
(8, 'joao', 'joao@gmail.com', '$2y$10$D1uQb1DvbflYCl7M2LnuuO2EreyQ5KYOFxv./1fN7HPIMW6Lo5T0S', '2024-09-14 20:05:37', NULL),
(10, 'Matheus', 'preto1@gmail.com', '$2y$10$/N5EFaG9LdAgewdOueLqUubNUFh9GnTQ3AbSPKLROrksXOI.mSsAq', '2024-10-22 23:19:11', NULL),
(11, 'aluno ifma', 'oi@gmail.com', '$2y$10$PgjEX3qOQ5mc2xkQ9HvXQerudNYOOZQO6FwSrhkXd76KpZzPDNWye', '2024-10-31 19:05:34', 'uploads/Clash of Clans Series - Pop Mart.jpeg'),
(12, 'Davi', 'davie@gmail.com', '$2y$10$a6zkO.4J0T5dJRzNsvVEQOQDeQIKBG0mutSMNcF1yjbjvq/ETzc2G', '2024-10-31 19:09:58', 'uploads/Clash Mini❤️⭐🤩.jpeg'),
(13, 'Matheus', 'matheus@gmail.com', '$2y$10$EDFppYLoyRfBbtmOmcsuPOn4qscf7CHSFR1SWD0QwCZpHIaOQmQK6', '2024-11-12 16:03:44', 'uploads/Clash of Clans Series - Pop Mart.jpeg'),
(14, 'negao', 'pinbada@gmail.com', '$2y$10$UBan96Y0NQw/w8P7xVZtNuMcjQcCeD4jxC/N.s.ZcdFwn3LMXRh6S', '2024-11-12 16:10:10', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
