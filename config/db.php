<?php
/*Datos de conexion a la base de datos*/

// Reading from enviroment
$my_DB_HOST = getenv('DB_HOST');
$my_DB_USER = getenv('DB_USERNAME');
$my_DB_PASS = getenv('DB_PASSWORD');
$my_DB_NAME = getenv('DB_NAME');

// Global Constant Definition
define('DB_HOST', $my_DB_HOST); //DB_HOST:  generalmente suele ser "127.0.0.1"
define('DB_USER', $my_DB_USER); //Usuario de tu base de datos
define('DB_PASS', $my_DB_PASS); //Contraseña del usuario de la base de datos
define('DB_NAME', $my_DB_NAME); //Nombre de la base de datos

// heroku factura-imagine jawsDB-Maria master

// heroku factura-simple-develop jawsDB-SQL develop

// db4free credentials no longer working.
/*define('DB_HOST', 'db4free.net');//DB_HOST:  generalmente suele ser "127.0.0.1"
define('DB_USER', 'imagineing');//Usuario de tu base de datos
define('DB_PASS', 'ayn2rzt6ca');//Contraseña del usuario de la base de datos
define('DB_NAME', 'factura3237de49');//Nombre de la base de datos*/
?>
