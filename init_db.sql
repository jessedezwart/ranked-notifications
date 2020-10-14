SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `ranked_notifications` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `ranked_notifications`;

CREATE TABLE `summoners` (
  `id` varchar(100) NOT NULL,
  `account_id` varchar(100) NOT NULL,
  `puuid` varchar(100) NOT NULL,
  `name` varchar(16) NOT NULL,
  `region` varchar(4) NOT NULL,
  `rank_solo` varchar(20) NOT NULL,
  `rank_flex` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `summoners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`,`region`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
