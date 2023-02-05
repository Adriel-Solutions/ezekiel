<?php
    namespace native\libs;

    class Job {
        private int|string $id;

        public function __construct($id = null) {
            if(empty($id)) return;

            $this->id = $id;
        }

        // @override
        public function run(?array $context) : void { return; }

        protected function report_progress(int|float $progress) : void {
            if(empty($this->id)) return;

            $service = default_service('jobs');
            $service->update($this->id, [ 'progress' => $progress ]);
        }
    }
