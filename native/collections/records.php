<?php

    namespace native\collections;

    use ArrayAccess;
    use native\libs\Record;

    class Records implements ArrayAccess {
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
    }

