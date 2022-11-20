<?php
    namespace native\libs;
    use Closure;

    class Arrays {

        /**
         * Retrieve an array's value via dot-notation path
         * For instance : $array['business.address.zip']
         * Becomes : $array['business']['address']['zip']
         */
        public static function get(array $arr, string $path, mixed $default = null) : mixed {
            if(empty($arr)) return $default;
            if(empty($path)) return $default;

            $parts = explode('.', $path, 2);
            $first_part = $parts[0];
            $rest = $parts[1] ?? null;

            if(!empty($arr[$first_part]) && empty($rest))
                return $arr[$first_part];

            if(empty($arr[$first_part]) && empty($rest))
                return $default;

            return self::get($arr[$first_part], $rest);
        }

        /**
         * Run a filter function against each element of an array and returns
         * whether at least one element returns true through the filter function
         */
        public static function any(array $arr, Closure $fn) : bool {
            if(empty($arr)) return false;
            if(empty($fn)) return false;

            foreach($arr as $e) {
                $result = $fn($e);

                if($result === true) return true;
            }

            return false;
        }

        /**
         * Run a filter function against each element of an array and returns
         * whether all the elements satisfy the filter or not
         */
        public static function all(array $arr, Closure $fn) : bool {
            if(empty($fn)) return true;
            if(empty($arr)) return false;

            foreach($arr as $e) {
                $result = $fn($e);

                if($result === false) return false;
            }

            return true;
        }

        /**
         * Return the first element of an array, that satisfies a filter function
         */
        public static function find(array $arr, Closure $fn, mixed $default = null) : mixed {
            if(empty($arr)) return $default;
            if(empty($fn)) return $default;

            foreach($arr as $e) {
                $result = $fn($e);

                if($result === true) return $e;
            }

            return $default;
        }

        /**
         * Determine whether the given array is a simple key => value table
         * or an array of arrays, nothing fancy
         */
        public static function is_multi(array $arr) : bool {
            if(empty($arr)) return false;
            if(self::is_associative($arr)) return false;

            foreach($arr as $k => $v)
                if(!is_array($v))
                    return false;

            return true;
        }

        /**
         * Determine whether the given array is a key => value table
         * or an integer-indexed array
         */
        public static function is_associative(array $arr) : bool {
            if (array() === $arr) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }

        public static function blacklist(array $arr, array $excluded) : array {
            if(empty($arr)) return [];
            if(empty($excluded)) return $arr;

            if ( self::is_associative($arr) )
                return array_diff_key($arr, array_flip($excluded));

            $new = [];
            foreach($arr as $v)
                if(!in_array($v, $excluded))
                    $new[] = $v;

            return $new;
        }

        public static function whitelist(array $arr, array $allowed) : array {
            if(empty($arr)) return [];
            if(empty($allowed)) return [];

            if ( self::is_associative($arr) )
                return array_intersect_key($arr, array_flip($allowed));

            $new = [];
            foreach($arr as $v)
                if(in_array($v, $allowed))
                    $new[] = $v;

            return $new;
        }

        public static function combine(array $arr, string $column_key, string $column_value) : array {
            return array_combine(
                array_column($arr, $column_key),
                array_column($arr, $column_value),
            );
        }

        public static function flatten(array $arr) : array
        {
            return array_merge(...$arr);
        }
    }
