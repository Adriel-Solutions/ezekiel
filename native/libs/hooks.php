<?php

    namespace native\libs;
    use Closure;

    class Hooks {
        private static array $table = [];

        public static function register(string $signal, Closure $handler) : void {
            if(!isset(self::$table[$signal]))
                self::$table[$signal] = [];

            self::$table[$signal][] = $handler;
        }

        public static function fire(string $signal, mixed &$params = null) : void {
            if(!isset(self::$table[$signal])) return;

            foreach(self::$table[$signal] as $handler)
                $handler($params);
        }
    }
