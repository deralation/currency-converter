CREATE DATABASE IF NOT EXISTS `test_currency_converter` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `test_currency_converter`

-- test_currency_converter.Converters definition

--  Drop table

--  DROP TABLE test_currency_converter.Converters;
DROP TABLE IF EXISTS `Converters`;
CREATE TABLE `Converters`(
	`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`date` datetime DEFAULT NULL,
	`source-currency` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`target-currency` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
	`rate` FLOAT DEFAULT NULL,
	`amount` FLOAT DEFAULT NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8
COLLATE=utf8_general_ci;