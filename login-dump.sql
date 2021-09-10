-- MariaDB dump 10.17  Distrib 10.4.13-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: login
-- ------------------------------------------------------
-- Server version	10.4.13-MariaDB

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
-- Table structure for table `auftraege`
--

DROP TABLE IF EXISTS `auftraege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auftraege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prioritaet` int(11) NOT NULL,
  `id_kategorien` int(11) DEFAULT NULL,
  `Auftragsname` varchar(50) NOT NULL,
  `Auftragsbeschreibung` text NOT NULL,
  `erstelldatum` date NOT NULL,
  `faellig_am` date NOT NULL,
  `Status` int(11) DEFAULT NULL,
  `ersteller` int(20) DEFAULT NULL,
  `archiviert` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kategorien` (`id_kategorien`),
  CONSTRAINT `auftraege_ibfk_1` FOREIGN KEY (`id_kategorien`) REFERENCES `kategorien` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auftraege`
--

LOCK TABLES `auftraege` WRITE;
/*!40000 ALTER TABLE `auftraege` DISABLE KEYS */;
INSERT INTO `auftraege` VALUES (10,1,1,'Neues Geschäftsauto besorgen','Am besten irgendein Fiat Lieferwagen für unter 30000','2021-03-01','2021-11-25',1,49,NULL),(11,5,5,'Wc Putzen','Jemand hat die Schüssel verfehlt, das zeug klebt überall an den Wänden','2021-03-02','2021-03-04',1,49,NULL),(12,4,3,'Jahresabschluss beenden','Es fehlen immer noch spesenabrechungen von einigen Mitarbeiter sie Habast von einem Buchhalter','2021-03-02','2021-02-01',1,49,NULL),(13,3,1,'Klo Reinigung','Das Klo muss geputzt werden','2021-02-16','2021-02-17',100,49,'1'),(14,3,2,'Entwicklung neues Todo Programm','Michael Schmid ist ein sehr toller Informatiker','2021-02-19','2021-03-02',99,49,NULL),(15,1,3,'auftragplatzhalter','auftragplatzhalter','2021-03-02','2024-02-02',1,49,NULL),(16,3,4,'auftragplatzhalter','auftragplatzhalter','2021-03-02','2024-10-04',1,49,NULL),(17,5,1,'GIpfeli oder Bretzel kaufen','in de pause','2021-03-02','2021-03-02',1,49,NULL),(18,2,1,'auftragplatzhalter','auftragplatzhalter','2021-03-26','2021-04-08',4,49,NULL),(19,2,1,'auftragplatzhalter','auftragplatzhalter','2021-03-02','2021-04-07',1,49,NULL),(20,1,1,'auftragplatzhalter','auftragplatzhalter','2021-03-19','2021-03-10',1,49,NULL),(21,2,4,'Glühbirne Wechseln','Abmarsch jetzt','2021-03-02','2021-03-03',4,49,NULL),(22,3,5,'Flur aufziehen','der Flur im 3. stock ist dreckig','2021-03-04','2021-03-16',1,49,NULL),(23,2,1,'auftragplatzhalter','auftragplatzhalter','2021-04-02','2021-04-11',1,49,NULL),(24,1,1,'auftragplatzhalter','auftragplatzhalter','2022-02-22','2023-02-22',1,49,NULL),(25,2,3,'auftragplatzhalter','auftragplatzhalter','2021-04-10','2025-02-22',1,49,NULL),(26,1,1,'auftragplatzhalter','auftragplatzhalter','2021-03-26','2026-09-02',1,49,NULL);
/*!40000 ALTER TABLE `auftraege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategorien`
--

DROP TABLE IF EXISTS `kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kategorien` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `kategorienamen` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategorien`
--

LOCK TABLES `kategorien` WRITE;
/*!40000 ALTER TABLE `kategorien` DISABLE KEYS */;
INSERT INTO `kategorien` VALUES (1,'Administration'),(2,'IT'),(3,'Buchhaltung'),(4,'Monteure'),(5,'Putzcrew');
/*!40000 ALTER TABLE `kategorien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_has_kategorien`
--

DROP TABLE IF EXISTS `user_has_kategorien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_has_kategorien` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_kategorien` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kategorie` (`id_kategorien`),
  KEY `fk_users` (`id_user`),
  CONSTRAINT `fk_kategorie` FOREIGN KEY (`id_kategorien`) REFERENCES `kategorien` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_has_kategorien`
--

LOCK TABLES `user_has_kategorien` WRITE;
/*!40000 ALTER TABLE `user_has_kategorien` DISABLE KEYS */;
INSERT INTO `user_has_kategorien` VALUES (34,49,1),(35,49,2),(36,49,3),(37,49,4),(38,49,5);
/*!40000 ALTER TABLE `user_has_kategorien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `perms` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'Francesco','Lurati','Admin','$2y$10$OEXdaxQ6Vhxohrd2Kc/BZ.eTTMQngCzKkjeE5ESIml0s/UR94/DLa','francesco.lurati@edubs.ch','a'),(49,'Francesco','Lurati','Aufseher','$2y$10$L.AWLXxBBXzVfn3LTVdR9OLLEKOU7ninQkw831AQpnCmE4meUoW1O','francesco.lurati@edubs.ch','u');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-02  9:53:17
