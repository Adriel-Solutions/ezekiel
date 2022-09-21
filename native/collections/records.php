<?php

    namespace native\collections;

    use ArrayAccess;
    use native\libs\Record;
    use native\libs\Service;

    class Records implements ArrayAccess  {
        private array $data = [];

        public function offsetGet(mixed $offset) : ?Record 
        {
            return $this->data[$offset] ?? null;
        }

        public function offsetSet(mixed $offset, mixed $value) : void 
        {
            if (is_null($offset)) 
                $this->data[] = $value;
             else 
                $this->data[$offset] = $value;
        }

        public function offsetExists($offset) : bool 
        {
            return isset($this->data[$offset]);
        }

        public function offsetUnset(mixed $offset) : void 
        {
            unset($this->data[$offset]);
        }

        public function set_data(array $d) : void
        {
            $this->data = $d;
        }

        public static function from(array $entries, Service $s) : Records
        {
            $collection = new Records();
            $records_objects = array_map(fn($r) => new Record($r, $s), $entries);
            $collection->set_data($records_objects);
            return $collection;
        }
    }

