-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: electric_company
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customer_accounts`
--

DROP TABLE IF EXISTS `customer_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_number` varchar(50) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `meter_number` varchar(50) DEFAULT NULL,
  `connection_type` enum('residential','commercial','industrial') DEFAULT 'residential',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_number` (`account_number`),
  UNIQUE KEY `unique_customer_username` (`username`),
  KEY `idx_account_number` (`account_number`),
  KEY `idx_status` (`status`),
  KEY `idx_connection_type` (`connection_type`),
  KEY `idx_customer_name` (`customer_name`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_accounts`
--

LOCK TABLES `customer_accounts` WRITE;
/*!40000 ALTER TABLE `customer_accounts` DISABLE KEYS */;
INSERT INTO `customer_accounts` VALUES (1,'EC-2024-0001',NULL,NULL,'John Smith','123 Main Street, Downtown','555-0101','john.smith@email.com','MTR-001','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(2,'EC-2024-0002',NULL,NULL,'Sarah Johnson','456 Oak Avenue, Suburb','555-0102','sarah.j@email.com','MTR-002','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(3,'EC-2024-0003',NULL,NULL,'ABC Corporation','789 Business Blvd, City Center','555-\r\n0103','contact@abc.com','MTR-003','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(4,'EC-2024-0004',NULL,NULL,'Michael Brown','321 Pine Road, Eastside','555-0104','mbrown@email.com','MTR-004','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(5,'EC-2024-0005',NULL,NULL,'Tech Industries Inc','555 Industrial Park, Zone A','555-\r\n0105','info@techindustries.com','MTR-005','industrial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(6,'EC-2024-0006',NULL,NULL,'Emily Davis','678 Maple Drive, Westside','555-0106','emily.d@email.com','MTR-006','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(7,'EC-2024-0007',NULL,NULL,'Green Mart Store','890 Commerce Street, Plaza','555-\r\n0107','greenmart@email.com','MTR-007','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(8,'EC-2024-0008',NULL,NULL,'Robert Wilson','234 Cedar Lane, Northside','555-0108','rwilson@email.com','MTR-008','residential','inactive','2025-10-22 01:01:51','2025-10-22 01:01:51'),(9,'EC-2024-0009',NULL,NULL,'Manufacturing Co','432 Factory Road, Industrial Zone','555-0109','info@mfgco.com','MTR-009','industrial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(10,'EC-2024-0010',NULL,NULL,'Lisa Anderson','567 Birch Street, Southside','555-0110','landerson@email.com','MTR-010','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(11,'EC-2024-0011',NULL,NULL,'David Martinez','890 Elm Avenue, Central','555-0111','dmartinez@email.com','MTR-011','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(12,'EC-2024-0012',NULL,NULL,'Retail Plaza LLC','123 Shopping Center, Mall District','555-0112','contact@retailplaza.com','MTR-012','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(13,'EC-2024-0013',NULL,NULL,'Jennifer Taylor','456 Spruce Road, Hillside','555-0113','jtaylor@email.com','MTR-013','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(14,'EC-2024-0014',NULL,NULL,'Heavy Industries Ltd','789 Manufacturing Ave, Zone B','555-0114','info@heavyind.com','MTR-014','industrial','suspended','2025-10-22 01:01:51','2025-10-22 01:01:51'),(15,'EC-2024-0015',NULL,NULL,'Thomas White','321 Willow Lane, Riverside','555-0115','twhite@email.com','MTR-015','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(16,'EC-2024-0016',NULL,NULL,'Office Complex Inc','654 Corporate Drive, Business Park','555-0116','admin@officecomplex.com','MTR-016','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(17,'EC-2024-0017',NULL,NULL,'Patricia Harris','987 Ash Street, Lakeside','555-0117','pharris@email.com','MTR-017','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(18,'EC-2024-0018',NULL,NULL,'Auto Manufacturing','246 Assembly Line Rd, Industrial\r\nPark','555-0118','contact@automfg.com','MTR-018','industrial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(19,'EC-2024-0019',NULL,NULL,'Christopher Lee','135 Poplar Avenue, Garden District','555-0119','clee@email.com','MTR-019','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(20,'EC-2024-0020',NULL,NULL,'Shopping Center Co','468 Retail Blvd, Downtown','555-\r\n0120','info@shopcenter.com','MTR-020','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(21,'EC-2024-0021',NULL,NULL,'Nancy Clark','579 Hickory Drive, Parkside','555-0121','nclark@email.com','MTR-021','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(22,'EC-2024-0022',NULL,NULL,'Steel Works Inc','802 Foundry Road, Industrial Zone C','555-0122','contact@steelworks.com','MTR-022','industrial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(23,'EC-2024-0023',NULL,NULL,'Daniel Lewis','913 Sycamore Lane, Meadow View','555-\r\n0123','dlewis@email.com','MTR-023','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(24,'EC-2024-0024',NULL,NULL,'Restaurant Group LLC','246 Dining Street, Food District','555-0124','info@restgroup.com','MTR-024','commercial','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(25,'EC-2024-0025',NULL,NULL,'Karen Walker','357 Magnolia Road, Sunset Hills','555-\r\n0125','kwalker@email.com','MTR-025','residential','active','2025-10-22 01:01:51','2025-10-22 01:01:51'),(28,'PF-2026-D0B2DA','Rovic','$2y$10$ZX7r57r5SD0wpLPDHfqLe.T9GMLeiOb7tseLvJhD4XbLKqipnTabS','Rovic Bilbao','123 Main Street, Manila, CO 12345','(929) 317-3739','rovic@email.com','MTR-6848FD','residential','active','2026-10-03 10:47:54','2026-10-03 10:47:54');
/*!40000 ALTER TABLE `customer_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026-10-04-000001','App\\Database\\Migrations\\CreatePowerFlowUsers','default','App',1791046775,1),(2,'2026-10-04-000002','App\\Database\\Migrations\\MoveCustomersToCustomerAccounts','default','App',1791053057,2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_accounts`
--

DROP TABLE IF EXISTS `user_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_accounts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_accounts`
--

LOCK TABLES `user_accounts` WRITE;
/*!40000 ALTER TABLE `user_accounts` DISABLE KEYS */;
INSERT INTO `user_accounts` VALUES (1,'admin','$2y$10$UPHEVEZrmPzEOZSu/rjXCezgEJ4IshyBE.yQdAjl4MXj7uB9jsHKy','PowerFlow Administrator','2026-10-03 16:59:35','2026-10-03 16:59:35');
/*!40000 ALTER TABLE `user_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'electric_company'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-07 10:12:05
