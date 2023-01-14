<?php
    namespace native\libs;

    use native\collections\Services;
    use native\collections\Thirdparties;
    use native\collections\Adapters;

    /**
     * Base class inherited by every Controller
     *
     * So far, its only role is to hold a list of Services and Adapters
     * identifier by an arbitrary name
     */
    class Controller {
        protected Adapters $adapters;
        protected Services $services;
        protected Thirdparties $thirdparties;

        public function __construct() {
            $this->adapters = new Adapters();
            $this->services = new Services();
            $this->thirdparties = new Thirdparties();

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
            $parent_class = get_parent_class($instance);
            $base_class = get_class($instance);

            $class = empty($parent_class) ? $base_class : $parent_class;
            $parts = explode('\\', $class);
            $class = end($parts);

            if(str_ends_with($class, 'y'))
                $class = substr($class, 0, strlen($class) - 1) . 'ie';

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
                    'operator' => $v !== '[NULL]' ? $schema[$k] : 'IS',
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
