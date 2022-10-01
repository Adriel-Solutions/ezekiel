<?php

    namespace native\facades;

    use native\collections\Records;
    use native\libs\Service as BaseService;
    use native\libs\Record;

    class Service {
        protected ?string $db = null;
        protected ?string $table = null;
        protected ?string $primary_key = 'pk';
        protected array $relations = [];
        protected array $schema = [];

        public function get_table() { return $this->table; }
        public function get_relations() { return $this->relations; }
        public function get_schema() { return $this->schema; }
        public function get_database() { return $this->db; }
        public function get_primary_key() { return $this->primary_key; }

        private static function call_base_service($function, &...$arguments) : mixed
        {
            $service = new BaseService(new static());
            return $service->$function(...$arguments);
        }

        public static function exists(int|string $id) : bool
        {
            return self::call_base_service('exists', $id);
        }

        public static function exists_one($conditions) : bool
        {
            return self::call_base_service('exists_one', $conditions);
        }

        public static function as_records() : BaseService {
            return self::call_base_service('as_records');
        }

        public static function get(int|string $id) : array|Record
        {
            return self::call_base_service('get', $id);
        }
        public static function get_many(array $page_parameters) : array|Records
        {
            return self::call_base_service('get_many', $page_parameters);
        }
        public static function get_all($page_parameters = []) : array|Records
        {
            return self::call_base_service('get_all', $page_parameters);
        }
        public static function get_count() : int
        {
            return self::call_base_service('get_count');
        }
        public static function find_one(array $conditions, bool $is_strict = true) : array|Record
        {
            return self::call_base_service('find_one', $conditions, $is_strict);
        }
        public static function find_many(array $conditions, array $page_parameters = []) : array|Records
        {
            return self::call_base_service('find_many', $conditions, $page_parameters);
        }
        public static function find_count(array $conditions, bool $is_strict = true) : int
        {
            return self::call_base_service('find_count', $conditions, $is_strict);
        }
        public static function find_all(array $conditions, array $page_parameters = []) : array|Records
        {
            return self::call_base_service('find_all', $conditions, $page_parameters);
        }
        public static function create(array $payload) : array|Record
        {
            return self::call_base_service('create', $payload);
        }
        public static function update(int|string $id, array $payload) : array|Record
        {
            return self::call_base_service('update', $id, $payload);
        }
        public static function delete(int|string $id) : void
        {
            self::call_base_service('delete', $id);
        }
        public static function find_and_update(array $conditions, array $payload) : array|Records
        {
            return self::call_base_service('find_and_update', $conditions, $payload);
        }
        public static function find_and_delete(array $conditions) : void
        {
            self::call_base_service('find_and_delete', $conditions);
        }
        public static function populate(&$object, string $field, array $conditions = [], array $page_parameters = []) : void
        {
            self::call_base_service('populate', $object, $field, $conditions, $page_parameters);
        }
        public static function populate_many(&$objects, string $field, array $conditions = [], array $page_parameters = []) : void
        {
            self::call_base_service('populate_many', $objects, $field, $conditions, $page_parameters);
        }
        public static function pluck(string $column, array $conditions = [], array $page_parameters = []) : array
        {
            return self::call_base_service('pluck', $column, $conditions, $page_parameters);
        }

    }
