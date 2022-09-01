<?php
    namespace native\routers;

    use native\libs\Options;
    use native\libs\Router;
    use native\libs\Hooks;

    class Webhooks extends Router {
        protected function load() {
            $this->set_prefix(Options::get('ROOT_WEBHOOKS'));

            Hooks::fire('before_mount_webhooks', $this);

            $this->mount(new \app\routers\Webhooks());

            // Default not-found handler
            $this->use(function($req, $res, &$next) {
                $res->send_not_found();
                $next = false;
            });
        }
    }
