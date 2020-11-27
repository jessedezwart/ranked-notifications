SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `rank_history` (
  `id` int(11) NOT NULL,
  `summoner_id` varchar(100) NOT NULL,
  `queue_type` varchar(20) NOT NULL,
  `rank` varchar(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `summoners` (
  `id` varchar(100) NOT NULL,
  `account_id` varchar(100) NOT NULL,
  `puuid` varchar(100) NOT NULL,
  `name` varchar(16) NOT NULL,
  `region` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `rank_history`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `summoners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`,`region`);


ALTER TABLE `rank_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
