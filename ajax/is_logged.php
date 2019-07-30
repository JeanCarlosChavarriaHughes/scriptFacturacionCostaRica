<?php
	session_start();
	require_once('../vendor/autoload.php');
	use GuzzleHttp\Client;
    use Ajax\Helpers as Helpers;
	use Illuminate\Database\Capsule\Manager as Capsule;


	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: ../login.php");
		exit;
    }

    // ======================================================
    // Instancia conexión con BD
    // ======================================================
    $capsule = new Capsule;
    $capsule->addConnection([
        'driver'    => getenv('DB_DRIVER'),
        'host'      => getenv('DB_HOST'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USERNAME'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => getenv('DB_CHARSET'),
        'collation' => getenv('DB_COLLATION'),
        // 'prefix'    => getenv('DB_PREFIX'),
    ]);
    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();
    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();
    // ======================================================

    $helpers = new Helpers();
    Helpers::validateTokenApi();

    // $xml = Helpers::createXmlFE($helpers, 83);

    // $xmlfirmado = Helpers::firmarXML($helpers, $xml->xml,"FE");
    // echo $xmlfirmado->xmlFirmado;

    // $enviofe = Helpers::envioHaciendaFE($helpers, $xmlfirmado->xmlFirmado, 83);
    // var_dump($enviofe);

    // $consultaenviofe = Helpers::consultaEnvioHaciendaFE("50629071900012345878900100001010000000007184619703");
    // var_dump($consultaenviofe);


