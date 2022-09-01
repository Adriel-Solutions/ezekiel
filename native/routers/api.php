<?php
    namespace native\routers;

    use native\libs\Router;
    use native\libs\Options;
    use native\libs\Hooks;

    class Api extends Router {
        protected function load() {
            $this->set_prefix(Options::get('ROOT_API'));

            Hooks::fire('before_mount_api', $this);

            $this->mount(new \app\routers\Api());

            // Default not-found handler
            $this->use(function($req, $res, &$next) {
                $res->send_not_found();
                $next = false;
            });
        }
    }

