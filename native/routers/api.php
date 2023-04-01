<?php
    namespace native\routers;

    use native\libs\Router;
    use native\libs\Options;
    use native\libs\Request;
    use native\libs\Response;

    class Api extends Router {
        protected function load() {
            $this->set_prefix(Options::get('ROOT_API'));

            // Default cache invalidation mechanism, when applicable
            if(Options::get('CACHE_ENABLED'))
                $this->get('/invalidate', function(Request $req, Response $res) {
                    switch(true) {
                        case empty($req->query):
                        case empty($req->query['route']):
                        case empty($req->query['secret']):
                        case $req->query['secret'] !== Options::get('CACHE_SECRET'):
                            return $res->send_malformed();
                    }

                    $route = str_replace([ '.' ], '',strtolower($req->query['route']));

                    if(!file_exists(cache_path($route)))
                        return $res->send_conflict();

                    unlink(cache_path($route));

                    $res->send_success();
                });

            // Default cache invalidation mechanism, clears the full cache, when applicable
            if(Options::get('CACHE_ENABLED'))
                $this->get('/invalidate-all', function(Request $req, Response $res) {
                    switch(true) {
                        case empty($req->query):
                        case empty($req->query['secret']):
                        case $req->query['secret'] !== Options::get('CACHE_SECRET'):
                            return $res->send_malformed();
                    }

                    $it = new \RecursiveDirectoryIterator(cache_folder());
                    $filtered = array("html");
                    foreach(new \RecursiveIteratorIterator($it) as $file) {
                        $ext = pathinfo($file, PATHINFO_EXTENSION);
                        if( in_array($ext, $filtered) && is_file($file) )
                            unlink($file);
                    }

                    $res->send_success();
                });

            $this->mount(new \app\routers\Api());

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

