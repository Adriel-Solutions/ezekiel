<?php
    namespace native\libs;

    /**
     * This internal library is responsible for loading the project-wide 
     * configuration that's stored in a dotenv file in /configuration/.xxx.env
     * All in all, it is a minimal dotenv parser
     */
    class Options {

        // Project-wide settings, stored as an associative array
        private static array $fields;

        /**
         * Reads the project's dotenv file and stores it into self::$fields
         * @param {string} $filepath : The path of the dotenv file to load and parse
         */
        public static function load(string $filepath) : void {
            $lines = file($filepath);

            foreach($lines as $line) {

                // Support for commands starting with #
                if(str_starts_with($line, '#')) continue;

                // Empty lines skip
                if(empty($line)) continue;

                $extracts = [];

                // Try to match the pattern KEY="VALUE"
                preg_match('/([a-zA-Z_]+)="([^"]+)"/', $line, $extracts);

                // If unmatched, skip the line
                if(empty($extracts)) continue;

                $left = $extracts[1];
                $right = $extracts[2];

                if(empty($left)) continue;
                if(empty($right)) continue;

                if( in_array( strtolower($right) , [ 'true' , 'false' ] ) ) 
                    $right = filter_var($right , FILTER_VALIDATE_BOOLEAN);

                self::$fields[$left] = $right;
                putenv("$left=$right");
                $_ENV["$left"] = $right;
            }
        }

        /**
         * Reads extra settings stored in database and stores them in self::$fields
         */
        public static function load_from_database() : void {
            $service = new Service();
            $service->set_table('settings');

            $settings = $service->get_all();

            foreach($settings as $s) {
                $key = $s['name'];
                $type = $s['type'];
                $value = $s['value'];

                settype($value, $type);

                self::$fields[$key] = $value;
                putenv("$key=$value");
                $_ENV["$key"] = $value;
            }
        }

        /**
         * Overwrites one or many settings field(s)
         *
         * @param {array} The new key-value associations
         */
        public static function overwrite($payload) {
            if(empty($payload))
                return;

            foreach($payload as $k => $v)
                self::$fields[$k] = $v;
        }

        /**
         * Returns the value of a given field, and an empty string if not found
         * @param {string} $field : The key of the field to retrieve the value of
         * @return {string} The value associated with $field, and an empty string if absent
         */
        public static function get($field) {
            return self::$fields[$field] ?? '';
        }
    }
