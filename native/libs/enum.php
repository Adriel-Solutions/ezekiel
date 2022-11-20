<?php
    namespace native\libs;

    trait Enum {
        public static function fromName(string $name){
            return constant("self::$name");
        }
    }
