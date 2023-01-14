<?php

    namespace native\jobs\cache;

    use native\libs\Job;

    class Clear extends Job {
        public function run(?array $context): ?string
        {
            unlink(cache_path($context['route']));

            return null;
        }
    }


