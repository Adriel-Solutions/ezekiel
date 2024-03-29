<?php
    namespace native\libs;

    /**
     * This internal library is responsible for generating HTML
     * with handy functions. It proves useful for reusability concerns,
     * such as outputing forms, inputs, templates, etc.
     */
    class Render {

        private static function _debug_comment($path)
        {
            if(Options::get('MODE') !== 'DEBUG') return;

            echo "<!-- $path -->";
        }

        /**
         * Outputs the content of a Partial (HTML-in-PHP file)
         * and gives it parameters using a variable named $params
         *
         * @param {string} $key The plain name of the partial to retrieve, without extension
         * @param {array} $params The list of key-values to give to the partial if it's dynamic
         * @return {string} The result of calling `include partial.php` into a string
         */
        public static function partial($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_APP . "/views/partials/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        public static function native_partial($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_NATIVE . "/views/partials/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        public static function module_custom($module, $key, $params = [], bool $return = false) {
            $key = strtolower($key);
            $path = strtolower(DIR_APP . "/modules/$module/views$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();

            if($return)
                return $output;

            echo $output;
        }

        public static function module_partial($module, $key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_APP . "/modules/$module/views/partials/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        /**
         * Outputs the content of a Layout (HTML-in-PHP file)
         * and gives it parameters using a variable named $params
         *
         * @param {string} $key The plain name of the layout to retrieve, without extension
         * @param {array} $params The list of key-values to give to the layout if it's dynamic
         * @return {string} The result of calling `include layout.php` into a string
         */
        public static function layout($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_APP . "/views/layouts/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        public static function native_layout($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_NATIVE . "/views/layouts/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        /**
         * Outputs the content of a Component (HTML-in-PHP file)
         * and gives it parameters using a variable named $params
         *
         * @param {string} $key The plain name of the partial to retrieve, without extension
         * @param {array} $params The list of key-values to give to the partial if it's dynamic
         * @return {string} The result of calling `include partial.php` into a string
         */
        public static function component($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_APP . "/views/components/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }

        public static function native_component($key, $params = []) {
            $key = strtolower($key);
            $path = strtolower(DIR_NATIVE . "/views/components/$key.php");

            ob_start();
            self::_debug_comment($path);
            include $path;
            $output = ob_get_clean();
            echo $output;
        }
    }
