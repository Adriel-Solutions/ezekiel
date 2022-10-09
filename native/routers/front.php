<?php
    namespace native\routers;

    use native\libs\Options;
    use native\libs\Router;

    class Front extends Router {
        protected function load() {
            $this->set_prefix(Options::get('ROOT_FRONT'));

            $this->mount(new \app\routers\Front());

            // Default not-found handler
            $this->use(function($req, $res, &$next) {
                if(is_enabled_ui())
                    $res->render([
                        'view' => '/pages/errors/404'
                    ]);
                else
                    $res->abort(404);

                $next = false;
            });

            // Default error handler
            $this->set_error_handler(function($req, $res, &$next, $error) {
                if('DEBUG' === Options::get('MODE')) {
                    $whoops = new \Whoops\Run;
                    $whoops->allowQuit(false);
                    $whoops->writeToOutput(false);
                    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
                    $html = $whoops->handleException($error);
                    $res->raw([ 'content' => $html ]);
                    $next = false;
                    return;
                }

                error_log($error);

                if(is_enabled_ui())
                    $res->render([
                        'view' => '/pages/errors/500',
                    ]);
                else
                    $res->abort(500);

                $next = false;
            });
        }
    }
