<?php

    namespace native\libs;

    use Monolog\Level;
    use Monolog\Logger as BaseLogger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Handler\FirePHPHandler;

    class Logger {
        private static BaseLogger $writer;

        public static function load() : void
        {
            self::$writer = new BaseLogger('app');

            $logs_dir = __DIR__ . '/../../storage/logs/app';
            foreach([ 'debug' , 'info' , 'notice' , 'warning' , 'error' , 'critical' , 'alert'] as $level)
                self::$writer->pushHandler(new StreamHandler($logs_dir . '/' . $level . '.log', Level::fromName(ucfirst($level))));
        }

        public static function debug($mixed, $content = []) { self::$writer->debug($mixed, $content); }
        public static function info($mixed, $content = []) { self::$writer->info($mixed, $content); }
        public static function notice($mixed, $content = []) { self::$writer->notice($mixed, $content); }
        public static function warning($mixed, $content = []) { self::$writer->warning($mixed, $content); }
        public static function error($mixed, $content = []) { self::$writer->error($mixed, $content); }
        public static function critical($mixed, $content = []) { self::$writer->critical($mixed, $content); }
        public static function alert($mixed, $content = []) { self::$writer->alert($mixed, $content); }
    }
