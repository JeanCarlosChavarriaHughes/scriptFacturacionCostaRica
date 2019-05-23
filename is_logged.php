<?php
    session_start();
	require_once('vendor/autoload.php');
    use Dotenv\Dotenv;
	use GuzzleHttp\Client;
    use Ajax\Helpers as Helpers;
	use Illuminate\Database\Capsule\Manager as Capsule;


	if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
        header("location: ../login.php");
		exit;
    }
    // ======================================================
    // Instancia para uso de variables .ENV
    // ======================================================
    if(getenv('APPLICATION_ENV') === 'local-develop') {
      $dotenv = Dotenv::create(__DIR__);
      $dotenv->load();
    }
    // ======================================================

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
        'prefix'    => getenv('DB_PREFIX'),
    ]);

    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();
    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();
    // ======================================================
    Helpers::validateTokenApi();
