<?php
    namespace native\libs;

    use PDO;

    /**
     * This internal library is responsible for connecting to the project's
     * database, and run SQL queries with PDO and prepared statements.
     */
    class Database {

        /**
         * Database instances
         * array<PDO>
         */
        public static array $dbs = [];
        private static PDO $current;

        private static function _pdo_param_type(string $php_type) : int
        {
            return [
                'integer' => PDO::PARAM_INT,
                'string' => PDO::PARAM_STR,
                'boolean' => PDO::PARAM_BOOL,
            ][$php_type];
        }

        public static function get_driver() : string
        {
            return self::$current->getAttribute(PDO::ATTR_DRIVER_NAME);
        }

        /**
         * Connects to the project's database using dotenv settings
         */
        public static function load() : void {
            $instances = array_map('strtoupper', explode(',', Options::get('ACTIVE_DBS')));

            // Loop through database nicknames
            foreach($instances as $instance) {
                // Extract settings using the database nickname for each parameter
                $credentials = [];
                foreach(['TYPE', 'HOST', 'NAME', 'USER', 'PASS'] as $key)
                    $credentials[$key] = Options::get(sprintf("DB_%s_$key", $instance));

                // Add a new PDO instance to our internal list
                self::$dbs[$instance] = new PDO(
                    $credentials['TYPE'] .
                    ':host='    . $credentials['HOST']  .
                    ';dbname='  . $credentials['NAME']  .
                    ';client_encoding=' . 'utf8',
                    $credentials['USER'],
                    $credentials['PASS'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ]
                );
            }

            // Set default database connection
            self::$current = self::$dbs['MAIN'];
        }


        public static function use(string $name) : void
        {
            self::$current = self::$dbs[strtoupper($name)];
        }

        /**
         * Executes a SQL request and returns its result
         *
         * @param {string} $sql The plain SQL query to run, with prepared parameters
         * @param {array} $params The list of params to feed to the prepared SQL query
         * @return {array} The result of the SQL query execution against the database
         */
        public static function query(string $sql, array $params = []) : array {
            $stmt = self::$current->prepare($sql);
            foreach($params as $k => $v)
                $stmt->bindValue(":$k", $v, self::_pdo_param_type(gettype($v)));
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function __destruct() {
            self::$current = null;
        }
    }
