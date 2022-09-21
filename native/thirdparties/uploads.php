<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use Throwable;

    class Uploads extends Thirdparty {
        /**
         * Writes a file on disk in default uploads directory
         * @param {string} $name : The file name (with extension) to store the file to
         * @param {$_FILE} $file : The PHP File object to write on disk
         */
        public function store(string $name, array $file) : bool
        {
            $destination = DIR_ROOT . '/storage/uploads/' . $name;

            if(file_exists($destination)) self::erase($destination);

            try {
                move_uploaded_file($file['tmp_name'], $destination);
            } catch(Throwable $e) {
                return false;
            }

            return true;
        }

        /**
         * Erases a file on disk in default uploads directory
         * @param {string} $name : The name of the file to erase
         */
        public function erase(string $name) : bool 
        {
            $destination = DIR_ROOT . '/storage/uploads/' . $name;

            if(!file_exists($destination)) return false;

            unlink($destination);

            return true;
        }
    }
