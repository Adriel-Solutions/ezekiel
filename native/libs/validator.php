<?php
    namespace native\libs;

    /**
     * This internal library is responsible for validating user input
     * It is and should be called exclusively from a Controller
     * Iodine-like
     */
    class Validator
    {
        // Validation rules
       
        /** --------------------------------------------------------------------------------------- **/

        public static function is_required($value) { return isset($value); }
        public static function is_not_empty($value) { return !empty($value); }
        public static function is_optional($value) { return $value === '' || !isset($value); }

        public static function is_min_length($value, $l) { return strlen($value) >= $l; }
        public static function is_max_length($value, $l) { return strlen($value) <= $l; }

        public static function is_string($value) { return is_string($value); }
        public static function is_regex($value, $r) { return is_string($value) && preg_match($r, $value); }
        public static function is_email($value) { return filter_var($value, FILTER_VALIDATE_EMAIL) !== false; }

        public static function is_date($value) { return is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) == true; }

        public static function is_boolean($value) { return $value === false || $value === true || in_array(strtolower(strval($value)), [ 'true', 'false', 'yes' , 'no', '0' , '1' ]); }

        public static function is_in($value, $a) { return in_array($value, explode(',', $a)); }
        public static function is_not_in($value, $a) { return !self::is_in($value, $a); }

        public static function is_numeric($value) { return is_numeric($value); }
        public static function is_min($value, $a) { return self::is_numeric($value) && $value >= $a; }
        public static function is_max($value, $a) { return self::is_numeric($value) && $value <= $a; }

        public static function is_unique($value, $table, $key) { 
            $service = new Service();
            $service->set_table($table);

            if($service->exists_one([ "$key" => $value ]))
                return false;

            return true; 
        }

        /** --------------------------------------------------------------------------------------- **/

        /**
         * Check a single value against a single rule
         *
         * @param {mixed} $value The supplied user input
         * @param {string} $rule The rule to check
         * @param {string} $key The named key associated with the value, if ever needed
         * @return {boolean} true when all the rule is passed, false otherwise
         */
        private static function _process_rule($value, $rule, $key = null) {
            $actual_rule = null;
            $rule_argument = null;

            $is_parametric = str_contains($rule, ':') ? true : false;

            // Rule = name:param
            if($is_parametric) {
                $parts = explode(':', $rule);
                $actual_rule = $parts[0];
                $rule_argument = $parts[1];
            } 
            // Simple rule
            else
                $actual_rule = $rule;

            $function = "is_$actual_rule";

            $is_valid = self::$function($value, $rule_argument, $key);

            if(!$is_valid) return false;

            return true;
        }


        /** --------------------------------------------------------------------------------------- **/

        // Exposed validation methods
        
        /** --------------------------------------------------------------------------------------- **/

        /**
         * Check a single value against a set of rules (aka a schema)
         *
         * @param {mixed} $value The supplied user input
         * @param {array} $schema The set of rules to check against the payload
         * @param {string} $key The named key associated with the value, if ever needed
         * @return {boolean} true when all the rules are passed, and false otherwise
         */
        public static function is_valid($value, $rules, $key = null) {
            if(empty($rules)) return true;

            foreach($rules as $rule) {
                $is_valid = self::_process_rule($value, $rule, $key);

                // Skip all rules if value is both optional and absent
                if($rule === 'optional' && $is_valid)
                    return true;

                // Do not consider optional as an error, to keep working on next rules
                if($rule === 'optional' && !$is_valid)
                    continue;

                if(!$is_valid) return false;
            }

            return true;
        }

        /**
         * Check a payload (list of key-values) against a set of rules (aka a schema)
         *
         * @param {&array} $payload The user input
         * @param {array} $schema The set of rules to check against the payload
         * @return {boolean} true when all the rules are passed, and false otherwise
         */
        public static function is_valid_schema($payload, $schema) {
            if(empty($schema)) return true;
            if(empty($payload)) return false;

            $keys = array_keys($schema);

            foreach($keys as $key) {
                $rules = $schema[$key];

                if(empty($rules)) continue;

                if(!isset($payload[$key]))
                    $value = NULL;
                else
                    $value = $payload[$key];

                if(!self::is_valid($value, $rules, $key)) return false;
            }

            return true;
        }

        /**
         * Remove unnecessary fields from an input passed by parameter
         * Assumption made : The schema + payload were first validated prior to calling enforce_schema
         *
         * @param {&array} $payload The user input
         * @param {array} $schema The set of rules to check against the payload
         */
        public static function enforce_schema(&$payload, $schema) {
            foreach($payload as $key => $value) {
                if(isset($schema[$key])) continue;
                unset($payload[$key]);
            }
        }

        /**
         * Finds all the errors in a payload, using a schema
         * Assumption made : The schema + payload were first validated prior to calling troubleshoot
         *
         * @param {&array} $payload The user input
         * @param {array} $schema The set of rules to check against the payload
         * @return {array} An array that associates each key with their errors
         */
        public static function troubleshoot_schema($payload, $schema) {
            $keys = array_keys($schema);

            $errors = [];
            foreach($keys as $key) {
                $rules = $schema[$key];

                if(empty($rules)) continue;

                if(!isset($payload[$key]))
                    $value = NULL;
                else
                    $value = $payload[$key];

                foreach($rules as $rule) {
                    $is_valid = self::_process_rule($value, $rule, $key);

                    // Skip all rules if value is both optional and absent
                    if($rule === 'optional' && $is_valid) break;

                    // Do not consider optional as an error, to keep working on next rules
                    if($rule === 'optional' && !$is_valid) continue;

                    if($is_valid) continue;

                    if(!isset($errors[$key])) $errors[$key] = [];

                    $errors[$key][] = $rule;
                }
            }

            return $errors;
        }
    }
