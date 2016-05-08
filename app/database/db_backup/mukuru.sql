-- MySQL dump 10.13  Distrib 5.6.22, for osx10.8 (x86_64)
--
-- Host: localhost    Database: mukuru_practical_test_db
-- ------------------------------------------------------
-- Server version	5.5.47-0+deb7u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `mpt_currency`
--

DROP TABLE IF EXISTS `mpt_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mpt_currency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `currency` enum('USD','GBP','KES','EUR') COLLATE utf8_unicode_ci NOT NULL,
  `rate` decimal(15,8) NOT NULL,
  `surcharge` decimal(5,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mpt_currency`
--

LOCK TABLES `mpt_currency` WRITE;
/*!40000 ALTER TABLE `mpt_currency` DISABLE KEYS */;
INSERT INTO `mpt_currency` VALUES (1,'US Dollars','USD',0.08082790,7.50,0.00,'2016-02-24 09:44:30','0000-00-00 00:00:00',NULL),(2,'British Pound','GBP',0.05270320,5.00,0.00,'2016-02-24 09:44:30','0000-00-00 00:00:00',NULL),(3,'Euro','EUR',0.07187100,5.00,0.00,'2016-02-24 09:44:30','0000-00-00 00:00:00',NULL),(4,'Kenyan Shilling','KES',7.81498000,2.50,2.00,'2016-02-24 09:44:30','0000-00-00 00:00:00',NULL);
/*!40000 ALTER TABLE `mpt_currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mpt_migrations`
--

DROP TABLE IF EXISTS `mpt_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mpt_migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mpt_migrations`
--

LOCK TABLES `mpt_migrations` WRITE;
/*!40000 ALTER TABLE `mpt_migrations` DISABLE KEYS */;
INSERT INTO `mpt_migrations` VALUES ('2016_02_20_130052_CreateCurrencyTable',1),('2016_02_20_130058_CreateOrdersTable',1);
/*!40000 ALTER TABLE `mpt_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mpt_orders`
--

DROP TABLE IF EXISTS `mpt_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mpt_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(10) unsigned NOT NULL,
  `zar_amount` decimal(15,2) NOT NULL,
  `foreign_amount` decimal(15,2) NOT NULL,
  `surcharge_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_currency_id_foreign` (`currency_id`),
  CONSTRAINT `orders_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `mpt_currency` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mpt_orders`
--

LOCK TABLES `mpt_orders` WRITE;
/*!40000 ALTER TABLE `mpt_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `mpt_orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-02-24 11:46:18
