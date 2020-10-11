-- MySQL dump 10.13  Distrib 8.0.12, for osx10.13 (x86_64)
--
-- Host: arfo8ynm6olw6vpn.cbetxkdyhwsb.us-east-1.rds.amazonaws.com    Database: a0yuiwc76c716tiv
-- ------------------------------------------------------
-- Server version	5.7.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '';

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_cliente` varchar(255) NOT NULL,
  `nombre_comercial_cliente` varchar(255) DEFAULT NULL,
  `tipo_cedula_cliente` varchar(2) DEFAULT NULL,
  `cedula_cliente` varchar(12) NOT NULL,
  `ubicacion_cliente` varchar(10) DEFAULT NULL,
  `telefono_cliente` varchar(15) NOT NULL,
  `telefono_fax_cod_cliente` varchar(5) DEFAULT NULL,
  `telefono_fax_cliente` varchar(15) DEFAULT NULL,
  `email_cliente` varchar(64) NOT NULL,
  `estado_cliente` varchar(4) NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL,
  `telefono_cod_cliente` varchar(5) DEFAULT NULL,
  `fecha_creacion_cliente` datetime NOT NULL,
  `id_moneda` varchar(6) NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (3,'Daniel Gonzalez Cordero','Daniel ImagineIng','01','115200399','1583','87700010','506','3016710511','dgonzalez@imagineing.com','1','CALLE 7A #12-42','506','2019-05-10 02:51:23','CRC'),(14,'Negrotico19 Gmail','negrotico19 gmail','01','702110235','486','22038111','506','27102083','negrotico19@gmail.com','1','CL 25 SUR','506','2019-08-07 22:52:53','CRC'),(15,'Junior Soto','Junior Sotto Freelancer','01','963285647','5313','6349875648','506','3214587458','juniorsotto8@gmail.com','1','Los martires cr 52 #9 89','506','2019-08-07 22:53:48','CRC'),(16,'JeanCarlos Chavarria Hughes','ImagineIng','01','702110235','486','89888447','506','27102083','jchavarria@imagineing.com','1','300 Norte 100 Oeste 75 Norte del CTPP','506','2019-09-18 04:02:21','CRC'),(17,'Facturas Imagine','Facturas Imagine','02','3101759197','5530','89888447','506','27102083','facturas.imagineing@gmail.com','1','Centro San Jose','506','2019-09-27 03:23:44','CRC'),(18,'Kevin Picado Arias','kpicado imagineing.com','01','100110022','1574','88682368','506','27102083','kpicado@imagineing.com','1','Centro San Jose','506','2019-09-27 03:26:40','CRC'),(19,'Kevin Picado Arias','kpicado gmail.com','01','100110023','1574','88682368','506','27102083','kpicari@gmail.com','1','Centro San juan','506','2019-09-27 03:31:12','CRC');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `precision` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `thousand_separator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'US Dollar','$','2',',','.','USD'),(2,'Colon','&#162;','2',',','.','CRC');
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_factura`
--

DROP TABLE IF EXISTS `detalle_factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `detalle_factura` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `numero_factura` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_venta` double NOT NULL,
  `monto_descuento` double DEFAULT NULL,
  `desc_descuento` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `detalle_factura_ibfk_1` (`numero_factura`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_factura_ibfk_1` FOREIGN KEY (`numero_factura`) REFERENCES `facturas` (`numero_factura`) ON DELETE CASCADE,
  CONSTRAINT `detalle_factura_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `products` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=404 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_factura`
--

LOCK TABLES `detalle_factura` WRITE;
/*!40000 ALTER TABLE `detalle_factura` DISABLE KEYS */;
INSERT INTO `detalle_factura` VALUES (278,3,20,1,56,15,'Día del niño'),(279,3,19,3,23,0,''),(280,3,17,1,40,0,''),(281,3,18,1,10,2,'Día del niño'),(282,4,36,1,15,2,'Descuento'),(285,6,39,1,15,0,''),(286,6,38,1,12,0,''),(287,6,37,1,12,3,'UN DESCUENTO'),(288,7,39,1,15,0,''),(289,7,37,1,12,0,''),(290,7,36,1,15,3,'Día del padre'),(291,8,17,1,40,0,''),(326,9,18,1,10,0,''),(327,9,19,1,23,0,''),(328,10,39,1,15,0,''),(329,10,37,1,12,0,''),(330,11,17,1,40,0,''),(331,12,17,1,40,3,'Día del niño'),(332,12,37,1,12,0,''),(333,12,24,1,236,0,''),(334,12,35,1,20,0,''),(335,12,25,1,5,0,''),(336,13,38,1,12,0,''),(337,13,39,1,15,0,''),(338,14,36,1,15,0,''),(339,15,39,1,15,0,''),(340,16,39,1,15,0,''),(341,16,38,1,12,0,''),(342,17,17,1,40,0,''),(343,18,17,1,40,0,''),(344,19,17,1,40,0,''),(345,20,17,1,40,0,''),(346,21,17,1,40,0,''),(347,22,17,1,40,0,''),(348,23,17,1,40,0,''),(349,24,17,1,40,0,''),(350,25,17,1,40,0,''),(351,26,17,1,40,0,''),(352,27,17,1,40,0,''),(353,28,17,1,40,0,''),(354,29,17,1,40,0,''),(355,30,17,1,40,0,''),(356,31,17,1,40,0,''),(357,32,17,1,40,0,''),(358,33,17,1,40,0,''),(359,34,17,1,40,0,''),(360,35,17,1,40,0,''),(361,36,17,1,40,0,''),(362,37,17,1,40,0,''),(363,38,17,1,40,0,''),(364,39,17,1,40,0,''),(365,40,17,1,40,0,''),(366,41,17,1,40,0,''),(367,42,17,1,40,0,''),(368,43,17,1,40,0,''),(369,44,17,1,40,0,''),(370,45,17,1,40,0,''),(371,46,17,1,40,0,''),(372,47,17,1,40,0,''),(373,48,17,1,40,0,''),(374,49,17,1,40,0,''),(375,50,17,1,40,0,''),(376,51,36,1,15,0,''),(377,51,37,1,12,0,''),(378,51,38,1,12,0,''),(379,51,39,1,15,12,'día del padre'),(380,52,39,1,15,2,'especial'),(381,52,38,1,12,0,''),(382,52,37,1,12,0,''),(383,53,18,1,10,0,''),(384,53,17,1,40,0,''),(385,54,18,1,10,0,''),(386,54,17,1,40,0,''),(387,55,18,1,10,0,''),(388,55,17,1,40,0,''),(389,56,18,1,10,0,''),(390,56,17,1,40,0,''),(391,57,18,1,10,0,''),(392,57,17,1,40,0,''),(393,58,38,1,12,0,''),(394,59,18,1,10,0,''),(395,59,17,1,40,0,''),(396,60,17,1,40,0,''),(397,61,18,1,10,0,''),(398,61,17,1,40,0,''),(399,62,37,1,236,0,''),(400,62,39,1,15,0,''),(401,63,38,2,15,0,''),(402,63,37,2,236,0,''),(403,63,39,2,15,0,'');
/*!40000 ALTER TABLE `detalle_factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `facturas` (
  `id_factura` int(11) NOT NULL AUTO_INCREMENT,
  `numero_factura` int(11) NOT NULL,
  `fecha_factura` varchar(20) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `condiciones` varchar(30) NOT NULL,
  `medio_pago` varchar(30) DEFAULT NULL,
  `total_venta` varchar(20) NOT NULL,
  `estado_factura` tinyint(1) NOT NULL,
  `total_colones` varchar(30) NOT NULL,
  `tipo_cambio` varchar(20) NOT NULL,
  `impuestos` int(2) NOT NULL,
  `moneda` varchar(11) NOT NULL,
  `plazo_credito` varchar(5) DEFAULT NULL,
  `pago_online` decimal(1,0) NOT NULL DEFAULT '0',
  `pago_online_order_id` varchar(100) DEFAULT NULL,
  `pago_online_payer_id` varchar(100) DEFAULT NULL,
  `pago_online_status` varchar(50) DEFAULT NULL,
  `pago_online_time` timestamp NULL DEFAULT NULL,
  `pago_online_id_trans` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_factura`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `id_vendedor` (`id_vendedor`),
  KEY `facturas_ibfk_1` (`id_cliente`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (79,3,'21-07-2019',3,1,'1','1','184.3',1,'167.30','0',9,'CRC',NULL,0,NULL,NULL,NULL,NULL,NULL),(80,4,'21-07-2019',3,1,'1','1','16.95',1,'14.95','0',2,'CRC',NULL,0,NULL,NULL,NULL,NULL,NULL),(82,6,'26-07-2019',3,1,'01','02','44.07',1,'41.07','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(83,7,'29-07-2019',3,1,'02','01','47.46',1,'44.46','0',5,'CRC','30',0,NULL,NULL,NULL,NULL,NULL),(84,8,'30-07-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(104,9,'01-08-2019',3,1,'01','01','34.3',1,'34.30','0',1,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(105,10,'01-08-2019',3,1,'01','01','30.51',1,'30.51','0',4,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(106,11,'07-08-2019',14,1,'01','01','45.2',1,'45.20','0',5,'AFN','',0,NULL,NULL,NULL,NULL,NULL),(107,12,'08-08-2019',3,1,'01','01','329.4',1,'326.40','0',16,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(108,13,'23-08-2019',3,1,'01','01','30.51',1,'30.51','0',4,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(109,14,'23-08-2019',3,1,'01','01','16.95',1,'16.95','0',2,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(110,15,'23-08-2019',3,1,'01','01','15.3',1,'0.00','0',2,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(111,16,'26-08-2019',3,1,'01','01','30.51',1,'30.51','0',4,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(112,17,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(113,18,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(114,19,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(115,20,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(116,21,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(117,22,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(118,23,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(119,24,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(120,25,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(121,26,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(122,27,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(123,28,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(124,29,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(125,30,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(126,31,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(127,32,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(128,33,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(129,34,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(130,35,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(131,36,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(132,37,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(133,38,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(134,39,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(135,40,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(136,41,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(137,42,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(138,43,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(139,44,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(140,45,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(141,46,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(142,47,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(143,48,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(144,49,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(145,50,'10-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(146,51,'10-09-2019',3,1,'01','01','61.02',1,'49.02','0',7,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(147,52,'10-09-2019',3,1,'01','01','44.07',1,'42.07','0',5,'CRC','',1,'8G8718501P056202V','7G585TNBC8HGC','COMPLETED','2019-09-13 09:31:38','31860808XU899162H'),(148,53,'10-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(149,54,'10-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(150,55,'10-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(151,56,'10-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',0,NULL,NULL,NULL,NULL,NULL),(152,57,'10-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',1,'0XY20411B2551430L','T2QFTUEVVWX8N','COMPLETED','2019-09-11 03:49:10','37W19655KM1652818'),(153,58,'11-09-2019',3,1,'01','01','13.56',1,'13.56','0',2,'CRC','',1,'0AE50800UD540191G','T2QFTUEVVWX8N','DECLINED','2019-09-13 07:40:45','6JA26078KF8154039'),(154,59,'13-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',1,'3VP45721C3603772M','Y6J8MRDUYUWVU','COMPLETED','2019-09-13 09:27:21','4JA1760011145702P'),(155,60,'13-09-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','',1,'8NH75105WH2971258','T2QFTUEVVWX8N','COMPLETED','2019-09-13 09:18:26','5V89861841786935B'),(156,61,'13-09-2019',3,1,'01','01','56.5',1,'56.50','0',7,'CRC','',0,'','','','0000-00-00 00:00:00',''),(157,62,'17-09-2019',16,1,'01','02','281.68',1,'281.68','0',31,'CRC','',0,'','','','0000-00-00 00:00:00',''),(158,63,'17-09-2019',16,1,'01','02','594.56',1,'594.56','0',63,'CRC','',0,'','','','0000-00-00 00:00:00','');
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perfil`
--

DROP TABLE IF EXISTS `perfil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `perfil` (
  `id_perfil` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_cedula` varchar(2) CHARACTER SET latin1 NOT NULL,
  `cedula` varchar(250) CHARACTER SET latin1 NOT NULL,
  `nombre_empresa` varchar(80) CHARACTER SET latin1 NOT NULL,
  `nombre_empresa_comercial` varchar(80) CHARACTER SET latin1 DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET latin1 NOT NULL,
  `ciudad` varchar(100) CHARACTER SET latin1 NOT NULL,
  `codigo_postal` varchar(100) CHARACTER SET latin1 NOT NULL,
  `estado` varchar(100) CHARACTER SET latin1 NOT NULL,
  `telefono` varchar(20) CHARACTER SET latin1 NOT NULL,
  `telefono_cod` varchar(3) CHARACTER SET latin1 DEFAULT NULL,
  `telefono_fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `telefono_fax_cod` varchar(3) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(60) CHARACTER SET latin1 NOT NULL,
  `file_p12` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `key_username` varchar(52) CHARACTER SET latin1 DEFAULT NULL,
  `key_password` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `pin_p12` varchar(4) CHARACTER SET latin1 DEFAULT NULL,
  `impuesto` int(2) DEFAULT NULL,
  `moneda` varchar(6) CHARACTER SET latin1 NOT NULL,
  `logo_url` varchar(255) CHARACTER SET latin1 NOT NULL,
  `mensaje_factura` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idFile` int(11) DEFAULT NULL,
  `downloadCode` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `usernameAPI` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `passwordAPI` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `iduserapi` int(11) DEFAULT NULL,
  `acercade` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `ubicacion` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `codigo_actividad_empresa` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perfil`
--

LOCK TABLES `perfil` WRITE;
/*!40000 ALTER TABLE `perfil` DISABLE KEYS */;
INSERT INTO `perfil` VALUES (1,'01','702110235','Facturador Digital CR','Facturador Digital CR','Calle Puente Tierra','Grecia','20201','Alajuela','89888447','506','27102083','506','negrotico19@gmail.com','070211023522.p12','cpf-07-0211-0235@stag.comprobanteselectronicos.go.cr','.HG@gK=]H>6M[^8ts/^*','2525',13,'CRC','img/1503637578_html-2188441_640.png','Esta factura constituye Titulo Ejecutivo y se rige por el artículo 460 del código de Comercio. La cancelación de esta factura se hará en U.S. $, o en colones al tipo de cambio vigente en la fecha de facturación de la misma.																																										',46,'f6de0bba4459216d67430d57a17c1e42','test702110235','pass702110235',5,'Acerca de PRUEBAS DE REGISTRO','5847',729001);
/*!40000 ALTER TABLE `perfil` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `products` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_producto` char(20) NOT NULL,
  `nombre_producto` char(255) NOT NULL,
  `status_producto` tinyint(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `precio_producto` double(11,2) NOT NULL,
  `precio_colon` decimal(10,2) NOT NULL,
  `unidad_medida` char(20) DEFAULT NULL,
  `impuesto_codigo` varchar(2) DEFAULT NULL,
  `impuesto_es_iva` int(1) DEFAULT NULL,
  `impuesto_iva_codigo` varchar(2) DEFAULT NULL,
  `impuesto_iva_tarifa` varchar(2) DEFAULT NULL,
  `imp_subimp_tarifa` varchar(2) DEFAULT NULL,
  `imp_subimp_codigo` varchar(2) DEFAULT NULL,
  `tipo_producto` enum('servicio','mercancia') NOT NULL,
  `tip_cod_comerc_producto` varchar(2) DEFAULT NULL COMMENT 'codigoComercial>tipo',
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo_producto` (`codigo_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (17,'Codigo14','Producto14  Bebidas Hasta30 13%',0,'2019-07-04 10:42:47',40.00,40.00,' A/m','04',0,'08','13','4','02','mercancia','01'),(18,'Codigo13','Producto13 Bebidas hasta15 13%',1,'2019-07-04 11:20:04',10.00,10.00,' A/m','04',0,'08','13','4','01','servicio','02'),(19,'Codigo12','Producto12 Consumo otros tabacos 0%',1,'2019-07-04 11:23:57',23.00,23.00,' A/m','02',0,'01','0 ','5','00','mercancia','02'),(20,'Codigo11','Producto11 Consumo Cigarritos 5%',1,'2019-07-04 11:24:17',56.00,56.00,' A/m','02',0,'08','13','5','03','servicio','02'),(21,'Codigo10','Producto10 Consumo Cigarros 10%',1,'2019-07-04 18:37:50',15.00,15.00,' A/m','02',0,'08','13','5','02','servicio','01'),(23,'Codigo9','Producto9 Consumo Tabaco 5%',1,'2019-07-04 18:38:14',15.00,15.00,' A/m','02',0,'08','13','5','01','mercancia','01'),(24,'Codigo7','Producto7 IVA Trans 8%',1,'2019-07-04 18:38:44',236.00,236.00,' A/m','01',1,'07','8 ','',NULL,'servicio','01'),(25,'Codigo6','Producto6 IVA Trans 4%',1,'2019-07-04 21:36:54',5.00,5.00,' A/m','01',1,'06','4 ','',NULL,'mercancia','01'),(34,'Codigo5','Producto5 IVA Red 2%',1,'2019-07-06 13:06:28',520.00,520.00,' A/m','00',1,'03','2 ','','2','servicio','02'),(35,'Codigo3','Producto3 IVA Trans 0%',1,'2019-07-06 13:39:10',20.00,20.00,' A/m','00',1,'05','0 ','','5','servicio','01'),(36,'Codigo2','Producto2 IVA Red 1%',1,'2019-07-20 10:06:30',15.00,15.00,' A/m','01',1,'02','1 ','',NULL,'mercancia','02'),(37,'Codigo8','Producto8 IVA Gen 13%',1,'2019-07-26 23:10:35',236.00,236.00,' A/m','01',1,'08','13','',NULL,'mercancia','01'),(38,'Codigo4','Producto4 IVA Red 4%',1,'2019-07-26 23:11:51',15.00,15.00,' A/m','01',1,'04','4 ','',NULL,'mercancia','01'),(39,'Codigo1','Producto1 IVA Ext 0%',1,'2019-07-27 00:02:32',15.00,15.00,' A/m','01',1,'01','0 ','',NULL,'mercancia','02'),(40,'Codigo15','Producto15 Bebidas Mas30 4%',1,'2019-09-27 02:56:13',50.00,50.00,' A/m','04',0,NULL,NULL,'4','03','mercancia','01');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmp`
--

DROP TABLE IF EXISTS `tmp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `tmp` (
  `id_tmp` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `cantidad_tmp` int(11) NOT NULL,
  `precio_tmp` double(8,2) DEFAULT NULL,
  `monto_descuento` double(8,2) DEFAULT NULL,
  `desc_descuento` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `session_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `moneda_tmp` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_tmp`)
) ENGINE=MyISAM AUTO_INCREMENT=314 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmp`
--

LOCK TABLES `tmp` WRITE;
/*!40000 ALTER TABLE `tmp` DISABLE KEYS */;
INSERT INTO `tmp` VALUES (308,37,3,236.00,0.00,'','rikpb774cca9narm778m439cga','CRC'),(307,38,3,15.00,0.00,'','rikpb774cca9narm778m439cga','CRC'),(306,39,4,15.00,0.00,'','rikpb774cca9narm778m439cga','CRC');
/*!40000 ALTER TABLE `tmp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `firstname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `perfil` (`id_perfil`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Facturador Simple','ImagineIng','demo','$2y$10$j3wTY5ls1sQ07o5UHVSSi.dIYTJDmujahNvZoQ0OtMQx2UKOmBRAy','info@imagineing.com','2016-05-21 15:06:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'a0yuiwc76c716tiv'
--

--
-- Dumping routines for database 'a0yuiwc76c716tiv'
--
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-26 21:46:31
