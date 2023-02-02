<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class JSONAPI extends Thirdparty {

        public static function fetch(string $method, string $url, array $query = [], array $body = []) : array
        {
            $result = [];
            $method = strtolower($method);

            $final_url = $url;
            if(!empty($query)) 
                $final_url .= '&' . http_build_query($query);

            $options = [];
            if(!empty($body))
                $options['json'] = $body;

            $http = new Client();
            try {
                $response = $http->$method($final_url, $options);
                $response = json_decode($response->getBody()->getContents(), true);
                $result = $response;
            } catch (Throwable $t) {
                $result = [ 'failed' =>  true , 'error' => $t ];
            }

            return $result;
        }
    }

