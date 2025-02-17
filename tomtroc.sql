-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : lun. 17 fév. 2025 à 15:46
-- Version du serveur : 10.11.11-MariaDB-ubu2204
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tomtroc`
--

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `is_read_by_user_one` tinyint(1) NOT NULL DEFAULT 0,
  `is_read_by_user_two` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `content`, `sent_at`, `created_at`, `updated_at`, `is_read_by_user_one`, `is_read_by_user_two`) VALUES
(2, 4, 2, 'Salut !', '2025-02-17 14:29:04', '2025-02-17 14:29:04', NULL, 1, 0),
(3, 4, 2, 'Tu vas bien ?', '2025-02-17 14:29:35', '2025-02-17 14:29:35', NULL, 1, 0),
(4, 4, 2, 'Test', '2025-02-17 14:30:14', '2025-02-17 14:30:14', NULL, 1, 0),
(5, 4, 2, 'Test 2', '2025-02-17 14:36:49', '2025-02-17 14:36:49', NULL, 1, 0),
(6, 4, 2, 'Ca marche ?', '2025-02-17 14:42:35', '2025-02-17 14:42:35', NULL, 1, 0),
(7, 4, 1, 'Test', '2025-02-17 15:11:40', '2025-02-17 15:11:40', NULL, 0, 1),
(8, 4, 2, 'Test', '2025-02-17 15:13:57', '2025-02-17 15:13:57', NULL, 1, 0),
(9, 4, 2, 'Test', '2025-02-17 15:16:14', '2025-02-17 15:16:14', NULL, 1, 0),
(10, 4, 2, 'Test 3', '2025-02-17 15:42:55', '2025-02-17 15:42:55', NULL, 1, 0),
(11, 4, 1, 'Test 4', '2025-02-17 15:43:04', '2025-02-17 15:43:04', NULL, 0, 1),
(12, 4, 2, 'Test 5', '2025-02-17 15:43:40', '2025-02-17 15:43:40', NULL, 1, 0);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_messages_conversation` (`conversation_id`),
  ADD KEY `fk_messages_sender` (`sender_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
