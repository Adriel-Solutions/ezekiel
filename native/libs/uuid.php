<?php
    namespace native\libs;

    /**
     * This internal library is responsible for generating unique UUID v5 ids
     * 99.9% of it is taken from StackOverflow
     */
    class UUID
    {
        /**
         * Outputs a UUID v5
         *
         * @param {string} namespace The type of UUID to return
         * @return {string} The generated v5 UUID
         */
        public static function v5($namespace = "6ba7b812-9dad-11d1-80b4-00c04fd430c8")
        {
            $salt = uniqid() . uniqid();
            $nhex = str_replace(array('-','{','}'), '', $namespace);
            $nstr = '';
            for($i = 0; $i < strlen($nhex); $i+=2) 
            {
                $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
            }

            $hash = sha1($nstr . $salt);

            return sprintf (    
                '%08s-%04s-%04x-%04x-%12s',
                substr($hash, 0, 8),
                substr($hash, 8, 4),
                (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
                (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
                substr($hash, 20, 12)
            );
        }

        public static function OTP($length = 6) {
            $alphabet = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $password = "";

            for ($i = 0; $i < $length; $i++) {
                $random_index = rand(0, strlen($alphabet) - 1);
                $password .= $alphabet[$random_index];
            }

            return $password;
        }

    }
