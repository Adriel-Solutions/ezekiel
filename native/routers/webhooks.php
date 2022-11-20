<?php
    namespace native\routers;

    use native\libs\Options;
    use native\libs\Router;

    class Webhooks extends Router {
        protected function load() {
            $this->set_prefix(Options::get('ROOT_WEBHOOKS'));

            $this->mount(new \app\routers\Webhooks());

            // Default not-found handler
            $this->use(function($req, $res, &$next) {
                $res->send_not_found();
                $next = false;
            });

            // Default error handler
            $this->set_error_handler(function($req, $res, &$next, $err) {
                $content = [];

                if('DEBUG' === Options::get('MODE'))
                    $content['error'] = strval($err);
                else 
                    error_log($err);

                $res->send_error([ 'content' => $content ]);

                $next = false;
            });
        }
    }
