<?php
    namespace native\middlewares;
    use native\libs\Middleware;
    use native\libs\Queue;
    use native\libs\Request;
    use native\libs\Response;

    class Cache extends Middleware {
        // Cache duration, in strtotime string
        private string $duration;

        public function __construct($params) {
            // Default
            $this->duration = '1 day';

            // Merge
            foreach([ 'duration' ] as $k) {
                if(!isset($params[$k])) continue;
                $this->$k = $params[$k];
            }
        }

        public function __invoke(Request $req, Response $res, &$next) {
            ob_start();
            $res->when_finished(function(Response $response) use($req) {
                $page = ob_get_contents();

                if ($response->code > 299)
                    return ob_flush();

                $route = $req->route;
                if(empty($route) || $route === '/') $route = 'index';

                if(str_contains($route, '/')) {
                    $route_parts = explode('/', $route);
                    $route_folder = join('/', array_slice($route_parts, 0, -1));
                    $final_folder = cache_folder() . $route_folder;
                    if(!is_dir($final_folder))
                        mkdir($final_folder, 0777, true);
                }

                
                file_put_contents(DIR_ROOT . cache_path($route), $page);
                Queue::schedule(new \native\jobs\cache\Clear())
                      ->for(date('c', strtotime('+' . $this->duration, time())))
                      ->with([ 'route' => $route ])
                      ->persist();

                ob_flush();
            });
        }
    }


