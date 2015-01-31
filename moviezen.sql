-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Sam 31 Janvier 2015 à 14:12
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
  PRIMARY KEY (`identifiant`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `admin`
--

INSERT INTO `admin` (`identifiant`, `mdp`) VALUES
('Francois', '$2y$10$qff4SUmbsjqaY10FiZtOpeO071M5xiSL6Ae7gNZDSFI14fhRpITHa'),
('Turing', '$2y$10$Vji7Q8AI7cIZZCeJ4uFbV.DWCte6HUUNzzpWYwqADs1ewsi8/GpWu');

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
('qcfdsgsg', 'dgdgfdfg', '0605080908', 'essai@isen-bretagne.fr', 'CSI3'),
('aevhsqdnb', 'qkfbzbvaq ', '0600000000', 'f@isen-bretagne.fr', 'CSI3'),
('qsejhfbh', 'fqsjbfsbhvbq', '', 'f@ols.fr', 'Cdnsqjhbvc'),
('Toique', 'dsqijfdsq', '0205060809', 'fanch@isen.fr', 'CIR3'),
('Toque', 'Franc', '', 'fanto@is.fr', 'CSI3'),
('eqfdsfsf', 'sdfsdf', '0605050505', 'fq@isen-bretagne.fr', 'CSOI3'),
('sefdsf', 'sdffz', '0505050505', 'ft@isen-bretagne.fr', 'ITII2'),
('TOQUe', 'dqsjhfd', '', 'fto@is.fr', 'CIR3'),
('dswf;l', 'ds,nqjfnsdz', '', 'ftoque@is.fr', 'csdfsc'),
('xfgscv', 'svsvsdv', '0505050505', 'ftoque@isen-bretagne.fr', 'CSI3'),
('ouezfbhjvb', 'fsjdhgfvsddkhbv', '', 'ftoque@isen.fr', 'sdkvnhsdqhvc'),
('seqfsdf', 'sdfsdf', '0605080905', 'ftoque@laposte.net', 'CSI3'),
('gaga', 'gaga', '0605080908', 'ga@isen-bretagne.fr', 'CSI'),
('efndsfnb', 'fdshbfdszbh', '0205050555', 'isen@isen-bretagne.fr', 'CIR2'),
('qscfq', 'sfdsfsdf', '0600000000', 'sd@isen-bretagne.fr', 'CSI3'),
('adsdqfsf', 'sdfsfd', '0550500606', 'to@isen-bretagne.fr', 'CSI3'),
('qsfdsfdsf', 'dsfsdfd', '0505050505', 'ty@isen-bretagne.fr', 'ITII');

-- --------------------------------------------------------

--
-- Structure de la table `inscrits_lots`
--

CREATE TABLE IF NOT EXISTS `inscrits_lots` (
  `inscrit_mail` varchar(255) COLLATE utf8_bin NOT NULL,
  `lots` varchar(2) COLLATE utf8_bin NOT NULL,
  `date_emprunt` datetime NOT NULL,
  `date_retour` datetime NOT NULL,
  KEY `inscri` (`inscrit_mail`),
  KEY `lot` (`lots`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `inscrits_lots`
--

INSERT INTO `inscrits_lots` (`inscrit_mail`, `lots`, `date_emprunt`, `date_retour`) VALUES
('ftoque@isen-bretagne.fr', 'F', '1998-05-05 00:00:00', '0000-00-00 00:00:00'),
('ftoque@isen-bretagne.fr', 'D', '1998-05-05 00:00:00', '0000-00-00 00:00:00'),
('to@isen-bretagne.fr', 'F', '2001-05-06 00:00:00', '2015-05-06 00:00:00'),
('ftoque@isen-bretagne.fr', 'F', '2015-01-16 00:00:00', '2015-01-09 00:00:00'),
('ftoque@isen-bretagne.fr', 'D', '2015-01-16 00:00:00', '2015-01-09 00:00:00'),
('f@isen-bretagne.fr', 'F', '2015-01-30 00:00:00', '2015-01-31 00:00:00'),
('sd@isen-bretagne.fr', 'D', '2015-01-31 00:00:00', '2015-01-31 00:00:00'),
('sd@isen-bretagne.fr', 'F', '2015-01-31 00:00:00', '2015-01-31 00:00:00'),
('ft@isen-bretagne.fr', 'F', '2015-01-31 02:01:21', '2015-01-31 01:01:23'),
('ft@isen-bretagne.fr', 'D', '2015-01-31 02:01:21', '2015-01-31 01:01:23'),
('f@isen-bretagne.fr', 'F', '1960-05-06 00:05:00', '1980-05-06 00:05:00'),
('to@isen-bretagne.fr', 'D', '2015-02-19 09:02:03', '2015-02-27 09:02:05'),
('ga@isen-bretagne.fr', 'B', '2015-02-11 11:02:00', '2015-02-27 11:02:55');

-- --------------------------------------------------------

--
-- Structure de la table `lots`
--

CREATE TABLE IF NOT EXISTS `lots` (
  `id` varchar(2) COLLATE utf8_bin NOT NULL,
  `composition` text COLLATE utf8_bin NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `image` text COLLATE utf8_bin NOT NULL,
  `caution` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `lots`
--

INSERT INTO `lots` (`id`, `composition`, `disponible`, `image`, `caution`) VALUES
('B', 'Essai', 0, '../Images/1768be5188fa2306c327c3319d6fedd7.jpg', 125),
('C', 'qedfsfsdf', 1, '', 100),
('CC', 'qdsfdfsgdfgfd', 1, '../Images/6065f78cc6338a7b3e0fc4683f937657.jpg', 0),
('D', 'Micro', 1, '', 0),
('F', 'Camera essai222', 1, '', 0),
('K', 'LOL', 1, '../Images/c0b3f30a21fe56d616215407c561db39.jpg', 200);

-- --------------------------------------------------------

--
-- Structure de la table `projections`
--

CREATE TABLE IF NOT EXISTS `projections` (
  `nom` varchar(255) COLLATE utf8_bin NOT NULL,
  `date_release` date NOT NULL,
  `date_projection` date NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `commentaires` text COLLATE utf8_bin NOT NULL,
  `affiche` text COLLATE utf8_bin NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `projections`
--

INSERT INTO `projections` (`nom`, `date_release`, `date_projection`, `description`, `commentaires`, `affiche`, `active`) VALUES
('FAFA', '2015-01-31', '2015-02-12', 'FAFA', 'qddq', '../Images/ab839057d16a858b76e202be4b7082f6.jpg', 1),
('Foxcatcherqedfsdf', '2015-01-14', '2015-01-16', 'TEST', 'qfdsfgdsfd', '../Images/ed94783fc491117c9947c807ce66ab19.jpg', 0),
('LE magicien d\\''OSSSSSSSSSS', '2015-01-31', '2015-03-13', 'Ceci est un essai particulièrement réussi', '', '../Images/3e7ad375b37dc29ca7f5035191c62127.jpg', 0),
('TESTETS', '2015-01-15', '2015-01-27', 'ESSSSSSSSSSSSSSSSSSSSAI', '', '', 0),
('TTTTTTTTTTTTTTTTTTTTTTTT', '0000-00-00', '0000-00-00', 'TEEEEEEEEEEEEEEEEEE', '', '', 0);

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
('fto@is.fr', 'Foxcatcherqedfsdf'),
('fanto@is.fr', 'Foxcatcherqedfsdf');

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
