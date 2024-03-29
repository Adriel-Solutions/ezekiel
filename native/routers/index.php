<?php
    namespace native\routers;

    use native\libs\Options;
    use native\libs\Request;
    use native\libs\Router;

    class Index extends Router {
        protected function load() {
            if(Options::get('API_IS_ENABLED'))
                $this->mount(native_router('api'));

            if(Options::get('WEBHOOKS_IS_ENABLED'))
                $this->mount(native_router('webhooks'));

            if(Options::get('FRONT_IS_ENABLED')) {
                if ( !empty(Options::get('ROOT_FRONT')) ) {
                    $this->get('/', function(Request $req, $res, &$next) {
                        $next = false;
                        $req->forward($this, Options::get('ROOT_FRONT'));
                    });
                }
                $this->mount(native_router('front'));
            }
        }
    }
