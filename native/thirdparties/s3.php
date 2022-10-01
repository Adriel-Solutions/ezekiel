<?php

    namespace native\thirdparties;

    use native\libs\Options;
    use native\libs\Thirdparty;
    use Throwable;

    class S3 extends Thirdparty {
        private $client;

        private function setup_client() : void
        {
            if(!empty($this->client)) return;

            $this->client = new \Aws\S3\S3Client([
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
        public function store($name, $content) : bool
        {
            $this->setup_client();

            try {
                $this->client->putObject([
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
        public function get($name) : string
        {
            $this->setup_client();
            $content = null;

            try {
                $content = $this->client->getObject([
                    'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                    'Key'    => $name,
                ]);
            } catch (Throwable $e) {
                /* throw $e; */
                return $content;
            }

            return (string) $content['Body'];
        }

        /**
         * @return {boolean} TRUE if erasing the file worked, FALSE otherwise
         */
        public function erase($name) : bool
        {
            $this->setup_client();

            try {
                $this->client->deleteObject([
                    'Bucket' => Options::get('STORAGE_S3_BUCKET'),
                    'Key'    => $name,
                ]);
            } catch (Throwable $e) {
                /* throw $e; */
                return false;
            }

            return true;
        }

        /**
         * @TODO Generate download / upload link
         */
    }
