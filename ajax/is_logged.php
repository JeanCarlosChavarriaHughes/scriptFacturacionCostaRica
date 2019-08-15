<?php
	session_start();
	require_once('../vendor/autoload.php');
    require_once('../config/db.php');
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
        'driver'    => DB_DRIVER,
        'host'      => DB_HOST,
        'database'  => DB_NAME,
        'username'  => DB_USER,
        'password'  => DB_PASS,
        'charset'   => DB_CHARSET,
        'collation' => DB_COLLATION,
        // 'prefix'    => getenv('DB_PREFIX,
    ]);
    // Make this Capsule instance available globally via static methods... (optional)
    $capsule->setAsGlobal();
    // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
    $capsule->bootEloquent();
    // ======================================================

    Helpers::validateTokenApi();


