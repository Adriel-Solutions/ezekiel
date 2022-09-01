<?php
    namespace native\services;

    use native\libs\Service;

    class Jobs extends Service {
        protected function load() : void {
            $this->table = 'jobs';
        }
    }
