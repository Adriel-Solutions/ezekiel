<?php

    namespace native\thirdparties;

    use native\libs\Options;

    class Uploads {
        /**
         * Writes a file on disk in default uploads directory
         * @param {string} $name : The file name (with extension) to store the file to
         * @param {$_FILE} $file : The PHP File object to write on disk
         */
        public function store($name, $file) {
            $destination = __DIR__ . '/../../' . Options::get('UPLOADS_DIR') . '/' . $name;

            if(file_exists($destination)) self::erase($destination);

            move_uploaded_file($file['tmp_name'], $destination);
        }

        /**
         * Erases a file on disk in default uploads directory
         * @param {string} $name : The name of the file to erase
         */
        public function erase($name) {
            $destination = __DIR__ . '/../../' . Options::get('UPLOADS_DIR') . '/' . $name;
            unlink($destination);
        }
    }
