<?php
/*Datos de conexion a la base de datos*/

define('DB_HOST', trim(getenv('DB_HOST')));//DB_HOST:  generalmente suele ser "127.0.0.1"
define('DB_USER', trim(getenv('DB_USERNAME')));//Usuario de tu base de datos
define('DB_PASS', trim(getenv('DB_PASSWORD')));//Contraseña del usuario de la base de datos
define('DB_NAME', trim(getenv('DB_NAME')));//Nombre de la base de datos
define('DB_DRIVER', trim(getenv('DB_DRIVER')));
define('DB_CHARSET', trim(getenv('DB_CHARSET')));
define('DB_COLLATION', trim(getenv('DB_COLLATION')));

define('SB_CLIENT_ID', trim(getenv('SB_CLIENT_ID')));
define('APPLICATION_ENV', trim(getenv('APPLICATION_ENV'))); //Indica si es sistema esta local o remoto. prod o develop

define('CLOUDCUBE_ACCESS_KEY_ID', trim(getenv('CLOUDCUBE_ACCESS_KEY_ID')));
define('CLOUDCUBE_SECRET_ACCESS_KEY', trim(getenv('CLOUDCUBE_SECRET_ACCESS_KEY')));
define('CLOUDCUBE_URL', trim(getenv('CLOUDCUBE_URL')));
define('CLOUDCUBE_DOMAIN', trim(getenv('CLOUDCUBE_DOMAIN')));

?>
