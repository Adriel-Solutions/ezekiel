<?php
    namespace native\middlewares;
    use native\libs\Middleware;
    use native\libs\Options;

    class Cors extends Middleware {
        public function __invoke($req, $res, &$next) {
          if($req->method !== 'OPTIONS') return;

          $allowed_origin = Options::get('CORS_ORIGIN');

          header("Access-Control-Allow-Origin: $allowed_origin");       
          header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
          header("Access-Control-Allow-Credentials: true");
          header('Access-Control-Allow-Headers: *');

          $next = false;
          $res->send_success();
        }
    }
