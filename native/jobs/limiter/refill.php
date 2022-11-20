<?php

    namespace native\jobs\limiter;

    use native\libs\Job;

    class Refill extends Job {
        public function run(?array $context): ?string
        {
            $service = default_service('attempts'); 
            $ip = $context['ip'];

            $oldest_attempt = $service->as_records()->find_one(
                [ 'ip' => $ip ],
                [ 'order' => [ 'at' => 'ASC' ] ]
            );

            $oldest_attempt->delete();

            return null;
        }
    }
