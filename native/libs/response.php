<?php
    namespace native\libs;

    /**
     * This internal library is responsible for representing an HTTP response.
     * All across the project, this is the one and only way to output data to
     * the end user.
     */
    class Response {

        // Auto-generated ID for the response, if ever needed
        public $id;

        // Route associated with the current response, for better end-user readability
        public $route;

        // Auto-generated timestamp for the response, for better end-user readability
        public $date;

        // HTTP code for this response
        public $code;

        // HTTP status for this response
        public $status;

        // Associative array representing the data one wishes to return to the end user
        // This is used only when JSON is the output format
        public $content;

        // The auto-computed duration for sending back the response to the end-user
        // Only used for logging purposes
        public $duration;

        // The auto-computed size for the current response
        // Only used for logging purposes
        public $size;

        // Auto-generated timestamp for when the response was first instantiated
        // Only used for logging purposes
        private $_created_at;

        // Auto-generated timestamp for when the response was output to the end-user
        // Only used for logging purposes
        private $_sent_at;

        // Internal boolean to keep track of whether the response was sent already or not
        // If it's the case, every attempt to output something more will fail
        public $_is_sent;

        private $finish_handler;


        function __construct($params) {
            $this->id = uniqid();
            $this->route = $params['route'];
            $this->date = date('c');
            $this->code = Constants::$HTTP_SUCCESS_CODE;
            $this->status = Constants::$HTTP_SUCCESS_STATUS;
            $this->duration = 0;
            $this->size = 0;

            $this->_created_at = microtime(true);
            $this->_sent_at = null;
            $this->_is_sent = false;
        }

        private function call_finish_handler() {
            if(empty($this->finish_handler)) return;
            $handler = $this->finish_handler;
            $handler($this);
        }

        /**
         * Returns a JSON response to the end-user, following REST principles
         *
         * @param {array} params : An associative array of parameters
         *                         -> {int} code : The HTTP response code
         *                         -> {string} status : The HTTP status
         *                         -> {array} content : The extra data to send to the end-user
         *
         *  Outputs a generic :
         *  {
         *      "code" : "200",                       // or whatever
         *      "status" : "Success"                  // or whatever
         *      "route" : "/users/abcd"               // or whatever is the current requested route
         *      "date" : "2022-06-14T18:27:06+00:00"  // or whatever is the current ISO 8601 date
         *      "content" : "{ ... }"                 // or whatever is the current extra data
         *  }
         */
        public function send($params) {
            if($this->_is_sent) return;

            $this->code = strval($params['code'] ?? $this->code);
            $this->status = $params['status'] ?? $this->status;
            $this->content = $params['content'] ?? (object)[];

            header('Content-type: application/json');
            http_response_code($this->code);

            $output = json_encode([
                'route' => $this->route,
                'date' => $this->date,
                'code' => $this->code,
                'status' => $this->status,
                'content' => $this->content
            ], JSON_UNESCAPED_SLASHES);

            print($output);

            $this->_is_sent = true;
            $this->_sent_at = microtime(true);
            $this->duration = $this->_sent_at - $this->_created_at;
            $this->size = strlen($output);

            $this->call_finish_handler();
        }

        public function send_file($params = []) {
            if($this->_is_sent) return;

            $this->code = Constants::$HTTP_SUCCESS_CODE;
            $this->status = Constants::$HTTP_SUCCESS_STATUS;

            header('Content-type: ' . $params['type']);
            header('Content-Disposition: attachment; filename="'. $params['name'] .'"');
            header('Cache-Control: max-age=0');

            http_response_code($this->code);

            print($params['data']);

            $this->_is_sent = true;
            $this->_sent_at = microtime(true);
            $this->duration = $this->_sent_at - $this->_created_at;
            $this->size = strlen($params['data']);

            $this->call_finish_handler();
        }

        /**
         * Sends an HTTP header Location to redirect the end user
         *
         * @param {string} $url The absolute URL to redirect the end user to
         * @param {int} $code The HTTP code to use for the redirection, 302 by default
         */
        public function redirect($url, $code = 302) {
            if($this->_is_sent) return;

            $this->_is_sent = true;
            $this->_sent_at = microtime(true);
            $this->duration = $this->_sent_at - $this->_created_at;
            $this->size = 0;
            $this->code = $code;

            header("Location: $url", true, $code);

            $this->call_finish_handler();
        }

        /**
         * Outputs an HTML page (a.k.a View) to the end user
         *
         *
         * @param {array} params : An associative array of parameters
         *                         -> {int} code : The HTTP response code, 200 by default
         *                         -> {string} title : The SEO title, Sample page by default
         *                         -> {string} slug : The slug for the page, for CSS purposes, sample by default
         *                         -> {string} description : The SEO description for the page, Sample description by default
         *                         -> {string} module : The name of the module to take the views from
         *                         -> {bool} use_native_views : Where to take the skeletons views from (native by default)
         *                         -> {bool} use_native_skeletons : Where to take the page views from (native by default)
         *                         -> {array<array>} scripts : The list of JS scripts for the current page
         *                         -> {array<string>} styles : The list of CSS styles for the current page
         *                         -> {string} view : The name of the view, without the .php extension
         *                         -> {array} context : The data to pass to the view
         *
         */
        public function render($params = []) {
            if($this->_is_sent) return;

            // SEO-related
            $title = $params['title'] ?? get_option('SEO_DEFAULT_TITLE') ?? 'Sample page';
            $slug = $params['slug'] ?? slugify($title);
            $description = $params['description'] ?? get_option('SEO_DEFAULT_DESCRIPTION') ?? 'Sample description';

            // Assets-related
            $scripts = $params['scripts'] ?? [];
            $styles = $params['styles'] ?? [];

            // Output-related
            $use_native_skeletons = !isset($params['use_native_skeletons']) ? true : $params['use_native_skeletons'];
            $use_native_views = !isset($params['use_native_views']) ? false : $params['use_native_views'];
            $view = 'index';
            if(!empty($params['view']));
                $view = $params['view'];

            // Output data-injection-related
            $context = $params['context'] ?? [];
            $context['current_path'] = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) ?? '/';
            $context['current_slug'] = $slug;

            $code = $params['code'] ?? $this->code;
            $this->code = $code;

            http_response_code($code);
            header('Content-type: text/html');

            $skeletons_dir = ($use_native_skeletons ? DIR_NATIVE : DIR_APP) . '/views';
            $views_dir = ($use_native_views ? DIR_NATIVE : DIR_APP) . '/views';

            $module_views_dir = empty($params['module']) 
                ? '' 
                : DIR_APP . '/modules/' . $params['module'] . '/views';


            include $skeletons_dir . '/skeletons/html_open.php';
            include $skeletons_dir . '/skeletons/head_open.php';
            include $skeletons_dir . '/skeletons/head_content.php';
            include $skeletons_dir . '/skeletons/head_close.php';
            include $skeletons_dir . '/skeletons/body_open.php';

            if(empty($params['module']))
                include strtolower($views_dir . "$view.php");
            else
                include strtolower($module_views_dir . "$view.php");

            include $skeletons_dir . '/skeletons/body_close.php';
            include $skeletons_dir . '/skeletons/html_close.php';

            $this->_is_sent = true;

            $this->call_finish_handler();
        }

        public function raw($params = []) {
            if($this->_is_sent) return;

            $code = $params['code'] ?? $this->code;
            $content_type = $params['content_type'] ?? 'text/html';
            $output = $params['content'];

            http_response_code($code);
            header('Content-Type: ' . $content_type);
            print($output);

            $this->_is_sent = true;

            $this->call_finish_handler();
        }

        public function abort(int $code) {
            if($this->_is_sent) return;

            http_response_code($code);

            $this->_is_sent = true;

            $this->call_finish_handler();
        }


        /**
         * Handy HTTP Success response alias
         */
        public function send_success($params = []) {
            $default = [ 'code' => Constants::$HTTP_SUCCESS_CODE, 'status' => Constants::$HTTP_SUCCESS_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Not Found response alias
         */
        public function send_not_found($params = []) {
            $default = [ 'code' => Constants::$HTTP_NOTFOUND_CODE, 'status' => Constants::$HTTP_NOTFOUND_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Too Many Requests response alias
         */
        public function send_too_many_requests($params = []) {
            $default = [ 'code' => Constants::$HTTP_TOOMANYREQUESTS_CODE, 'status' => Constants::$HTTP_TOOMANYREQUESTS_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Unauthorized response alias
         */
        public function send_unauthorized($params = []) {
            $default = [ 'code' => Constants::$HTTP_UNAUTHORIZED_CODE, 'status' => Constants::$HTTP_UNAUTHORIZED_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Forbidden response alias
         */
        public function send_forbidden($params = []) {
            $default = [ 'code' => Constants::$HTTP_FORBIDDEN_CODE, 'status' => Constants::$HTTP_FORBIDDEN_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Conflictual State response alias
         */
        public function send_conflict($params = []) {
            $default = [ 'code' => Constants::$HTTP_CONFLICT_CODE, 'status' => Constants::$HTTP_CONFLICT_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Malformed Request response alias
         */
        public function send_malformed($params = []) {
            $default = [ 'code' => Constants::$HTTP_MALFORMED_CODE, 'status' => Constants::$HTTP_MALFORMED_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Handy HTTP Error 500 response alias
         */
        public function send_error($params = []) {
            $default = [ 'code' => Constants::$HTTP_ERROR_CODE, 'status' => Constants::$HTTP_ERROR_STATUS ];
            $this->send(array_merge($params, $default));
        }

        /**
         * Associate a handler with the response's end (aka, after it's been sent)
         */
        public function when_finished($handler) {
            $this->finish_handler = $handler;
        }
    }
