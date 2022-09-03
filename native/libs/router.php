<?php
    namespace native\libs;
    use Throwable;
    use Closure;

    /**
     * This internal library is responsible for routing the requests, and for calling
     * all the middlewares / controllers needed along the way, based only on the requested route from the end
     * user. It is the most fundamental core / backbone of the whole server.
     *
     * Note of implementation : Route parameters, Route prefixes, and Sub-routers are supported
     *                          Method matching is supported
     *                          Catch-all handlers are supported
     */
    class Router {

        // The list of routes defined for this router, organized by HTTP method
        private array $routes;

        // The route prefix for this router
        private string $routes_prefix;

        private null|Closure $error_handler;

        // The Router class is not supposed to be overwritten or inherited in any way
        public function __construct() {

            // All the routers are initialized with these HTTP methods by default
            // Every other method will be ignored
            $this->routes = [
                'OPTIONS' => [],
                'GET' => [],
                'POST' => [],
                'PUT' => [],
                'DELETE' => [],

                '_ERROR_' => []
            ];

            // Default route prefix for this router
            $this->routes_prefix = "";

            $this->error_handler = null;

            // Load overriden settings
            $this->load();
        }

        /**
         * Meant to be overriden by child classes
         * This is where a child Router class mounts its routes etc
         */
        protected function load() {}

        /**
         * Defines the route prefix for this router
         *
         * @param {string} $prefix
         */
        public function set_prefix($prefix) {
            $this->routes_prefix = $prefix;
        }

        /**
         * Appends an internal METHOD-ROUTE-HANDLERS association to this router
         * In other words, it defines that, when ROUTE is called by (HTTP) METHOD, HANDLERS are called
         *
         * @param {string} $method : The HTTP method
         * @param {string} $route : The requested route (supports regex)
         * @param {...mixed} $handlers : A variable list of handlers to call when the route's matched
         */
        private function append(string $method, string $route, Callable ...$handlers) : void {
            $route_bind = [$route];
            for($i = 0; $i < count($handlers); $i++)
                array_push($route_bind, $handlers[$i]);
            array_push($this->routes[$method], $route_bind);
        }

        /**
         * Simple alias for append('GET')
         */
        public function get(string $route, Callable ...$handlers) : void {
            $this->append('GET', $route, ...$handlers);
        }

        /**
         * Simple alias for append('POST')
         */
        public function post(string $route, Callable ...$handlers) : void {
            $this->append('POST', $route, ...$handlers);
        }

        /**
         * Simple alias for append('PUT')
         */
        public function put(string $route, Callable ...$handlers) : void {
            $this->append('PUT', $route, ...$handlers);
        }

        /**
         * Simple alias for append('DELETE')
         */
        public function delete(string $route, Callable ...$handlers) : void {
            $this->append('DELETE', $route, ...$handlers);
        }

        /**
         * Simple alias for calling append(...) with all the supported HTTP methods,
         * along with a match-all regex (.*) to match all the possible routes
         */
        public function use(Callable ...$handlers) : void {
            $pattern_start = empty($this->routes_prefix) ? "" : '(/';
            $pattern_end = empty($this->routes_prefix) ? ".*" : ".*)?";
            foreach($this->routes as $method => $route_binds)
                if ( $method === '_ERROR_' ) continue;
                else $this->append($method, $pattern_start . $pattern_end, ...$handlers);
        }

        public function set_error_handler(Closure $handler) : void {
            $this->error_handler = $handler;
        }
        public function get_error_handler() : null|Closure {
            return $this->error_handler;
        }

        /**
         * Returns the current router's prefix
         *
         * @return {string} The current router's prefix
         */
        public function get_prefix() : string {
            return $this->routes_prefix;
        }

        /**
         * Returns the current router's list of routes, mostly for debugging purposes
         *
         * @return {array<array>} The current router's routes
         */
        public function get_routes() : array {
            return $this->routes;
        }

        /**
         * Mounts a sub-router onto the current one
         * In other words, this enables one router to dispath requests to one or many other routers,
         * and this takes care of respecting the whole prefixes hierarchy on a per-router basis
         *
         * @param {Router} router : The router to mount
         */
        public function mount(Router $router) : void {
            // Mount sub-routes
            foreach($router->get_routes() as $method => $route_binds) {
                foreach($route_binds as $route_bind) {
                    $route = $route_bind[0];
                    $handlers = array_slice($route_bind, 1);

                    $this->append(
                        $method,
                        $router->get_prefix() . ($route === '/' ? '' : $route),
                        ...$handlers
                    );
                }
            }

            // Mount error handler like it were a route called by HTTP verb _ERROR_
            if(!empty($router->get_error_handler()))
                $this->append(
                    '_ERROR_',
                    $router->get_prefix() . '(/.*)?',
                    $router->get_error_handler()
                );
        }

        /**
         * The backbone of the server. This function is responsible for parsing the
         * request's information, creating appropriate Request and Response instances,
         * determining which Controllers and Middlewares to call, and call them sequentially
         * Every single request, if served by PHP, goes through this function
         */
        public function dispatch() : void {
            /**
             * Extract the critical information from the HTTP headers
             * (ip, http method, requested route)
             */
            $ip = Options::get('IS_BEHIND_PROXY') === true ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
            $method = strtoupper($_SERVER['REQUEST_METHOD']);
            $user_agent = !isset($_SERVER['HTTP_USER_AGENT']) ? 'Unknown' : $_SERVER['HTTP_USER_AGENT'];
            $route = $_SERVER['REQUEST_URI'] === "/" ? '/' : rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
            $uri = $_SERVER['REQUEST_URI'];

            // Store the headers lowercased
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);

            // Keep the raw body for signature calculations / other treatments
            $raw = file_get_contents('php://input');

            // Extract the remaining data (files, GET query parameters)
            $files = $_FILES;
            $query = $_GET;

            // Initialize / Retrieve the session
            $session = new Session();

            // Define req and res
            $req = new Request([
                'ip' => $ip,
                'user_agent' => $user_agent,
                'route' => $route,
                'uri' => $uri,
                'headers' => $headers,
                'method' => $method,
                'query' => $query,
                'files' => $files,
                'raw' => $raw,
                'session' => $session
            ]);

            $res = new Response([
                'route' => $route
            ]);

            // Entrypoint for the routing dispatch mechanism
            $prefix = $this->routes_prefix ?? "";
            $available_route_binds = $this->routes[$method];
            $triggered_handlers = [];

            // Loop through all the known routes of this router
            foreach($available_route_binds as $route_bind) {
                $available_route = $route_bind[0];
                $handlers = array_slice($route_bind, 1);

                // Create a regex from the internal route, to check if it matches later
                if($available_route === "/")
                    $regex = '^'.str_replace('/', '\\/', $prefix).'$';
                else
                    $regex = '^'.str_replace('/', '\\/', $prefix.$available_route).'$';

                // Turns the route params into standard regex parts
                // :route_param -> [a-ZA-Z0-9_-]+
                if(strpos($regex, ':') !== false)
                    $regex = preg_replace('/\:([a-zA-Z_]+)/', '(?<$1>[a-zA-Z0-9_-]+)', $regex);

                // Check if the requested route matches the current available route regex
                if(!preg_match('/'.$regex.'/', $route)) continue;

                // Try to extract the route params from the requested route (/route/38/sub-route -> 38)
                $params = [];
                preg_match_all('/'.$regex.'/', $route, $params);
                if(!empty($params)) {
                    array_shift($params);
                    foreach($params as $key => $value)
                        $req->params[$key] = $value[0];
                }

                // Append all the matched route's handlers to an array
                foreach($handlers as $handler)
                    array_push($triggered_handlers, $handler);
            }

            // If no route was found / no handler was associated with the matched route, exit
            if(empty($triggered_handlers)) return;

            // This boolean is passed by reference to each and every handler
            // When a handler decides to stop the waterfall execution, it sets $next to false
            $next = true;

            // Sequential call of all the matched route's handlers
            foreach($triggered_handlers as $handler){
                if(!$next) break;

                if($res->_is_sent) break;

                try {
                    $handler($req, $res, $next);
                } catch (Throwable $error) {
                    $next = false;
                    $this->dispatch_error($req, $res, $error);
                }
            }
        }

        private function dispatch_error(Request $req, Response $res, Throwable $error) : void {
            $method = '_ERROR_';
            $route = $req->route;

            // Entrypoint for the routing dispatch mechanism
            $prefix = $this->routes_prefix ?? "";
            $available_route_binds = $this->routes[$method];
            $triggered_handlers = [];

            // Loop through all the known routes of this router
            foreach($available_route_binds as $route_bind) {
                $available_route = $route_bind[0];
                $handlers = array_slice($route_bind, 1);

                // Create a regex from the internal route, to check if it matches later
                if($available_route === "/")
                    $regex = '^'.str_replace('/', '\\/', $prefix).'$';
                else
                    $regex = '^'.str_replace('/', '\\/', $prefix.$available_route).'$';

                // Turns the route params into standard regex parts
                // :route_param -> [a-ZA-Z0-9_-]+
                if(strpos($regex, ':') !== false)
                    $regex = preg_replace('/\:([a-zA-Z_]+)/', '(?<$1>[a-zA-Z0-9_-]+)', $regex);

                // Check if the requested route matches the current available route regex
                if(!preg_match('/'.$regex.'/', $route)) continue;


                // Append all the matched route's handlers to an array
                foreach($handlers as $handler)
                    array_push($triggered_handlers, $handler);
            }

            // Sequential call of all the matched route's handlers
            $next = true;
            foreach($triggered_handlers as $handler){
                if(!$next) break;

                $handler($req, $res, $next, $error);
            }
        }
    }
