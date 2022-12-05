<?php
    namespace native\middlewares;
    use native\libs\Middleware;
    use native\libs\Queue;
    use native\libs\Request;
    use native\libs\Response;

    class Limiter extends Middleware {
        // The maximum number of attempts
        private int $max;

        // The time it takes (in seconds) to be granted one more attempt
        private int $refill_rate;

        public function __construct($params) {
            // Default
            $this->max = 10;
            $this->refill_rate = 15;

            // Merge
            foreach([ 'max' ] as $k) {
                if(!isset($params[$k])) continue;
                $this->$k = $params[$k];
            }
        }

        public function __invoke(Request $req, Response $res, &$next) {
            $service = default_service('attempts');

            $ip = $req->ip;
            $uri = $req->uri;
            $count = $service->find_count([ 'uri' => $uri, 'ip' => $ip ]);

            if($count < $this->max) {
                $service->create([ 
                    'ip' => $ip,
                    'uri' => $uri,
                ]);

                Queue::schedule(new \native\jobs\limiter\Refill())
                       ->in($this->refill_rate . " seconds")
                       ->from('now')
                       ->with([ 'ip' => $ip ])
                       ->persist();

                return;
            }

            $res->send_too_many_requests();
            $next = false;
        }
    }


