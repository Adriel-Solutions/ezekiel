<?php

    namespace native\libs;

    use Monolog\Level;
    use Monolog\Logger as BaseLogger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Handler\FirePHPHandler;

    class Logger {
        private static array $writers;

        public static function load() : void
        {
            self::$writers = [];
            $logs_dir = __DIR__ . '/../../storage/logs/app';
            foreach([ 'debug' ,'info' , 'notice' , 'warning' , 'error' , 'critical' , 'alert'] as $level) {
                self::$writers[$level] = new BaseLogger($level);
                $stream = new StreamHandler($logs_dir . '/' . $level . '.log', Level::fromName(ucfirst($level)));
                self::$writers[$level]->pushHandler($stream);
            }
        }

        public static function debug($mixed, $content = []) { self::$writers['debug']->debug($mixed, $content); }
        public static function info($mixed, $content = []) { self::$writers['info']->info($mixed, $content); }
        public static function notice($mixed, $content = []) { self::$writers['notice']->notice($mixed, $content); }
        public static function warning($mixed, $content = []) { self::$writers['warning']->warning($mixed, $content); }
        public static function error($mixed, $content = []) { self::$writers['error']->error($mixed, $content); }
        public static function critical($mixed, $content = []) { self::$writers['critical']->critical($mixed, $content); }
        public static function alert($mixed, $content = []) { self::$writers['alert']->alert($mixed, $content); }
    }
