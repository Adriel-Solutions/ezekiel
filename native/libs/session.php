<?php
    namespace native\libs;
    
    /**
     * This internal library is a simple wrapper over the existing $_SESSION
     * It's just a convenient and " good-looking " way to access the native array
     */
    class Session {
        public function __construct() {
            session_start();
        }

        public function get(string $key, mixed $default = null) : mixed {
            if(!$this->has($key)) return $default;
            return $_SESSION[$key];
        }

        public function set(string $key, mixed $value) : void {
            $_SESSION[$key] = $value;
        }

        public function unset(string $key) : void {
            if($this->has($key))
                unset($_SESSION[$key]);
        }

        public function has(string $key) : bool {
            return array_key_exists($key, $_SESSION);
        }

        public function clear() : void {
            session_unset();
        }

        public function destroy() : void {
            $this->clear();
            session_destroy();
        }
    }
