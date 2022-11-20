<?php

    namespace native\libs;

    use native\libs\Arrays;
    use Exception;

    class Dialect {
        private string $table;

        private static bool $is_encryption_enabled;
        private static bool $encryption_key;

        public function __construct(string $table)
        {
            self::$is_encryption_enabled = Options::get('ENCRYPTION_ENABLED');
            self::$encryption_key = Options::get('ENCRYPTION_KEY');
            $this->table = $table;
        }

        public function normalize_conditions(array $conditions) : array {
            if(empty($conditions)) return [];
            if(Arrays::is_multi($conditions)) return $conditions;

            $new_conditions = [];
            foreach($conditions as $k => $v) {
                $new_conditions[] = [
                    'column' => $k,
                    'operator' => is_array($v) ? 'IN' : '=',
                    'value' => $v
                ];
            }

            return $new_conditions;
        }

        protected function normalize_conditions_values(array $conditions) : array {
            $new_conditions = [  ];

            foreach($conditions as $c) {
                if(!is_null($c['value'])) { 
                    $new_conditions[] = $c;
                    continue;
                }

                $c['value'] = '[NULL]';
                $c['operator'] = 'IS';

                $new_conditions[] = $c;
            }

            return $new_conditions;
        }

        protected function _auto_alias(array $conditions) : array {
            $columns = [  ];
            $new_conditions = [  ];

            foreach($conditions as $c) {
                $new_condition = $c;

                // Developer-supplied alias -> Nothing to do
                if(!empty($c['alias'])) {
                    $new_conditions[] = $new_condition;
                    continue;
                }

                // Unknown column -> Store it
                if(empty($columns[$c['column']])) {
                    $columns[$c['column']] = 1;
                    $new_conditions[] = $new_condition;
                    continue;
                }

                // Known column -> Create an alias
                $columns[$c['column']] += 1;
                $new_condition['alias'] = $c['column'] . '_' . $columns[$c['column']];
                $new_conditions[] = $new_condition;
            }

            // Raw SQL -> alias
            $idx = 0;
            foreach($new_conditions as &$nc) {
                if(!str_starts_with($nc['column'], '['))
                    continue;

                $nc['alias'] = 'computed_value_' . $idx++;
            }

            return $new_conditions;
        }

        /**
         * Apply extra treatment on user-supplied values before inserting/updating rows
         * At the moment, that is used only to turn booleans into 1s and 0s
         */
        public function normalize_payload(array $payload) : array {
            if ( empty($payload) ) return [];

            $new_payload = [];

            foreach($payload as $k => $v) {
                $new_value = $v;

                if ( $v === false )
                    $new_value = 0;

                if ( $v === true )
                    $new_value = 1;

                $new_payload[$k] = $new_value;
            }

            return $new_payload;
        }

        /**
         * Generates 'alias' key-value in conditions schema when two
         * columns have the same name and could collide
         * Assumption : supplied $conditions are already normalized
         */
        public function auto_alias(array $conditions) : array {
            $columns = [  ];
            $new_conditions = [  ];

            foreach($conditions as $c) {
                $new_condition = $c;

                // Developer-supplied alias -> Nothing to do
                if(!empty($c['alias'])) {
                    $new_conditions[] = $new_condition;
                    continue;
                }

                // Unknown column -> Store it
                if(empty($columns[$c['column']])) {
                    $columns[$c['column']] = 1;
                    $new_conditions[] = $new_condition;
                    continue;
                }

                // Known column -> Create an alias
                $columns[$c['column']] += 1;
                $new_condition['alias'] = $c['column'] . '_' . $columns[$c['column']];
                $new_conditions[] = $new_condition;
            }

            return $new_conditions;
        }

        /**
         * Generates a WHERE string given a set of conditions
         * Note of implementation : All the conditions are joined by the -AND clause
         *
         * @param {array<array>} $conditions A list of conditions, such that
         *                       -> column : The name of the column to test
         *                       -> operator : The operator to use for the test, = by default
         *                       -> value : The expected value. Using [XXX] will escape XXX
         *                       -> alias : What to rename the prepared :parameter if needed
         * @param {boolean} $is_strict Whether to use AND or OR for joining conditions
         * @param {boolean} $is_decrypted Whether to wrap every column with pgp_sym_decrypt
         *
         *  @return {string} The full WHERE clause, without the -WHERE keyword
         */
        public function build_where_str(array $conditions, bool $is_strict = true, bool $include_where_keyword = true) : string {
            if(empty($conditions)) return "";

            $base = $include_where_keyword ? ' WHERE  ' : ' ';
            $conditions_strs = [];

            $has_to_be_decrypted = self::$is_encryption_enabled === true ? true : false;

            // If conditions are not expressed as an array of arrays, normalize them
            // [ $key => $value ] becomes the format described in the documentation
            $conditions = $this->normalize_conditions($conditions);
            $conditions = $this->normalize_conditions_values($conditions);
            $conditions = $this->auto_alias($conditions);

            // Process the conditions
            foreach($conditions as $c) {
                // Column is an SQL expression, example : LENGTH(col)
                if(str_starts_with($c['column'], '[')) {
                    if(self::$is_encryption_enabled)
                        throw new Exception('SQL expressions are not supported when encryption is enabled');
                    else
                        $c['column'] = str_replace([ '[' , ']' ], '', $c['column']);
                }

                if ( $has_to_be_decrypted )
                    $c_str = $this->build_decrypted_column_str($c['column']);
                else
                    $c_str = $c['column'];

                $column = empty($c['alias']) ? $c['column'] : $c['alias'];

                $c_str .= ' ';

                if(!empty($c['operator']))
                    $c_str .= $c['operator'];
                else
                    $c_str .= '=';

                $c_str .= ' ';

                // Value = Array
                if(is_array($c['value'])) {
                    $c_str .= '(';

                    $indexed_placeholders = [];
                    foreach($c['value'] as $idx => $v)
                        $indexed_placeholders[] = ':' . $column . '_' .$idx;
                    $c_str .= join(', ', $indexed_placeholders);

                    $c_str .= ')';
                }
                // Value = [SQL_STUFF]
                elseif(str_starts_with($c['value'], '['))
                    $c_str .= str_replace([ '[' , ']' ], '', $c['value']);
                // Value = Literal
                else {
                    $c_str .= ' :' ;

                    // table.column
                    if(str_contains($column, '.'))
                        $c_str .= str_replace('.', '_', $column);
                    // column
                    else
                        $c_str .= $column;
                }

                $conditions_strs[] = $c_str;
            }

            $conditions_joiner = $is_strict ? ' AND ' : ' OR ';
            $where_str = $base . join($conditions_joiner, $conditions_strs);
            return $where_str;
        }

        /**
         * Generates a SET string given a set of key-values
         *
         * @param {array}  $fields An array of key-values to put in the SET SQL clause
         *
         *  @return {string} The full STR clause, without the -SET keyword
         */
        public function build_set_str(array $fields, string $placeholder_prefix = "") : string {
            $base = '  ';

            $fields_strs = [];

            // Payload described as an array of key-values
            foreach($fields as $k => $v) {
                $f_str = $k;
                $f_str .= ' ';
                $f_str .= '=';
                $f_str .= ' ';

                $placeholder = null;

                // Value = [SQL_STUFF]
                if(str_starts_with($v, '['))
                    $placeholder = str_replace([ '[' , ']' ], '', $v);
                // Value = Literal
                else {
                    $placeholder .= ' :' ;

                    if ( !empty($placeholder_prefix) )
                        $k = $placeholder_prefix . $k;

                    // table.column
                    if( str_contains($k, '.') )
                        $placeholder .= str_replace('.', '_', $k);
                    else
                        $placeholder .= $k;
                }

                // Intentional SQL cast to text
                if ( self::$is_encryption_enabled )
                    // Value = [SQL_STUFF]
                    if ( str_starts_with($v, '[') )
                        throw new Exception('SQL expressions are not supported when encryption is enabled');
                    // Value = Literal
                    else
                        $f_str .= $this->build_encrypted_column_str("$placeholder::text");
                else
                    $f_str .= $placeholder;

                $fields_strs[] = $f_str;
            }

            $set_str = $base . join(',', $fields_strs);
            return $set_str;
        }

        public function build_page_str(array $params = []) : string {
            if(empty($params['per_page']) || empty($params['page']))
                return "";

            return "LIMIT :per_page OFFSET :page_shift";
        }

        public function build_page_payload(array $params) : array {
            if(empty($params['per_page']) || empty($params['page']))
                return [];

            $per_page = $params['per_page'];
            $page_shift = $per_page * ($params['page'] - 1);

            return [ 'per_page' => (int) $per_page , 'page_shift' => (int)$page_shift ];
        }

        /**
         * Generates a query "payload" given a set of conditions with their values
         * Payload is just an obnoxious way to call an associative array of key-values
         * This function just takes care of escaping SQL when needed, or applying specific treatments
         *
         * @param {array<array>} $conditions See build_where_str
         * @return {array} The list of key-values to further fill the prepared statement in the WHERE clause
         */
        public function build_query_payload(array $conditions, string $placeholder_prefix = "") : array {
            if(empty($conditions)) return [];

            $payload = [];

            // If conditions are not expressed as an array of arrays, normalize them
            // [ $key => $value ] becomes the format described in the documentation
            $conditions = $this->normalize_conditions($conditions);
            $conditions = $this->normalize_conditions_values($conditions);
            $conditions = $this->auto_alias($conditions);

            foreach($conditions as $c) {
                // Value = [SQL_STUFF]
                if(is_string($c['value']) && str_starts_with($c['value'], '['))
                    continue;

                $column = !empty($c['alias']) ? $c['alias'] : $c['column'];

                if(!empty($placeholder_prefix))
                    $column = $placeholder_prefix . $column;

                // Value = Array
                if(is_array($c['value'])) {
                    foreach($c['value'] as $idx => $v)
                        $payload['' . $column . '_' . $idx] = $v;

                    continue;
                }

                // Value = Literal
                if(str_contains($column, '.'))
                    $payload[str_replace('.', '_', $column)] = $c['value'];
                else
                    $payload[$column] = $c['value'];
            }

            $payload = $this->normalize_payload($payload);

            return $payload;
        }

        /**
         * Generates an ORDER BY clause
         *
         * @param {array} $params A list of column => order (ASC / DESC)
         * @return {string} A full ORDER BY clause
         */

        public function build_order_str(array $params) : string {
            $order = $params['order'] ?? [];

            if(empty($order)) return "";

            $base_str = "ORDER BY ";
            $order_strs = [];

            foreach($order as $k => $v)
                $order_strs[] = "$k $v";

            return $base_str . join(', ' , $order_strs);
        }

        public function build_encrypted_column_str(string $column) : string {
            $key = self::$encryption_key;
            return "pgp_sym_encrypt($column, '$key')";
        }

        public function build_encrypted_placeholder_str(string $placeholder) : string {
            $key = self::$encryption_key;
            return "pgp_sym_encrypt(:$placeholder, '$key')";
        }

        public function build_decrypted_column_str(string $column) : string {
            $key = self::$encryption_key;
            return "pgp_sym_decrypt($column, '$key')";
        }

        public function build_decrypted_placeholder_str(string $placeholder) : string {
            $key = self::$encryption_key;
            return "pgp_sym_decrypt(:$placeholder, '$key')";
        }

        public function get_primary_key() : string {
            if ( self::$is_encryption_enabled )
                return $this->build_decrypted_column_str($this->primary_key);
            return $this->primary_key;
        }

        /**
         * Query the table associated with the current Service to retrieve all the columns
         * and return a comma-separated list of these, with proper calls to pgp_sym_decrypt
         * That is supposed to produce a perfect replacement for the native '*' of SQL, except
         * that it takes care of decrypting data
         *
         * @return {string} A comma-separated list of columns wrapped inside a pgp_sym_decrypt()
         */
        public function build_decrypted_table_columns_str(string $table = "", string $prefix = "") : string {
            $encryption_key = self::$encryption_key;
            $table = $this->table;

            $rows = Database::query(
                                    "SELECT column_name
                                     FROM information_schema.columns
                                     WHERE table_name = :table",
                                    [ 'table' => $table ]
            );
            $columns = array_column($rows, 'column_name');

            $str = join(
                ', ',
                array_map(
                    function($cn) use ($encryption_key, $prefix) {

                        return empty($prefix)
                            ? "pgp_sym_decrypt($cn, '$encryption_key') AS $cn"
                            : "pgp_sym_decrypt($prefix.$cn, '$encryption_key') AS $cn";
                    },
                    $columns
                )
            );

            return $str;
        }

        public function build_returned_columns_str(string $prefix = "") : string {
            if ( !self::$is_encryption_enabled )
                return '*';
            return $this->build_decrypted_table_columns_str($this->table, $prefix);
        }
    }
