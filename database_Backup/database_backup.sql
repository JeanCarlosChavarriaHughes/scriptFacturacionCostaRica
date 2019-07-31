/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.5-10.1.34-MariaDB : Database - mxz841yn7rjpnlmu
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mxz841yn7rjpnlmu` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `mxz841yn7rjpnlmu`;

/*Table structure for table `clientes` */

DROP TABLE IF EXISTS `clientes`;
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
  `id_moneda` int(2) NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

/*Data for the table `clientes` */

LOCK TABLES `clientes` WRITE;

insert  into `clientes`(`id_cliente`,`nombre_cliente`,`nombre_comercial_cliente`,`tipo_cedula_cliente`,`cedula_cliente`,`ubicacion_cliente`,`telefono_cliente`,`telefono_fax_cod_cliente`,`telefono_fax_cliente`,`email_cliente`,`estado_cliente`,`direccion_cliente`,`telefono_cod_cliente`,`fecha_creacion_cliente`,`id_moneda`) values (3,'Daniel Gonzalez',NULL,'02','1234567891',NULL,'87700010',NULL,NULL,'dgonzalez@imagineing.com','1','',NULL,'2019-05-10 02:51:23',0);

UNLOCK TABLES;

/*Table structure for table `currencies` */

DROP TABLE IF EXISTS `currencies`;

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

/*Data for the table `currencies` */

LOCK TABLES `currencies` WRITE;

insert  into `currencies`(`id`,`name`,`symbol`,`precision`,`thousand_separator`,`decimal_separator`,`code`) values (1,'US Dollar','$','2',',','.','USD'),(2,'Colon','&#162;','2',',','.','CRC');

UNLOCK TABLES;

/*Table structure for table `detalle_factura` */
DROP TABLE IF EXISTS `detalle_factura`;
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
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8;

/*Data for the table `detalle_factura` */

LOCK TABLES `detalle_factura` WRITE;

insert  into `detalle_factura`(`id_detalle`,`numero_factura`,`id_producto`,`cantidad`,`precio_venta`,`monto_descuento`,`desc_descuento`) values (278,3,20,1,56,15,'Día del niño'),(279,3,19,3,23,0,''),(280,3,17,1,40,0,''),(281,3,18,1,10,2,'Día del niño'),(282,4,36,1,15,2,'Descuento'),(285,6,39,1,15,0,''),(286,6,38,1,12,0,''),(287,6,37,1,12,3,'UN DESCUENTO'),(288,7,39,1,15,0,''),(289,7,37,1,12,0,''),(290,7,36,1,15,3,'Día del padre'),(291,8,17,1,40,0,'');

UNLOCK TABLES;

/*Table structure for table `facturas` */

DROP TABLE IF EXISTS `facturas`;
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
  PRIMARY KEY (`id_factura`),
  UNIQUE KEY `numero_factura` (`numero_factura`),
  KEY `id_vendedor` (`id_vendedor`),
  KEY `facturas_ibfk_1` (`id_cliente`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8;

/*Data for the table `facturas` */

LOCK TABLES `facturas` WRITE;

insert  into `facturas`(`id_factura`,`numero_factura`,`fecha_factura`,`id_cliente`,`id_vendedor`,`condiciones`,`medio_pago`,`total_venta`,`estado_factura`,`total_colones`,`tipo_cambio`,`impuestos`,`moneda`,`plazo_credito`) values (79,3,'21-07-2019',3,1,'1','1','184.3',1,'167.30','0',9,'CRC',NULL),(80,4,'21-07-2019',3,1,'1','1','16.95',1,'14.95','0',2,'CRC',NULL),(82,6,'26-07-2019',3,1,'01','02','44.07',1,'41.07','0',5,'CRC',''),(83,7,'29-07-2019',3,1,'02','01','47.46',1,'44.46','0',5,'CRC','30'),(84,8,'30-07-2019',3,1,'01','01','45.2',1,'45.20','0',5,'CRC','');

UNLOCK TABLES;

/*Table structure for table `perfil` */
DROP TABLE IF EXISTS `perfil`;
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

/*Data for the table `perfil` */

LOCK TABLES `perfil` WRITE;

insert  into `perfil`(`id_perfil`,`tipo_cedula`,`cedula`,`nombre_empresa`,`nombre_empresa_comercial`,`direccion`,`ciudad`,`codigo_postal`,`estado`,`telefono`,`telefono_cod`,`telefono_fax`,`telefono_fax_cod`,`email`,`file_p12`,`key_username`,`key_password`,`pin_p12`,`impuesto`,`moneda`,`logo_url`,`mensaje_factura`,`idFile`,`downloadCode`,`usernameAPI`,`passwordAPI`,`iduserapi`,`acercade`,`ubicacion`,`codigo_actividad_empresa`) values (1,'01','123458789','Su nombre','Su nombre Comercial','San Pedro','Grecia','20201','Alajuela','89888447','506','27102083','506','pruebas@mail.com','070211023522.p12','cpf-07-0211-0235@stag.comprobanteselectronicos.go.cr','.HG@gK=]H>6M[^8ts/^*','2525',13,'¢','img/1503637578_html-2188441_640.png','Esta factura constituye Titulo Ejecutivo y se rige por el artículo 460 del código de Comercio. La cancelación de esta factura se hará en U.S. $, o en colones al tipo de cambio vigente en la fecha de facturación de la misma.																																										',44,'a840d380943483705bf10a6acc50631d','test702110235','pass702110235',5,'Acerca de PRUEBAS DE REGISTRO','6558',900002);

UNLOCK TABLES;

/*Table structure for table `products` */
DROP TABLE IF EXISTS `products`;
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

/*Data for the table `products` */

LOCK TABLES `products` WRITE;

insert  into `products`(`id_producto`,`codigo_producto`,`nombre_producto`,`status_producto`,`date_added`,`precio_producto`,`precio_colon`,`unidad_medida`,`impuesto_codigo`,`impuesto_es_iva`,`impuesto_iva_codigo`,`impuesto_iva_tarifa`,`imp_subimp_tarifa`,`imp_subimp_codigo`,`tipo_producto`,`tip_cod_comerc_producto`) values (17,'8f5665f5','Servilletas',0,'2019-07-04 10:42:47',40.00,40.00,' A/m','01',1,'08','13','',NULL,'mercancia',NULL),(18,'5665dsfs65f4','Globos',1,'2019-07-04 11:20:04',10.00,10.00,' Unid','01',1,'08','13',NULL,NULL,'servicio',NULL),(19,'fgdfg546dg4d6f4g','Silla',1,'2019-07-04 11:23:57',23.00,23.00,' A/m','01',1,'01','0 ','',NULL,'mercancia',NULL),(20,'5fd5df5dfeett98','Mantel',1,'2019-07-04 11:24:17',56.00,56.00,' A/m','02',0,'08','13','5',NULL,'servicio',NULL),(21,'8f9865f5','tenedor',1,'2019-07-04 18:37:50',15.00,15.00,' A/m','03',0,'08','13','10',NULL,'servicio',NULL),(23,'88765f5','Cuchara',1,'2019-07-04 18:38:14',15.00,15.00,' Unid','01',1,'08','13',NULL,NULL,'mercancia',NULL),(24,'8876q89w','Cuchillo',1,'2019-07-04 18:38:44',236.00,236.00,' A/m','07',1,'04','4 ','',NULL,'servicio',NULL),(25,'sdsd5515155','Tijera',1,'2019-07-04 21:36:54',5.00,5.00,' Unid','04',0,'08','13','4',NULL,'mercancia',NULL),(34,'8f566s895f5','Celular',1,'2019-07-06 13:06:28',520.00,520.00,' Unid','03',0,NULL,NULL,'10','2','servicio',NULL),(35,'887fdsfds6q89w','Lapicero',1,'2019-07-06 13:39:10',20.00,20.00,' A/m','07',1,'01','0 ','','5','servicio','03'),(36,'1256487945','Pañuelo',1,'2019-07-20 10:06:30',15.00,15.00,' A/m','01',1,'08','13',NULL,NULL,'mercancia',NULL),(37,'8f566dsd5f5','un producto',1,'2019-07-26 23:10:35',12.00,12.00,' A/m','01',1,'08','13',NULL,NULL,'mercancia','1'),(38,'1234567891011','un producto',1,'2019-07-26 23:11:51',12.00,12.00,' A/m','01',1,'08','13',NULL,NULL,'mercancia','01'),(39,'fgdfg54','aaaaaaa',1,'2019-07-27 00:02:32',15.00,15.00,'A/m','01',1,'08','13',NULL,NULL,'mercancia','02');

UNLOCK TABLES;

/*Table structure for table `tmp` */
DROP TABLE IF EXISTS `tmp`;
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
) ENGINE=MyISAM AUTO_INCREMENT=256 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `tmp` */

LOCK TABLES `tmp` WRITE;

insert  into `tmp`(`id_tmp`,`id_producto`,`cantidad_tmp`,`precio_tmp`,`monto_descuento`,`desc_descuento`,`session_id`,`moneda_tmp`) values (254,17,1,40.00,0.00,'','qgg543oqpcl615fnv6mbiql81p','CRC'),(255,18,1,10.00,0.00,'','qgg543oqpcl615fnv6mbiql81p','CRC');

UNLOCK TABLES;

/*Table structure for table `users` */
DROP TABLE IF EXISTS `users`;
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

/*Data for the table `users` */

LOCK TABLES `users` WRITE;

insert  into `users`(`user_id`,`firstname`,`lastname`,`user_name`,`user_password_hash`,`user_email`,`date_added`) values (1,'Dagoberto','Demo','demo','$2y$10$j3wTY5ls1sQ07o5UHVSSi.dIYTJDmujahNvZoQ0OtMQx2UKOmBRAy','demo@demo.com','2016-05-21 15:06:00');

UNLOCK TABLES;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
