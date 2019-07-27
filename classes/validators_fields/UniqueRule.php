<?php
    namespace ValidatorsFields;
    use Rakit\Validation\Rule;
    use Illuminate\Database\Capsule\Manager as Capsule;

    /**
    *  Determina si un valor dado ya existe en la base de datos.
    *
    * @return boolean
    */
    class UniqueRule extends Rule
    {
        protected $message = ":attribute :value ya existe en base de datos.";

        protected $fillableParams = ['table', 'column', 'except'];

        protected $con;

        public function __construct()
        {
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
            ]);
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
            // ======================================================
            $this->con = $capsule;
        }

        public function check($value): bool
        {
            // make sure required parameters exists
            $this->requireParameters(['table', 'column']);

            // getting parameters
            $column = $this->parameter('column');
            $table = $this->parameter('table');
            $except = $this->parameter('except');

            if ($except AND $except == $value) {
                return true;
            }

            $result = Capsule::table($table)->where($column,'=',$value)->count();
            return $result > 0 ? false : true;
        }
    }