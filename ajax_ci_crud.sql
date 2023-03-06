-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 06 Mar 2023, 10:41:15
-- Sunucu sürümü: 10.4.27-MariaDB
-- PHP Sürümü: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ajax_ci_crud`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Customer`
--

CREATE TABLE `Customer` (
  `id` int(11) UNSIGNED NOT NULL,
  `Name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Surname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Email` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `Customer`
--

INSERT INTO `Customer` (`id`, `Name`, `Surname`, `Phone`, `Email`) VALUES
(6, 'Ahmet', 'Mehmet', '05555555555', 'test@test.com');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Product`
--

CREATE TABLE `Product` (
  `id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Balance` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `Product`
--

INSERT INTO `Product` (`id`, `Name`, `Balance`) VALUES
(1, 'ÜLKER ÇİKOLATALI GOFRET', 0),
(2, 'BROWNİ', 10),
(3, 'TADELLE', -2),
(23, 'İÇİM SÜT', 15);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `Transaction`
--

CREATE TABLE `Transaction` (
  `id` int(11) NOT NULL,
  `DateTime` datetime DEFAULT current_timestamp(),
  `CustomerId` int(11) NOT NULL,
  `ProductId` int(11) NOT NULL,
  `Direction` tinyint(4) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `Transaction`
--

INSERT INTO `Transaction` (`id`, `DateTime`, `CustomerId`, `ProductId`, `Direction`, `Description`, `Amount`) VALUES
(3, '2023-03-06 08:52:00', 6, 3, -1, 'Satış', 12),
(8, '2023-03-06 12:16:00', 6, 3, 1, 'Alış', 10),
(9, '2023-03-06 14:18:00', 6, 2, 1, 'Alış', 10),
(11, NULL, 0, 23, 1, 'Sayım', 15);

--
-- Tetikleyiciler `Transaction`
--
DELIMITER $$
CREATE TRIGGER `ProductBalance` AFTER INSERT ON `Transaction` FOR EACH ROW UPDATE Product SET Balance = (SELECT IFNULL(SUM(Direction * Amount), 0) FROM Transaction WHERE ProductId=NEW.ProductId) WHERE Id=NEW.ProductId
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ProductBalanceDelete` AFTER DELETE ON `Transaction` FOR EACH ROW UPDATE Product SET Balance = (SELECT IFNULL(SUM(Direction * Amount), 0) FROM Transaction WHERE ProductId=OLD.ProductId) WHERE Id=OLD.ProductId
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ProductBalanceUpdateAfter` AFTER UPDATE ON `Transaction` FOR EACH ROW UPDATE Product SET Balance = (SELECT IFNULL(SUM(Direction * Amount), 0) FROM Transaction WHERE ProductId=OLD.ProductId) WHERE Id=OLD.ProductId
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ProductBalanceUpdateAfter2` AFTER UPDATE ON `Transaction` FOR EACH ROW UPDATE Product SET Balance = (SELECT IFNULL(SUM(Direction * Amount), 0) FROM Transaction WHERE ProductId=NEW.ProductId) WHERE Id=NEW.ProductId
$$
DELIMITER ;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `Customer`
--
ALTER TABLE `Customer`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `Transaction`
--
ALTER TABLE `Transaction`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Product` (`ProductId`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `Customer`
--
ALTER TABLE `Customer`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `Product`
--
ALTER TABLE `Product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Tablo için AUTO_INCREMENT değeri `Transaction`
--
ALTER TABLE `Transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `Transaction`
--
ALTER TABLE `Transaction`
  ADD CONSTRAINT `Product` FOREIGN KEY (`ProductId`) REFERENCES `Product` (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
