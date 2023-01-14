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
            $res->when_finished(function($response) use($req) {
                $page = ob_get_contents();

                file_put_contents(DIR_ROOT . '/storage/cache/' . $req->route . '.html', $page);
                Queue::schedule(new \native\jobs\cache\Clear())
                      ->for(date('c', strtotime('+' . $this->duration, time())))
                      ->with([ 'route' => $req->route ])
                      ->persist();

                ob_flush();
            });
        }
    }


