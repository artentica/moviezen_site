-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 20 Février 2015 à 17:41
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `moviezen`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `identifiant` text COLLATE utf8_bin NOT NULL,
  `mdp` text COLLATE utf8_bin NOT NULL,
  `mail` text COLLATE utf8_bin NOT NULL,
  `responsable_emprunt` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`identifiant`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `admin`
--

INSERT INTO `admin` (`identifiant`, `mdp`, `mail`, `responsable_emprunt`) VALUES
('Artentica', '$2y$10$ZD1R1mcxcDQguhZR8FBq6efRqwhTo4aj3.MvG58D42g2Mx35zl1ne', 'yolo@penis.com', 1),
('Fanch', '$2y$10$kNl2Zmst6UtTo2Bzytz11eEQZGNWGwuQReCnPsA1uXKSINlCiW9jS', 'fanch.toquer@laposte.net', 0),
('test', '$2y$10$CykxRBL7DBJB4RNmWIW2V.fdDXV9xWKQXgpjryKS9rL4AHjsFI02m', 'test@gmail.com', 0);

-- --------------------------------------------------------

--
-- Structure de la table `desinscription`
--

CREATE TABLE IF NOT EXISTS `desinscription` (
  `mail`  varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `désinscription_code` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `raison` text CHARACTER SET utf8 COLLATE utf8_bin,
  `done` tinyint(1) NOT NULL DEFAULT '0',
  `projection`  varchar(255) NOT NULL,
   PRIMARY KEY (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `dispo`
--

CREATE TABLE IF NOT EXISTS `dispo` (
  `jour` int(11) NOT NULL AUTO_INCREMENT,
  `B` tinyint(1) NOT NULL DEFAULT '1',
  `A` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`jour`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=367 ;

--
-- Contenu de la table `dispo`
--

INSERT INTO `dispo` (`jour`, `B`, `A`) VALUES
(1, 1, 1),
(2, 1, 1),
(3, 1, 1),
(4, 1, 1),
(5, 1, 1),
(6, 1, 1),
(7, 1, 1),
(8, 1, 1),
(9, 1, 1),
(10, 1, 1),
(11, 1, 1),
(12, 1, 1),
(13, 1, 1),
(14, 1, 1),
(15, 1, 1),
(16, 1, 1),
(17, 1, 1),
(18, 1, 1),
(19, 1, 1),
(20, 1, 1),
(21, 1, 1),
(22, 1, 1),
(23, 1, 1),
(24, 1, 1),
(25, 1, 1),
(26, 1, 1),
(27, 1, 1),
(28, 1, 1),
(29, 1, 1),
(30, 1, 1),
(31, 1, 1),
(32, 1, 1),
(33, 1, 1),
(34, 1, 1),
(35, 1, 1),
(36, 1, 1),
(37, 1, 1),
(38, 1, 1),
(39, 1, 1),
(40, 1, 1),
(41, 1, 1),
(42, 1, 1),
(43, 1, 1),
(44, 1, 1),
(45, 1, 1),
(46, 1, 1),
(47, 1, 1),
(48, 1, 1),
(49, 1, 1),
(50, 1, 1),
(51, 1, 1),
(52, 1, 1),
(53, 1, 1),
(54, 1, 1),
(55, 1, 1),
(56, 1, 1),
(57, 1, 1),
(58, 1, 1),
(59, 1, 1),
(60, 1, 1),
(61, 1, 1),
(62, 1, 1),
(63, 1, 1),
(64, 1, 1),
(65, 1, 1),
(66, 1, 1),
(67, 1, 1),
(68, 1, 1),
(69, 1, 1),
(70, 1, 1),
(71, 1, 1),
(72, 1, 1),
(73, 1, 1),
(74, 1, 1),
(75, 1, 1),
(76, 1, 1),
(77, 1, 1),
(78, 1, 1),
(79, 1, 1),
(80, 1, 1),
(81, 1, 1),
(82, 1, 1),
(83, 1, 1),
(84, 1, 1),
(85, 1, 1),
(86, 1, 1),
(87, 1, 1),
(88, 1, 1),
(89, 1, 1),
(90, 1, 1),
(91, 1, 1),
(92, 1, 1),
(93, 1, 1),
(94, 1, 1),
(95, 1, 1),
(96, 1, 1),
(97, 1, 1),
(98, 1, 1),
(99, 1, 1),
(100, 1, 1),
(101, 1, 1),
(102, 1, 1),
(103, 1, 1),
(104, 1, 1),
(105, 1, 1),
(106, 1, 1),
(107, 1, 1),
(108, 1, 1),
(109, 1, 1),
(110, 1, 1),
(111, 1, 1),
(112, 1, 1),
(113, 1, 1),
(114, 1, 1),
(115, 1, 1),
(116, 1, 1),
(117, 1, 1),
(118, 1, 1),
(119, 1, 1),
(120, 1, 1),
(121, 1, 1),
(122, 1, 1),
(123, 1, 1),
(124, 1, 1),
(125, 1, 1),
(126, 1, 1),
(127, 1, 1),
(128, 1, 1),
(129, 1, 1),
(130, 1, 1),
(131, 1, 1),
(132, 1, 1),
(133, 1, 1),
(134, 1, 1),
(135, 1, 1),
(136, 1, 1),
(137, 1, 1),
(138, 1, 1),
(139, 1, 1),
(140, 1, 1),
(141, 1, 1),
(142, 1, 1),
(143, 1, 1),
(144, 1, 1),
(145, 1, 1),
(146, 1, 1),
(147, 1, 1),
(148, 1, 1),
(149, 1, 1),
(150, 1, 1),
(151, 1, 1),
(152, 1, 1),
(153, 1, 1),
(154, 1, 1),
(155, 1, 1),
(156, 1, 1),
(157, 1, 1),
(158, 1, 1),
(159, 1, 1),
(160, 1, 1),
(161, 1, 1),
(162, 1, 1),
(163, 1, 1),
(164, 1, 1),
(165, 1, 1),
(166, 1, 1),
(167, 1, 1),
(168, 1, 1),
(169, 1, 1),
(170, 1, 1),
(171, 1, 1),
(172, 1, 1),
(173, 1, 1),
(174, 1, 1),
(175, 1, 1),
(176, 1, 1),
(177, 1, 1),
(178, 1, 1),
(179, 1, 1),
(180, 1, 1),
(181, 1, 1),
(182, 1, 1),
(183, 1, 1),
(184, 1, 1),
(185, 1, 1),
(186, 1, 1),
(187, 1, 1),
(188, 1, 1),
(189, 1, 1),
(190, 1, 1),
(191, 1, 1),
(192, 1, 1),
(193, 1, 1),
(194, 1, 1),
(195, 1, 1),
(196, 1, 1),
(197, 1, 1),
(198, 1, 1),
(199, 1, 1),
(200, 1, 1),
(201, 1, 1),
(202, 1, 1),
(203, 1, 1),
(204, 1, 1),
(205, 1, 1),
(206, 1, 1),
(207, 1, 1),
(208, 1, 1),
(209, 1, 1),
(210, 1, 1),
(211, 1, 1),
(212, 1, 1),
(213, 1, 1),
(214, 1, 1),
(215, 1, 1),
(216, 1, 1),
(217, 1, 1),
(218, 1, 1),
(219, 1, 1),
(220, 1, 1),
(221, 1, 1),
(222, 1, 1),
(223, 1, 1),
(224, 1, 1),
(225, 1, 1),
(226, 1, 1),
(227, 1, 1),
(228, 1, 1),
(229, 1, 1),
(230, 1, 1),
(231, 1, 1),
(232, 1, 1),
(233, 1, 1),
(234, 1, 1),
(235, 1, 1),
(236, 1, 1),
(237, 1, 1),
(238, 1, 1),
(239, 1, 1),
(240, 1, 1),
(241, 1, 1),
(242, 1, 1),
(243, 1, 1),
(244, 1, 1),
(245, 1, 1),
(246, 1, 1),
(247, 1, 1),
(248, 1, 1),
(249, 1, 1),
(250, 1, 1),
(251, 1, 1),
(252, 1, 1),
(253, 1, 1),
(254, 1, 1),
(255, 1, 1),
(256, 1, 1),
(257, 1, 1),
(258, 1, 1),
(259, 1, 1),
(260, 1, 1),
(261, 1, 1),
(262, 1, 1),
(263, 1, 1),
(264, 1, 1),
(265, 1, 1),
(266, 1, 1),
(267, 1, 1),
(268, 1, 1),
(269, 1, 1),
(270, 1, 1),
(271, 1, 1),
(272, 1, 1),
(273, 1, 1),
(274, 1, 1),
(275, 1, 1),
(276, 1, 1),
(277, 1, 1),
(278, 1, 1),
(279, 1, 1),
(280, 1, 1),
(281, 1, 1),
(282, 1, 1),
(283, 1, 1),
(284, 1, 1),
(285, 1, 1),
(286, 1, 1),
(287, 1, 1),
(288, 1, 1),
(289, 1, 1),
(290, 1, 1),
(291, 1, 1),
(292, 1, 1),
(293, 1, 1),
(294, 1, 1),
(295, 1, 1),
(296, 1, 1),
(297, 1, 1),
(298, 1, 1),
(299, 1, 1),
(300, 1, 1),
(301, 1, 1),
(302, 1, 1),
(303, 1, 1),
(304, 1, 1),
(305, 1, 1),
(306, 1, 1),
(307, 1, 1),
(308, 1, 1),
(309, 1, 1),
(310, 1, 1),
(311, 1, 1),
(312, 1, 1),
(313, 1, 1),
(314, 1, 1),
(315, 1, 1),
(316, 1, 1),
(317, 1, 1),
(318, 1, 1),
(319, 1, 1),
(320, 1, 1),
(321, 1, 1),
(322, 1, 1),
(323, 1, 1),
(324, 1, 1),
(325, 1, 1),
(326, 1, 1),
(327, 1, 1),
(328, 1, 1),
(329, 1, 1),
(330, 1, 1),
(331, 1, 1),
(332, 1, 1),
(333, 1, 1),
(334, 1, 1),
(335, 1, 1),
(336, 1, 1),
(337, 1, 1),
(338, 1, 1),
(339, 1, 1),
(340, 1, 1),
(341, 1, 1),
(342, 1, 1),
(343, 1, 1),
(344, 1, 1),
(345, 1, 1),
(346, 1, 1),
(347, 1, 1),
(348, 1, 1),
(349, 1, 1),
(350, 1, 1),
(351, 1, 1),
(352, 1, 1),
(353, 1, 1),
(354, 1, 1),
(355, 1, 1),
(356, 1, 1),
(357, 1, 1),
(358, 1, 1),
(359, 1, 1),
(360, 1, 1),
(361, 1, 1),
(362, 1, 1),
(363, 1, 1),
(364, 1, 1),
(365, 1, 1),
(366, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `inscrits`
--

CREATE TABLE IF NOT EXISTS `inscrits` (
  `nom` text COLLATE utf8_bin NOT NULL,
  `prenom` text COLLATE utf8_bin NOT NULL,
  `tel` varchar(10) COLLATE utf8_bin NOT NULL,
  `mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `classe` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `inscrits`
--

INSERT INTO `inscrits` (`nom`, `prenom`, `tel`, `mail`, `classe`) VALUES
('ef', 'e', '', 'e.*@e.e', 'CIR2'),
('aze', 'qsd', '', 'e@xn--lzg.fr', 'CIR1'),
('e', 'ez', '', 'er@re.Vrf', 'BTSPREPA1'),
('knkjn', 'kjnkjn', '', 'jhbfd@hgv.fr', 'CIR2'),
('f', 'f', '', 'jn@hbf.fr', 'CSI1'),
('jhbjhbbhjbk', 'jk', '', 'jngv@hgv.fr', 'BTSPREPA2'),
('Vincent', 'kjn', '', 'kjgv@jhgfc.fr', 'CSI1'),
('Artentica', 'kj', '', 'kjhn@hkgv.hjgcv', 'CSI3'),
('r', 'r', '', 'rrrr@jhbr.fr', 'CSI1'),
('dytu', 'u', '', 'u@g.fr', 'ITII5'),
('wfdg', 'fwgwf', '', 'wfg@gfds.Fr', 'CIR2');

-- --------------------------------------------------------

--
-- Structure de la table `inscrits_lots`
--

CREATE TABLE IF NOT EXISTS `inscrits_lots` (
  `inscrit_mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `lots` varchar(2) COLLATE utf8_bin NOT NULL,
  `date_emprunt` double NOT NULL,
  `date_retour` double NOT NULL,
  KEY `inscri` (`inscrit_mail`),
  KEY `lot` (`lots`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `lots`
--

CREATE TABLE IF NOT EXISTS `lots` (
  `id` varchar(2) COLLATE utf8_bin NOT NULL,
  `composition` text COLLATE utf8_bin NOT NULL,
  `image` text COLLATE utf8_bin NOT NULL,
  `caution` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `projections`
--

CREATE TABLE IF NOT EXISTS `projections` (
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `date_release` double NOT NULL,
  `date_projection` double NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `commentaires` text COLLATE utf8_bin NOT NULL,
  `affiche` text COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `back_affiche` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `projections`
--

INSERT INTO `projections` (`nom`, `date_release`, `date_projection`, `description`, `commentaires`, `affiche`, `active`, `back_affiche`) VALUES
('qs', 0, 0, '    s', 's', '../Images/affiche/34daa7ea2e5ff2e406e65b191431c025.jpg', 0, '../Images/affiche/764110934d57feaa6e63932db6ea1f19.jpg'),
('qsd', 1424353800, 1424872200, 'qsd', 'qsd', '../Images/affiche/98a6f3689f0ddfa7c0a37f27417f4a9e.jpg', 1, '../Images/affiche/98a6f3689f0ddfa7c0a37f27417f4a9e.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `projections_inscrits`
--

CREATE TABLE IF NOT EXISTS `projections_inscrits` (
  `inscrit_mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `projection` varchar(255) COLLATE utf8_bin NOT NULL,
  KEY `inscrit` (`inscrit_mail`),
  KEY `projection` (`projection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `projections_inscrits`
--

INSERT INTO `projections_inscrits` (`inscrit_mail`, `projection`) VALUES
('jhbfd@hgv.fr', 'qsd');

-- --------------------------------------------------------

--
-- Structure de la table `promotion`
--

CREATE TABLE IF NOT EXISTS `promotion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `promotion` (`promotion`),
  KEY `promotion_index` (`promotion`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=14 ;

--
-- Contenu de la table `promotion`
--

INSERT INTO `promotion` (`id`, `promotion`) VALUES
(3, 'BTSPREPA1'),
(6, 'BTSPREPA2'),
(2, 'CIR1'),
(5, 'CIR2'),
(8, 'CIR3'),
(1, 'CSI1'),
(4, 'CSI2'),
(7, 'CSI3'),
(9, 'ITII3'),
(11, 'ITII4'),
(13, 'ITII5'),
(10, 'M1'),
(12, 'M2');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `inscrits_lots`
--
ALTER TABLE `inscrits_lots`
  ADD CONSTRAINT `inscr_mail_constr` FOREIGN KEY (`inscrit_mail`) REFERENCES `inscrits` (`mail`),
  ADD CONSTRAINT `lots_constr` FOREIGN KEY (`lots`) REFERENCES `lots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `projections_inscrits`
--
ALTER TABLE `projections_inscrits`
  ADD CONSTRAINT `inscr_constr` FOREIGN KEY (`inscrit_mail`) REFERENCES `inscrits` (`mail`),
  ADD CONSTRAINT `proj_constr` FOREIGN KEY (`projection`) REFERENCES `projections` (`nom`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;