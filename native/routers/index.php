<?php
    namespace native\routers;

    use native\libs\Options;
    use native\libs\Request;
    use native\libs\Router;

    class Index extends Router {
        protected function load() {
            if ( !empty(Options::get('ROOT_API')) )
                $this->mount(native_router('api'));

            if ( !empty(Options::get('ROOT_FRONT')) ) {
                $this->get('/', function(Request $req, $res, &$next) {
                    $next = false;
                    $req->forward($this, Options::get('ROOT_FRONT'));
                });
                $this->mount(native_router('front'));
            }

            if ( !empty(Options::get('ROOT_WEBHOOKS')) )
                $this->mount(native_router('webhooks'));
        }
    }
