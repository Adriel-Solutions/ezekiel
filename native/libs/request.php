<?php
    namespace native\libs;

    use native\libs\Session;

    /**
     * This internal library is responsible for representing an HTTP request
     * in the project. It comes handy when dealing with headers, body, IP, and 
     * other concerns that could be tricky using plain PHP
     */
    class Request {

        // Auto-generated ID for the request, if ever needed
        public $id;

        // User Agent
        public $user_agent;

        // IP address
        public $ip;

        // Route called, without parameters nor domain, nor protocol
        public $route;

        // Headers, all lower-cased
        public $headers;

        // Route parameters, like in /users/:user_id <-- x
        public $params;

        // Query params, a.k.a GET parameters
        public $query;

        // Raw URI without domain and protocol
        public $uri;

        // HTTP method, like GET or POST or whatever
        public $method;

        // The parsed body of the request, if present, as an associative array
        public $body;

        // An alias for $_FILES
        public $files;

        // An alias for $_SESSION
        // Mostly used for view-rendering purpose
        public Session $session;

        // A state for the request, meant to hold data for working across multiple
        // controllers and middlewares. This is useful, for instance, for storing
        // the authenticated user for the current request
        // Mostly used for REST API purpose
        public $context;

        // Raw body string
        // Useful when performing HMAC validation 
        public $raw;

        function __construct($params) {
            $this->id = uniqid();
            $this->user_agent = $params['user_agent'];
            $this->ip = $params['ip'];
            $this->route = $params['route'];
            $this->headers = $params['headers'];
            $this->method = $params['method'];
            $this->body = [];
            $this->raw = $params['raw'];
            $this->uri = $params['uri'];
            $this->files = $params['files'];
            $this->session = $params['session'];
            $this->query = $params['query'];
            $this->params = [];
            $this->context = [];
        }

        /**
         * @TODO Reimplement, dispatch must be parametric
         */
        function forward(Router $from, string $new_route) {
            $this->route = $new_route;
            $from->redispatch($this);
        }
    }
