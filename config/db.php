<?php
/*Datos de conexion a la base de datos*/

// heroku factura-imagine jawsDB
// mysql://c5slmb6oyhuo6rxy:fep7oias93ln51fc@jlg7sfncbhyvga14.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/mxz841yn7rjpnlmu

define('DB_HOST', getenv('DB_HOST'));//DB_HOST:  generalmente suele ser "127.0.0.1"
define('DB_USER', getenv('DB_USERNAME'));//Usuario de tu base de datos
define('DB_PASS', getenv('DB_PASSWORD'));//Contraseña del usuario de la base de datos
define('DB_NAME', getenv('DB_NAME'));//Nombre de la base de datos

// db4free credentials no longer working.
/*define('DB_HOST', 'db4free.net');//DB_HOST:  generalmente suele ser "127.0.0.1"
define('DB_USER', 'imagineing');//Usuario de tu base de datos
define('DB_PASS', 'ayn2rzt6ca');//Contraseña del usuario de la base de datos
define('DB_NAME', 'factura3237de49');//Nombre de la base de datos*/
?>
