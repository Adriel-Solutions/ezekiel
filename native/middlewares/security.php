<?php
    namespace native\middlewares;
    use native\libs\Middleware;

    class Security extends Middleware {
        public function __invoke($req, $res, &$next) {
            // Remove "X-Powered-By"
            header_remove("X-Powered-By");

            // Protect against XSS
            header('X-XSS-Protection: 1; mode=block');

            // Disable frames
            header('X-Frame-Options: DENY');

            // Prevent automatic MIME detection
            header('X-Content-Type-Options: nosniff');
        }
    }
