-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 20 juin 2025 à 21:33
-- Version du serveur : 8.0.42-0ubuntu0.24.04.1
-- Version de PHP : 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `app-parking`
--

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `send_date` timestamp NOT NULL,
  `status` enum('supprimer','en_cours') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `send_date`, `status`) VALUES
(1, 1, 'ceci est un test', '2025-05-26 14:40:27', 'en_cours'),
(2, 1, 'Nouvelle réservation pour la place n°1 du 2025-05-26T12:12 au 2025-05-26T13:13', '2025-05-26 14:40:27', 'en_cours'),
(3, 16, 'Bienvenue, test ! Votre compte a été créé avec succès.', '2025-05-26 08:15:55', 'en_cours'),
(4, 1, 'Votre réservation n°5 a été annulée avec succès.', '2025-05-26 14:40:27', 'en_cours'),
(5, 20, 'Bienvenue, user ! Votre compte a été créé avec succès.', '2025-06-11 08:04:06', 'supprimer'),
(6, 20, 'Nouvelle réservation pour la place n°1 du 2025-06-17T12:12 au 2025-06-18T12:12', '2025-06-16 07:41:07', 'supprimer'),
(7, 20, 'Nouvelle réservation pour la place n°51 du 2025-06-19T23:23 au 2025-06-20T23:23', '2025-06-19 16:23:47', 'supprimer'),
(8, 20, 'Nouvelle réservation pour la place n°41 du 2025-06-20T12:12 au 2025-06-21T12:12', '2025-06-19 16:42:39', 'supprimer'),
(9, 20, 'Nouvelle réservation pour la place n°54 du 2025-06-21T23:23 au 2025-06-22T23:23', '2025-06-19 16:44:22', 'supprimer'),
(10, 20, 'Nouvelle réservation pour la place n°11 du 2025-06-20T16:33 au 2025-06-21T16:34', '2025-06-20 12:30:31', 'supprimer'),
(11, 20, 'Nouvelle réservation pour la place n°43 du 2025-06-21T21:05 au 2025-06-22T01:05', '2025-06-20 21:05:15', 'en_cours'),
(12, 20, 'Nouvelle réservation pour la place n°49 du 2025-06-22T01:07 au 2025-06-22T02:07', '2025-06-20 21:07:25', 'en_cours'),
(13, 20, 'Nouvelle réservation pour la place n°49 du 2025-06-26T21:13 au 2025-06-29T02:09', '2025-06-20 21:08:56', 'en_cours'),
(14, 20, 'Nouvelle réservation pour la place n°48 du 2025-06-20T23:09 au 2025-06-21T00:09', '2025-06-20 21:09:11', 'en_cours');

-- --------------------------------------------------------

--
-- Structure de la table `parking`
--

CREATE TABLE `parking` (
  `id` int NOT NULL,
  `number_place` int NOT NULL,
  `type_place` enum('moto','voiture','electrique','handicape') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('disponible','indisponible') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `parking`
--

INSERT INTO `parking` (`id`, `number_place`, `type_place`, `status`) VALUES
(1, 1, 'moto', NULL),
(2, 2, 'moto', NULL),
(3, 3, 'moto', NULL),
(4, 4, 'voiture', NULL),
(5, 5, 'voiture', NULL),
(6, 7, 'electrique', NULL),
(7, 8, 'electrique', NULL),
(8, 6, 'voiture', NULL),
(9, 10, 'voiture', NULL),
(10, 11, 'voiture', NULL),
(11, 11, 'voiture', NULL),
(12, 12, 'voiture', NULL),
(13, 13, 'voiture', NULL),
(14, 14, 'voiture', NULL),
(15, 15, 'voiture', NULL),
(16, 16, 'voiture', NULL),
(17, 17, 'voiture', NULL),
(18, 18, 'voiture', NULL),
(19, 19, 'voiture', NULL),
(20, 20, 'voiture', NULL),
(21, 21, 'voiture', NULL),
(22, 22, 'voiture', NULL),
(23, 23, 'voiture', NULL),
(24, 24, 'voiture', NULL),
(25, 25, 'voiture', NULL),
(26, 26, 'voiture', NULL),
(27, 27, 'voiture', NULL),
(28, 28, 'voiture', NULL),
(29, 29, 'voiture', NULL),
(30, 30, 'voiture', NULL),
(31, 31, 'voiture', NULL),
(32, 32, 'voiture', NULL),
(33, 33, 'voiture', NULL),
(34, 34, 'voiture', NULL),
(35, 35, 'voiture', NULL),
(36, 36, 'voiture', NULL),
(37, 37, 'voiture', NULL),
(38, 38, 'voiture', NULL),
(39, 39, 'voiture', NULL),
(40, 40, 'voiture', NULL),
(41, 41, 'moto', NULL),
(42, 42, 'moto', NULL),
(43, 43, 'moto', NULL),
(44, 44, 'electrique', NULL),
(45, 45, 'moto', NULL),
(46, 46, 'electrique', NULL),
(47, 47, 'electrique', NULL),
(48, 48, 'moto', NULL),
(49, 49, 'moto', NULL),
(50, 50, 'moto', NULL),
(51, 51, 'handicape', NULL),
(52, 52, 'handicape', NULL),
(53, 53, 'handicape', NULL),
(54, 54, 'handicape', NULL),
(55, 55, 'handicape', NULL),
(56, 56, 'handicape', NULL),
(57, 57, 'handicape', NULL),
(58, 58, 'handicape', NULL),
(59, 59, 'handicape', NULL),
(60, 60, 'handicape', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `parking_id` int NOT NULL,
  `price` int NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('en_cours','reserver','terminer','attente','annuler') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `parking_id`, `price`, `start_date`, `end_date`, `status`) VALUES
(2, 1, 4, 72, '2025-05-22 12:12:00', '2025-05-23 12:12:00', 'terminer'),
(6, 1, 1, 96, '2025-05-28 12:12:00', '2025-05-30 12:12:00', 'attente'),
(7, 3, 4, 324, '2025-06-26 09:00:00', '2025-06-30 20:17:00', 'attente'),
(8, 1, 1, 6, '2025-05-27 12:12:00', '2025-05-27 14:14:00', 'annuler'),
(9, 1, 1, 4, '2025-05-26 12:12:00', '2025-05-26 13:13:00', 'annuler'),
(10, 20, 1, 48, '2025-06-17 12:12:00', '2025-06-18 12:12:00', 'terminer'),
(11, 20, 51, 0, '2025-06-19 23:23:00', '2025-06-20 23:23:00', 'attente'),
(12, 20, 41, 48, '2025-06-20 12:12:00', '2025-06-21 12:12:00', 'reserver'),
(13, 20, 54, 0, '2025-06-21 23:23:00', '2025-06-22 23:23:00', 'attente'),
(14, 20, 11, 75, '2025-06-20 16:33:00', '2025-06-21 16:34:00', 'reserver'),
(15, 20, 43, 8, '2025-06-21 21:05:00', '2025-06-22 01:05:00', 'attente'),
(16, 20, 49, 2, '2025-06-22 01:07:00', '2025-06-22 02:07:00', 'attente'),
(17, 20, 49, 106, '2025-06-26 21:13:00', '2025-06-29 02:09:00', 'attente'),
(18, 20, 48, 2, '2025-06-20 23:09:00', '2025-06-21 00:09:00', 'attente');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `name` varchar(15) NOT NULL,
  `email` text NOT NULL,
  `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(10) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `registration_date` timestamp NOT NULL,
  `status` enum('active','inactif') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `phone`, `role`, `registration_date`, `status`) VALUES
(1, 'admin', 'test.test@gmail.com', '$2y$10$lYDGN.mYMeaCPxUH0YtvtO2iz2iXzIwO6kUBb32ETZ5gMJKut1AXm', '0101010101', 'admin', '2025-05-22 10:03:08', 'active'),
(2, 'hello', 'hello.hello@gmail.com', '$2y$10$3ZZrRMQYHJBynvPwbXE7Be/xNed4zcwYbGaISKtc.WdKWnvWtmgfu', '0202020202', 'user', '2025-05-22 09:54:02', 'active'),
(3, 'Drugeon', 'julie.dior16@gmail.com', '$2y$10$OSDhrZiLpjkr/vnQv2rJg.GYwaPGNVFA8sYteT14YmH1t8xzfHch6', '0666788990', 'user', '2025-05-22 18:16:00', 'active'),
(4, 'me', 'me.me@gmail.com', '$2y$10$m1nOkxYryL/Ze5QGkj36VuVGbEr17DJdTgC1BpBy2lL0Cq7hGCHpS', '0202020202', 'user', '2025-05-23 10:01:42', 'active'),
(5, 'ke', 'k.k@gmail.com', '$2y$10$e41bJ3mpJjk0T1NUczqwvOAAt0UY5hEiok2N2mwqSxspooNhJUuEu', '0300303030', 'user', '2025-05-23 10:02:53', 'active'),
(6, 'e', 'e.e@gmail.com', '$2y$10$w8jNVlD1P4CXCJVI9Pq7pu5Skguqn15Qczxe4raiyDrwU5Xi1eAv2', '3478374347', 'user', '2025-05-23 10:03:56', 'active'),
(7, 'eee', 'e.a@gmail.com', '$2y$10$2LO/dR16lQTuGYHNvw760e.Lc5E2tjz9LsjLwncRaKNAWGfHTMMIq', '3473947938', 'user', '2025-05-23 10:05:54', 'active'),
(13, 'tere', 'teete.e@gmail.com', '$2y$10$8.nulikMF.WF5jyxM.Bc2Oqx9PbAw5S09Bct2ZNFeJgzgbYzL05ke', '3847329847', 'user', '2025-05-23 12:00:48', 'inactif'),
(14, 'test', 'test.ger@gmail.com', '$2y$10$ZRgAgXPTXwqDqJgKzXOSfurDySlflkoTk2wSuBpDUK8gQ3u8yjWOq', '2231231231', 'user', '2025-05-26 06:12:14', 'active'),
(15, 'test', 'gegegegt.ger@gmail.com', '$2y$10$M35rXfMiz.aszSYsyKVQOOFnCBbSMsVA8Bzgx2uRBbbs4E022ABL6', '2231231231', 'user', '2025-05-26 06:12:32', 'active'),
(16, 'test', 'bryan.alex@gmail.com', '$2y$10$ZH.KnyJlYGIEXwGx7PJ7texg/JsehH6RneGrgFJRc5HTAz9ZuVI3i', '2231231231', 'user', '2025-05-26 06:15:55', 'inactif'),
(17, 'michel', 'michel.michel@gmail.com', '$2y$10$a/aUEtfVhEH9zfCPgtf8auu8tFCsYe2sHZM7Of.cX7k9wkhp2.zWe', '1212121212', 'admin', '2025-05-28 07:41:20', 'active'),
(18, 'griughrugh', 'euoiejfui.fef@gmail.com', '$2y$10$NUrOEOKn8T1cPdglbhelDu74UaunfaWY9onVNvoPWD50GJ9YEgUlK', '2478434834', 'user', '2025-05-28 08:09:01', 'active'),
(19, 'Jean', 'jean.jean@gmail.com', '$2y$10$hUqWaDyMOdddpiJX6D7b8.2mbUmJDjW4uB6Wv7D5jZ7TJA1dwRblm', '2832843843', 'user', '2025-06-03 14:30:26', 'active'),
(20, 'user', 'user.user@gmail.com', '$2y$10$ssSBIwop/cERccC3kmdEveOL2XdunC9RWZfWZJ.ptr3EaTML9smiW', '3434343434', 'user', '2025-06-11 06:04:05', 'active');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `parking`
--
ALTER TABLE `parking`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parking_id` (`parking_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `parking`
--
ALTER TABLE `parking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`parking_id`) REFERENCES `parking` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
