<?php

    namespace native\thirdparties;

    use native\libs\Options;
    use native\libs\Thirdparty;
    use Throwable;

    class S3 extends Thirdparty {
        private static \Aws\S3\S3Client $client;

        private static function setup_client() : void
        {
            if(!empty(self::$client)) return;

            self::$client = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => Options::get('STORAGE_S3_REGION'),
                'endpoint' => Options::get('STORAGE_S3_HOST'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key'    => Options::get('STORAGE_S3_KEY'),
                    'secret' => Options::get('STORAGE_S3_SECRET'),
                ],
            ]);
        }
        /**
         * Writes a file to an S3-compatible server bucket
         * @param {string} $name : The resource name to store the file to
         * @param {$_FILE} $file : The raw content to store
         * @return {boolean} TRUE if storing the file worked, FALSE otherwise
         */
        public static function store($name, $content) : bool
        {
            self::setup_client();

            try {
                self::$client->putObject([
                    'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                    'Key'    => $name,
                    'Body'   => $content
                ]);
            } catch (Throwable $e) {
                /* throw $e; */
                return false;
            }

            return true;
        }

        /**
         * Reads a file from an s3-compatible server bucket
         * @param {string} $name : The name of the file to read
         * @return {string} The content of the file
         */
        public static function get(string $filename) : string
        {
            self::setup_client();
            $content = null;

            try {
                $content = self::$client->getObject([
                    'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                    'Key'    => $filename,
                ]);
            } catch (Throwable $e) {
                /* throw $e; */
                return $content;
            }

            return (string) $content['Body'];
        }

        public static function exists(string $filename) : bool
        {
            self::setup_client();
            return self::$client->doesObjectExist(Options::get('STORAGE_S3_BUCKET'), $filename);
        }

        /**
         * @return {boolean} TRUE if erasing the file worked, FALSE otherwise
         */
        public static function erase(string $filename) : bool
        {
            self::setup_client();

            try {
                self::$client->deleteObject([
                    'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                    'Key'    => $filename,
                ]);
            } catch (Throwable $e) {
                /* throw $e; */
                return false;
            }

            return true;
        }

        public static function create_presigned_url_download(string $filename) : string
        {
            self::setup_client();

            $command = self::$client->getCommand('GetObject', [
                'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                'Key'    => $filename
            ]);

            $request = self::$client->createPresignedRequest($command, Options::get('STORAGE_S3_DEFAULT_DOWNLOAD_TIME'));
            return (string) $request->getUri();
        }

        public static function create_presigned_url_upload(string $filename) : string
        {
            self::setup_client();

            $command = self::$client->getCommand('PutObject', [
                'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                'Key'    => $filename
            ]);

            $request = self::$client->createPresignedRequest($command, Options::get('STORAGE_S3_DEFAULT_UPLOAD_TIME'));
            return (string) $request->getUri();
        }
    }
