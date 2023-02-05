<?php

    namespace native\jobs\cache;

    use native\libs\Job;

    class Clear extends Job {
        public function run(?array $context): void
        {
            if(!file_exists(cache_path($context['route']))) return;

            unlink(cache_path($context['route']));

            return;
        }
    }


