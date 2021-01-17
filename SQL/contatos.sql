-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17-Jan-2021 às 21:05
-- Versão do servidor: 10.4.16-MariaDB
-- versão do PHP: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `contatos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cadastros`
--

CREATE TABLE `cadastros` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Extraindo dados da tabela `cadastros`
--

INSERT INTO `cadastros` (`id`, `nome`, `telefone`, `email`) VALUES
(19, 'Aldemar Vigario', '(61) 95745-4716', 'aldemarvigario@mail.com'),
(20, 'Samuel Blaustein', '(44) 96990-3703', 'samuelblaustein@mail.com'),
(22, 'Seu Mazarito', '(70) 97803-0776', 'seumazarito@mail.com'),
(24, 'Bertoldo Brecha', '(60) 70971-2931', 'BertoldoBrecha@mail.com'),
(29, 'Baltazar da Rocha', '(51) 43886-3594', 'seubaltazar@mail.com'),
(30, 'Seu Boneco', '(81) 17956-6340', 'seuboneco@mail.com'),
(31, 'Ptolomeu', '(53) 53912-7896', 'ptolomeu@mail.com'),
(32, 'Professor Raimundo	', '(45) 33137-6282', 'professorraimundo@mail.com'),
(33, 'João Bacurinho	', '(45) 82335-0929', 'joaobicudinho@mail.com'),
(34, 'Dona Bela', '(71) 88562-4544', 'donabela@mail.com'),
(35, 'Célia Caridosa de Melo	', '(70) 73889-4776', 'celiacaridosademelo@mail.com'),
(36, 'Dona Clara', '(55) 34817-8094', 'donaclara@mail.com'),
(37, 'Galeão Cumbica', '(22) 39684-0133', 'galeaocumbica@mail.com'),
(38, 'Agnaldo Peixoto', '(65) 82148-5400', 'agnaldopeixoto@mail.com'),
(39, 'Armando Volta', '(26) 18922-2998', 'armandovolta@mail.com'),
(40, 'Flora Própolis', '(39) 76874-9126', 'florapropolis@mail.com'),
(41, 'Maria Bonita', '(38) 57711-5047', 'mariabonita@mail.com'),
(42, 'Paulo Cintura', '(29) 37361-7868', 'paulocintura@mail.com'),
(43, 'Nerso da Capitinga', '(61) 55685-5422', 'nersodacapitinga@mail.com'),
(44, 'Seu Mazarito', '(10) 79281-4812', 'mazarito@mail.com'),
(45, 'Manuela DAlém-mar', '(19) 86158-2376', 'manueladalemmar@mail.com'),
(46, 'Sócrates Homem de Mello', '(73) 51856-8270', 'socrateshomemdemello@mail.com');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cadastros`
--
ALTER TABLE `cadastros`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cadastros`
--
ALTER TABLE `cadastros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
