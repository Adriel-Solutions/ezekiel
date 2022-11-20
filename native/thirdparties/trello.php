<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class Trello extends Thirdparty {

        private static string $base_url = "https://api.trello.com/1";

        private static function fetch(string $method, string $endpoint, array $query = [], array $body = []) : array
        {
            $method = strtolower($method);

            $final_url = self::$base_url . $endpoint;
            $final_url .= '?';
            $final_url .= 'key=' . Options::get('TRELLO_API_KEY');
            $final_url .= '&token=' . Options::get('TRELLO_API_TOKEN');

            if(!empty($query)) 
                $final_url .= '&' . http_build_query($query);

            $http = new Client();

            if('post' === $method)
                $response = $http->$method($final_url, [ 'json' => $body ]);
            else
                $response = $http->$method($final_url);

            return json_decode($response->getBody()->getContents(), true);
        }

        public static function create_card(string $list_id, array $attributes = [])
        {
            $response = self::fetch(
                method: 'POST',
                endpoint: '/cards',
                query: [ 'idList' => $list_id ],
                body: $attributes
            );

            return $response;
        }

        public static function create_webhook(array $attributes = [])
        {
            self::fetch(
                method: 'POST',
                endpoint: '/tokens/' . Options::get('TRELLO_API_TOKEN') . '/webhooks',
                body: $attributes
            );
        }

        public static function get_lists(string $name)
        {
            /* $response = self::fetch('/boards/' . get_option('TRELLO_BOARD_ID') . '/lists'); */
            /* return $response; */
        }

    }
