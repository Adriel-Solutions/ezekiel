<?php
    namespace native\libs;

    /**
     * Base class inherited by every Controller
     *
     * So far, its only role is to hold a list of Services and Adapters
     * identifier by an arbitrary name
     */
    class Controller {
        protected array $adapters;
        protected array $services;
        protected array $thirdparties;

        public function __construct() {
            $this->adapters = [];
            $this->services = [];
            $this->thirdparties = [];

            $this->load();
        }

        /**
         * @override
         */
        protected function load() {}

        /**
         * Adds an instance of Adapter to the internal list
         *
         * @param {string} $name : The name associated with the Adapter instance
         * @param {any} $instance : The instance of the Adapter to store
         */
        public function register(string $name, Adapter|Service|Thirdparty $instance) : void {
            $class = get_parent_class($instance);
            $internal_array = strtolower($class) . 's';
            $this->$internal_array[$name] = $instance;
        }

        /**
         * Turns GET parameters into SQL conditions
         *
         * @param {array} $schema : The schema to look for ($f => $operator)
         * @param {array} $params : The GET parameters ($k => $v)
         * @return {array<array>} : A list of SQL conditions @see _build_where_str
         */
        public function generate_sql_filters(array $schema, array $params) : array {
            $filters = [];

            foreach($params as $k => $v) {
                if(empty($schema[$k])) continue;
                if(empty($v)) continue;

                $filters[] = [
                    'column' => $k,
                    'operator' => $schema[$k],
                    'value' => in_array($schema[$k], ['LIKE', 'ILIKE']) ? "%$v%" : $v
                ];
            }

            return $filters;
        }

        /**
         * Turns GET parameters into a querystring
         *
         * @param {array} $schema : The schema to look for ($f => $operator)
         * @param {array} $params : The GET parameters ($k => $v)
         * @return {array<array>} : A GET querystring
         */
        public function generate_str_filters(array $schema, array $params) : string {
            $filters = [];

            foreach($params as $k => $v) {
                if(empty($schema[$k])) continue;

                $filters[] = $k . '=' . urlencode($v);
            }

            return join('&', $filters);
        }
    }
