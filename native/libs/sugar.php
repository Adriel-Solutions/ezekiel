<?php
    use native\libs\Render;
    use native\libs\Options;
    use native\libs\I18n;
    use native\libs\Router;
    use native\libs\Adapter;
    use native\libs\Arrays;
    use native\libs\Service;
    use native\libs\Middleware;
    use native\libs\Controller;
    use native\libs\Job;
    use native\libs\Thirdparty;
    use native\libs\Constants;
    use native\libs\Hooks;
    use native\libs\Module;

    function ip() : string
    {
        $final_ip = "IP-UNKNOWN";

        $forwarded_ip = $_SERVER["HTTP_X_FORWARDED_FOR"] ?? "";
        $client_ip = $_SERVER["HTTP_CLIENT_IP"] ?? "";
        $cf_ip = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? "";
        $remote_ip = $_SERVER["REMOTE_ADDR"] ?? "";

        $all_ips = array_filter(explode(",", "$forwarded_ip,$client_ip,$cf_ip,$remote_ip"));
        foreach ($all_ips as $ip) {
            if ($final_ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
                break;
        }

        return $final_ip;
    }


    function vd(mixed ...$x) : void
    {
        echo '<pre>';
        var_dump($x);
        echo '</pre>';
    }

    /**
     * Turns MyClassName into my_class_name
     */
    function decamelize(string $str) : string
    {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $str));
    }

    /**
     * Turns \Namespace\Substuff\DeepPath\Class into Class
     */
    function without_namespace(string $str) : string
    {
        $tmp = explode('\\', $str);
        return end($tmp);
    }

    
    /**
     * Practical aliases
     * HC -> HTML Component -> Render::component
     * HP -> HTML Partial -> Render::partial
     * HMP -> HTML Module Partial -> Render::module_partial
     * HL -> HTML Layout -> Render::layout
     */
    function HC($key, $params = []) {
        Render::component($key, $params);
    }
    function HNC($key, $params = [], $return = false) {
        if($return) {
            ob_start();
            Render::native_component($key, $params);
            return ob_get_clean();
        }
        Render::native_component($key, $params);
    }
    function RHNC($key, $params = []) : string {
        ob_start();
        Render::native_component($key, $params);
        return ob_get_clean();
    }
    function HP($key, $params = []) {
        Render::partial($key, $params);
    }
    function HNP($key, $params = []) {
        Render::native_partial($key, $params);
    }
    function HMP($module, $key, $params = [], $return = false) {
        if($return) {
            ob_start();
            Render::module_partial($module, $key, $params);
            return ob_get_clean();
        }
        Render::module_partial($module, $key, $params);
    }
    function HL($key, $params = []) {
        Render::layout($key, $params);
    }
    function HNL($key, $params = []) {
        Render::native_layout($key, $params);
    }

    /**
     * Practical path generators
     */
    function api_path(string $path) : string {
        return Options::get('ROOT_API') . $path;
    }

    function front_path(string $path) : string {
        return Options::get('ROOT_FRONT') . $path;
    }

    function webhooks_path(string $path) : string
    {
        return Options::get('ROOT_WEBHOOKS') . $path;
    }

    function front_upload_path(string $path) : string {
        if(Options::get('STORAGE_S3_HOST'))
            return \native\thirdparties\S3::create_presigned_url_download($path);

        return Options::get('ROOT_STORAGE') . '/uploads' . $path ;
    }

    function cache_path(string $path) : string
    {
        return cache_folder() . $path . '.html';
    }

    function cache_folder() : string
    {
        return DIR_ROOT . '/storage/app/cache';
    }

    function front_asset_path(string $path) : string {
        return 
            Options::get('ROOT_ASSETS') 
            . $path 
            . (
                ( Options::get('MODE')  === 'PRODUCTION' )
                ? '?v=' . Options::get('ASSETS_VERSION') 
                : '' 
            );
    }

    function front_native_asset_path(string $path) : string {
        return 
            Options::get('ROOT_NATIVE_ASSETS') 
            . $path 
            . (
                ( Options::get('MODE')  === 'PRODUCTION' )
                ? '?v=' . Options::get('ASSETS_VERSION') 
                : '' 
            );
    }

    function front_module_asset_path(string $module, string $path) : string {
        return 
            Options::get('ROOT_MODULES_ASSETS') 
            . '/' . $module
            . $path 
            . (
                ( Options::get('MODE')  === 'PRODUCTION' )
                ? '?v=' . Options::get('ASSETS_VERSION') 
                : '' 
            );
    }

    function absolute_url(string $path) : string
    {
        return
            Options::get('ROOT_URL')
            . $path;
    }

    /**
     * WP-like simplified slugify function for internal matters
     */
    function slugify(string $str) : string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $str)));
    }

    /**
     * Output sanitization
     *
     * @param {string} $str The string to sanitize before output
     * @return {string} The sanitized string
     */
    function sanitize_before_output(?string $str) : string {
        if(!isset($str)) return "";
        return htmlentities($str, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Simple and handy alias for I18n::translate, with substitution support
     *
     * @param {string} $key The string to translate
     * @param {array} $params The list of parameters to substitue in the string, if needed
     * @return {string} The translated $key, substituted with $params if needed.
     */
    function __(string $key, mixed ...$params) : string {
        if(empty($params))
            return I18n::translate($key);

        return sprintf(I18n::translate($key), ...$params);
    }

    /**
     * Simple and handy alias for sanitize_before_output()
     */
    function _e(string|array|null $var) : string|array {
        if(empty($var))
            return "";

        if(is_array($var))
            return array_map(fn($v) => sanitize_before_output($v), $var);
        return sanitize_before_output($var);
    }

    /**
     * Alias meant to be used in front 
     */
    function get_option(string $key) : mixed 
    {
        return Options::get($key);
    }

    function get_constant(string $key) : mixed 
    {
        return Constants::$$key;
    }

    function get_locales() : array 
    {
        return I18n::get_supported_locales();
    }

    function fire_hook(string $signal, mixed &$params = null) : void 
    {
        Hooks::fire($signal, $params);
    }

    /**
     * Handy ways to create an instance of a [native] middleware / service / controller / adapter by using just its name
     */

    /**
     * Returns an instance of a [native|app] class instantiated with parameters
     *
     * @param {string} $type The category of class (middleware | adapter | service | controller | thirdparty)
     * @param {string} $name The class name
     * @param {string} $parameters The parameters to instantiate the class with
     * @return {object}
     */
    function _instantiate(string $namespace, string $category, string $name, array $parameters = []) : Adapter|Middleware|Controller|Router|Service|Thirdparty|Job {
        $class = '\\' . join('\\', [ 
            strtolower($namespace),
            strtolower($category),
            ucfirst($name)
        ]);
        $instance = new $class($parameters);
        return $instance;
    }

    function _instantiate_module($namespace, $module, $parameters = []) {
        $class = '\\' . strtolower($namespace) . '\\' . 'modules' . '\\' . strtolower($module) . '\\' . 'Module';
        $class = '\\' . join('\\', [ 
            strtolower($namespace),
            'modules',
            strtolower($module),
            'Module'
        ]);
        $instance = new $class($parameters);
        return $instance;
    }

    function _instantiate_from_module(string $namespace, string $module, string $category, string $name, array $parameters = []) : Adapter|Middleware|Controller|Router|Service|Thirdparty|Job {
        $name = strtolower($name);
        $name = str_replace('/', '\\', $name);

        if(str_contains($name, '\\')) {
            $parts = explode('\\', $name);
            $last_name = ucfirst(array_pop($parts));
            $name = join('\\', [ ...$parts , $last_name  ]);
        } else { 
            $name = ucfirst($name);
        }

        $class = '\\' . join('\\', [ 
            strtolower($namespace),
            'modules',
            strtolower($module),
            strtolower($category),
            $name
        ]);
        $instance = new $class($parameters);
        return $instance;
    }

    function native_mdw(string $name, array $parameters = [])    : Middleware    { return _instantiate('native', 'middlewares', $name, $parameters); }
    function native_ctrl(string $name, array $parameters = [])   : Controller    { return _instantiate('native', 'controllers', $name, $parameters); }
    function native_srvc(string $name, array $parameters = [])   : Service       { return _instantiate('native', 'services', $name, $parameters); }
    function native_adpt(string $name, array $parameters = [])   : Adapter       { return _instantiate('native', 'adapters', $name, $parameters); }
    function native_trdp(string $name, array $parameters = [])   : Thirdparty    { return _instantiate('native', 'thirdparties', $name, $parameters); }
    function native_router(string $name, array $parameters = []) : Router        { return _instantiate('native', 'routers', $name, $parameters); }
    function native_job(string $name, array $parameters = [])    : Job           { return _instantiate('native', 'jobs', $name, $parameters); }

    function mdw(string $name, array $parameters = [])    : Middleware    { return _instantiate('app', 'middlewares', $name, $parameters); }
    function ctrl(string $name, array $parameters = [])   : Controller    { return _instantiate('app', 'controllers', $name, $parameters); }
    function srvc(string $name, array $parameters = [])   : Service       { return _instantiate('app', 'services', $name, $parameters); }
    function adpt(string $name, array $parameters = [])   : Adapter       { return _instantiate('app', 'adapters', $name, $parameters); }
    function trdp(string $name, array $parameters = [])   : Thirdparty    { return _instantiate('app', 'thirdparties', $name, $parameters); }
    function router(string $name, array $parameters = []) : Router        { return _instantiate('app', 'routers', $name, $parameters); }
    function job(string $name, array $parameters = [])    : Job           { return _instantiate('app', 'jobs', $name, $parameters); }

    function module(string $name, array $parameters = []) : Module { return _instantiate_module('app', $name, $parameters); }

    function module_mdw(string $module, string $name, array $parameters = [])     : Middleware    { return _instantiate_from_module('app', $module, 'middlewares', $name, $parameters); }
    function module_ctrl(string $module, string $name, array $parameters = [])    : Controller    { return _instantiate_from_module('app', $module, 'controllers', $name, $parameters); }
    function module_srvc(string $module, string $name, array $parameters = [])    : Service       { return _instantiate_from_module('app', $module, 'services', $name, $parameters); }
    function module_adpt(string $module, string $name, array $parameters = [])    : Adapter       { return _instantiate_from_module('app', $module, 'adapters', $name, $parameters); }
    function module_trdp(string $module, string $name, array $parameters = [])    : Thirdparty    { return _instantiate_from_module('app', $module, 'thirdparties', $name, $parameters); }
    function module_router(string $module, string $name, array $parameters = [])  : Router        { return _instantiate_from_module('app', $module, 'routers', $name, $parameters); }
    function module_job(string $module, string $name, array $parameters = [])     : Job           { return _instantiate_from_module('app', $module, 'jobs', $name, $parameters); }

    function static_page(string $name) : array
    {
        $name = "\app\controllers\\$name";
        $class = new $name();
        return [ $class , 'render' ];
    }

    /**
     * Convenient way to create a "default" service for a given table
     */
    function default_service(string $table) : Service
    {
        $s = new Service();
        $s->set_table($table);
        return $s;
    }

    /**
     * Determine whether UI is installed or not
     */
    function is_enabled_ui() : bool
    {
        return file_exists(DIR_NATIVE . DIRECTORY_SEPARATOR . 'views/components');
    }

    /**
     * Array 
     */
    function array_find(array $arr, Closure $fn, $default = null) : mixed
    {
        return Arrays::find($arr, $fn, $default);
    }

    function array_flatten(array $arr) : array
    {
        return Arrays::flatten($arr);
    }

    function array_all(array $arr, Closure $fn) : bool
    {
        return Arrays::all($arr, $fn);
    }

    /**
     */
    function with(mixed $var, Closure $fn) : mixed
    {
        return $fn($var);
    }
