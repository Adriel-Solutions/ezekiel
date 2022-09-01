<?php
    namespace native\libs;

    use PDO;

    /**
     * This internal library is responsible for connecting to the project's
     * database, and run SQL queries with PDO and prepared statements.
     */
    class Database {

        /**
         * Database instance
         */
        public static PDO $db;

        /**
         * Connects to the project's database using dotenv settings
         */
        public static function load() : void {
            self::$db = new PDO('pgsql:host=' . Options::get('DB_HOST')  .
                                  ';dbname='  . Options::get('DB_NAME') .
                                  ';client_encoding=' . 'utf8',
                                                Options::get('DB_USER') ,
                                                Options::get('DB_PASS') ,
                                              [
                                                  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                              ]
                        );
        }

        /**
         * Executes a SQL request and returns its result
         *
         * @param {string} $sql The plain SQL query to run, with prepared parameters
         * @param {array} $params The list of params to feed to the prepared SQL query
         * @return {array} The result of the SQL query execution against the database
         */
        public static function query(string $sql, array $params = []) : array {
            $stmt = self::$db->prepare($sql);
            $stmt->execute($params ?? []);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function __destruct() {
            self::$db = null;
        }
    }
