<?php

    use native\libs\Middleware;

    class Authorization extends Middleware {
        // The key where to find the value for the user role
        // Defaults to : POWER
        private $field;

        // The list of allowed roles
        // Defaults to an empty array
        private $roles;

        // Whether the error should be shown as coming from the API, or from Frontend
        // One of : FRONT / API
        // Defaults to : FRONT
        private $for;

        public function __construct($params) {
            // Default
            $this->field = 'power';
            $this->for = 'front';
            $this->roles = [];

            // Merge
            foreach([ 'field' , 'roles' , 'for'] as $k) {
                if(!isset($params[$k])) continue;
                $this->$k = $params[$k];
            }
        }

        /**
         * Assumption made : The Authentication Middleware is called prior
         *                   and $context holds a User
         */
        public function __invoke($req, $res, &$next) {
            $user = $req->context['user'];

            $role = strtolower($user[$this->field]);
            $allowed = array_map('strtolower', $this->roles);

            if(in_array($role, $allowed)) return;

            if('api' === strtolower($this->for))
                $res->send_unauthorized();
            elseif ('front' === strtolower($this->for))
                $res->redirect(front_path('/sign-in'));

            $next = false;
        }
    }
