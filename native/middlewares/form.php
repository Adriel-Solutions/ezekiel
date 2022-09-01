<?php
    namespace native\middlewares;
    use native\libs\Middleware;

    class Form extends Middleware {
        public function __invoke($req, $res, &$next) {
            if ( !isset($req->headers['content-type']) )
                return;

            if 
                ( !str_contains($req->headers['content-type'], 'application/x-www-form-urlencoded')
                && ( !str_contains($req->headers['content-type'], 'multipart/form-data') )
            )
                return;

            $body = $_POST;
            $req->body = $body;
        }
    }


