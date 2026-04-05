-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 10:18 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `immo_connect`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `insert_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `title`, `description`, `created_at`, `updated_at`, `deleted`, `deleted_at`, `insert_by_id`) VALUES
(31, 'Appartement', 'Locations et ventes d appartements en zone urbaine.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL),
(32, 'Maison', 'Maisons, villas et residences individuelles.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL),
(33, 'Terrain', 'Terrains a vendre et parcelles viabilisees.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL),
(34, 'Boutique', 'Locaux commerciaux et boutiques.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL),
(35, 'Hotel', 'Hotels, residences et hebergements.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL),
(36, 'Coworking', 'Espaces de coworking et bureaux equipes.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260404045500', '2026-04-04 05:05:28', 420),
('DoctrineMigrations\\Version20260404101500', '2026-04-04 05:05:28', 63);

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `type_file` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `property_id` int(11) NOT NULL,
  `insert_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`id`, `file_url`, `type_file`, `created_at`, `updated_at`, `deleted`, `deleted_at`, `property_id`, `insert_by_id`) VALUES
(23, 'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 14, 4),
(24, 'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=900&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 14, 4),
(25, 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 15, 4),
(26, 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 16, 4),
(27, 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 17, 4),
(28, 'https://images.unsplash.com/photo-1517502884422-41eaead166d4?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 18, 4),
(29, 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80', 'image', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 19, 4);

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `surface` double NOT NULL,
  `is_available` tinyint(4) DEFAULT NULL,
  `rooms_number` int(11) NOT NULL,
  `description` longtext NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `insert_by_id` int(11) DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `operation` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`id`, `title`, `price`, `surface`, `is_available`, `rooms_number`, `description`, `created_at`, `updated_at`, `deleted`, `deleted_at`, `owner_id`, `category_id`, `insert_by_id`, `location`, `operation`) VALUES
(14, 'Villa premium avec piscine et rooftop', 285000000, 420, 1, 6, 'Grande villa familiale avec jardin, piscine, dependance et espace de teletravail. Ideal pour achat patrimonial, location premium ou revente a forte valeur.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 32, 4, 'Riviera Golf, Cocody', 'A vendre'),
(15, 'Appartement meuble proche plateau', 950000, 95, 1, 3, 'Appartement moderne, meuble et securise avec acces rapide au Plateau. Convient aux cadres et expatries.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 31, 4, 'Zone 4, Marcory', 'A vendre'),
(16, 'Terrain titre a fort potentiel de plus-value', 42000000, 600, 1, 1, 'Terrain plat avec acces facile, dossier complet et possibilite d accompagnement pour le titre foncier.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 33, 4, 'Bingerville, Abidjan', 'A vendre'),
(17, 'Residence hoteliere pour sejours business', 165000, 48, 1, 2, 'Suite hoteliere avec services inclus, salle de sport, wifi et restauration.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 35, 4, 'Plateau, Abidjan', 'A vendre'),
(18, 'Boutique premium pour commerce de proximite', 1250000, 110, 1, 2, 'Local commercial bien place, grande vitrine, espace de stockage et zone tres passante.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 34, 4, 'Deux-Plateaux, Cocody', 'A vendre'),
(19, 'Plateau de coworking equipe et modulable', 350000, 65, 1, 2, 'Bureaux partages avec accueil, salle de reunion, internet haut debit et formule flexible pour entrepreneurs.', '2026-04-04 04:30:13', '2026-04-04 04:30:13', 0, NULL, 4, 36, 4, 'Marcory Biery, Abidjan', 'A vendre'),
(20, 'Appartement meuble plateau 2222', 20000, 178, 0, 4, 'Appartement moderne, meuble et securise avec acces rapide au Plateau. Convient aux cadres et expatries.', '2026-04-04 05:14:53', '2026-04-04 05:14:53', 0, NULL, 4, 34, 4, 'Riveria OOug, Cocody Renemn', 'A louer');

-- --------------------------------------------------------

--
-- Table structure for table `type_property`
--

CREATE TABLE `type_property` (
  `id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `insert_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type_type`
--

CREATE TABLE `type_type` (
  `id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `insert_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `insert_by_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `name`, `phone`, `first_name`, `is_active`, `created_at`, `updated_at`, `deleted`, `deleted_at`, `insert_by_id`) VALUES
(4, 'superadmin@immoconnect.local', '[\"ROLE_SUPER_ADMIN\",\"ROLE_ADMIN\",\"ROLE_OWNER\"]', '$2y$13$Z7ezgYKfdN8eIq8VWSH4N.fJY0gLTsr2XzVqzA3nMmRP95TW8Xpfq', 'Admin', '+2250102030405', 'Super', 1, '2026-04-04 04:30:12', '2026-04-04 04:30:12', 0, NULL, NULL),
(5, 'setondji-fabrice.agbo@etudiant.univ-rennes.fr', '[\"ROLE_TENANT\"]', '$2y$13$31QTkIaAVWF15M5pTDgN4utgCxWyAB4BwQtk1CxVkGniOs2jhMKfS', 'AGBO', '+33772880534', 'Sètondji Fabrice', 1, '2026-04-04 04:36:53', '2026-04-04 04:36:53', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_64C19C1A049D856` (`insert_by_id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10C549213EC` (`property_id`),
  ADD KEY `IDX_6A2CA10CA049D856` (`insert_by_id`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8BF21CDE12469DE2` (`category_id`),
  ADD KEY `IDX_8BF21CDEA049D856` (`insert_by_id`),
  ADD KEY `IDX_8BF21CDE7E3C61F9` (`owner_id`);

--
-- Indexes for table `type_property`
--
ALTER TABLE `type_property`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_460D3B22A049D856` (`insert_by_id`);

--
-- Indexes for table `type_type`
--
ALTER TABLE `type_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5BD19221727ACA70` (`parent_id`),
  ADD KEY `IDX_5BD19221A049D856` (`insert_by_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD KEY `IDX_8D93D649A049D856` (`insert_by_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `type_property`
--
ALTER TABLE `type_property`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `type_type`
--
ALTER TABLE `type_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category`
--
ALTER TABLE `category`
  ADD CONSTRAINT `FK_64C19C1A049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10C549213EC` FOREIGN KEY (`property_id`) REFERENCES `property` (`id`),
  ADD CONSTRAINT `FK_6A2CA10CA049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `property`
--
ALTER TABLE `property`
  ADD CONSTRAINT `FK_8BF21CDE12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_8BF21CDE7E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_8BF21CDEA049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `type_property`
--
ALTER TABLE `type_property`
  ADD CONSTRAINT `FK_460D3B22A049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `type_type`
--
ALTER TABLE `type_type`
  ADD CONSTRAINT `FK_5BD19221727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `type_type` (`id`),
  ADD CONSTRAINT `FK_5BD19221A049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649A049D856` FOREIGN KEY (`insert_by_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
