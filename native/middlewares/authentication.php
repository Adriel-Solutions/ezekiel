<?php

    namespace native\middlewares;

    use native\libs\Middleware;
use native\libs\Request;
use native\libs\Response;
use native\libs\Service;

    class Authentication extends Middleware {
        // Type of authentication 
        // One of : API / SESSION 
        private $type;

        // Table to look for credentials
        private $table;

        // Case when auth type is API 
        // Column to check against the supplied token
        private $column;

        // Case when auth type is API
        // HTTP header to retrieve token from
        private $header;

        // Whether the error should be shown as coming from the API, or from Frontend
        // One of : FRONT / API
        private $for;

        public function __construct($params) {
            // Default
            $this->type = 'session';
            $this->table = 'users';
            $this->for = 'front';
            $this->column = null;
            $this->header = null;

            // Merge
            foreach([ 'type' , 'table' , 'name' , 'header' , 'for' , 'column' ] as $k) {
                if(!isset($params[$k])) continue;
                $this->$k = $params[$k];
            }
        }

        public function __invoke(Request $req, Response $res, &$next) {
            $service = default_service($this->table);

            // Mechanism : Session
            if('session' === strtolower($this->type)) {
                $is_logged = $req->session->get('logged', false);

                if(!$is_logged) {
                    $next = false;

                    if('front' === strtolower($this->for))
                        return $res->redirect(front_path('/sign-in'));

                    if('api' === strtolower($this->for))
                        return $res->send_unauthorized();
                }

                $user_id = $req->session->get('user_id');

                if(!$service->exists($user_id)) {
                    $req->session->unset('logged');
                    $req->session->unset('user_id');

                    if('front' === strtolower($this->for))
                        return $res->redirect(front_path('/sign-in'));

                    if('api' === strtolower($this->for))
                        return $res->send_unauthorized();
                }

                $user = $service->get($user_id);

                if(
                    (isset($user['is_activated']) && !$user['is_activated'])
                    || ((isset($user['is_deleted'])) && $user['is_deleted'])
                ) {
                    $req->session->unset('logged');
                    $req->session->unset('user_id');

                    if('front' === strtolower($this->for))
                        return $res->redirect(front_path('/sign-in'));

                    if('api' === strtolower($this->for))
                        return $res->send_unauthorized();
                }

                $req->context['user'] = $user;

                return;
            }

            // Mechanism : API Token
            if('api' === strtolower($this->type)) {

                if(!isset($req->headers[$this->header])) {
                    $next = false;
                    return $res->send_malformed();
                }

                $token = $req->headers[$this->header];

                if(!$service->exists_one([ "$this->column" => $token ])) {
                    $next = false;
                    return $res->send_malformed();
                }

                $user = $service->find_one([ "$this->column" => $token ]);

                $req->context['user'] = $user;

                return;
            }
        }
    }

