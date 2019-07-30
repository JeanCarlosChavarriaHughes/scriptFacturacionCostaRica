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
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8;

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
  KEY `id_cliente` (`id_cliente`),
  KEY `id_vendedor` (`id_vendedor`),
  CONSTRAINT `facturas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `facturas_ibfk_2` FOREIGN KEY (`id_vendedor`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM AUTO_INCREMENT=248 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
