<?php
    namespace native\middlewares;
    use native\libs\Middleware;
    use native\libs\Options;

    /**
     * Assumption made : The request url parameters injection was done already by a Router
     */
    class Id extends Middleware {
        // Whether the error should be shown as coming from the API, or from Frontend
        // One of : FRONT / API
        private $for;

        // Redirect route in case the supplied ID does not have the proper format
        // Only used for FRONT
        // Defaults to : /
        private $fallback;

        public function __construct($params) {
            // Default
            $this->for = 'front';
            $this->fallback = front_path('/');

            // Merge
            foreach([  'for' , 'type' , 'fallback' ] as $k) {
                if(!isset($params[$k])) continue;
                $this->$k = $params[$k];
            }
        }

        public function __invoke($req, $res, &$next) {
            // Good Case 1 : Encryption enabled -> No test can be done, ids are UUIDs
            // Potential @TODO : Test UUID format
            if(Options::get('ENCRYPTION_ENABLED') === true)
                return;

            $keys = array_keys($req->params);
            $keys = array_filter($keys, fn($k) => str_contains($k, 'id'));

            foreach($keys as $k) {
                $id = $req->params[$k];

                // Good Case 2 : Encryption disabled -> ids must be integers
                if(is_numeric($id))
                    continue;

                // Wrong Case ->
                if('front' === strtolower($this->for))  
                    $res->redirect($this->fallback);
                elseif('api' === strtolower($this->for))
                    $res->send_malformed();

                $next = false;
                break;
            }

        }
    }



