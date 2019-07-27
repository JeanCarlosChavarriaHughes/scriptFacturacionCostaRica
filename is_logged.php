<?php
    session_start();
    require_once("config/constants.php");
	require_once('vendor/autoload.php');
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
    // Helpers::validateTokenApi();

    // ======================================================
    // PARA PRUEBAS SPRINT #2
    // ======================================================
    //
    // $last_consecutive   = Capsule::table('detalle_factura')->max('numero_factura');
    // echo $consecutive_number = Helpers::getConsecutiveNumber($last_consecutive, "06", (int) getenv('BASE_LOCAL'), (int) getenv('BASE_TERMINAL'));
    // $consecutive_for_Nkey = str_pad( (string) $last_consecutive, 10, '0', STR_PAD_LEFT);
    // $numeric_key = Helpers::getNumericKey("fisico", 702320717, 507, (int) $consecutive_for_Nkey, "FE");
    // echo $numeric_key->clave;
    // echo Helpers::checkStatusForNumericKey("50706061900070232071700100001040000000003384619703");
    //
    // ======================================================

