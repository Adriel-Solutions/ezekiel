<?php

    namespace native\jobs\limiter;

    use native\libs\Job;

    class Refill extends Job {
        public function run(?array $context): void
        {
            $service = default_service('attempts'); 

            $oldest_attempt = $service->as_records()->get($context['attempt']['pk']);

            $oldest_attempt->delete();

            return;
        }
    }

