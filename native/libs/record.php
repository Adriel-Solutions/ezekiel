<?php

    namespace native\libs;

    use native\libs\Service;

    /**
     * Assumption made : Only purpose -> Quick and easy edit of a row in database
     *                   No need for population (yet ?!)
     */
    class Record {
        private Service $service;
        public array $data;

        public function __construct($data, $service) {
            $this->data = $data;
            $this->service = $service;
        }

        public function save() {
            $this->data = $this->service->create($this->data);
        }

        public function delete() {
            $this->service->delete($this->data['pk']);
            $this->data = [];
        }

        public function set($payload) {
            $this->data = $this->service->update($this->data['pk'], $payload);
        }

        public function populate($field) {
            $this->service->populate($this->data, $field);
        }

        public function refresh() {
            $this->data = $this->service->get($this->data['pk']);
        }

        public function get($key) {
            return $this->data[$key];
        }
    }
