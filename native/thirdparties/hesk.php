<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class Hesk extends Thirdparty {

        private static function fetch(string $method, string $endpoint, array $query = [], array $body = [])
        {
            $method = strtolower($method);

            $base_url = Options::get('HESK_URL');
            $final_url = $base_url . $endpoint;
            $final_url .= '?';

            if(!empty($query)) 
                $final_url .= '&' . http_build_query($query);

            $http = new Client();

            if('post' === $method)
                $response = $http->$method($final_url, [ 'form_params' => $body ]);
            else
                $response = $http->$method($final_url);

            // Do whatever with the response, it's not JSON so ..?
        }

        public static function create_ticket(string $name, string $email, string $priority, string $subject, int $category, string $message, array $attributes)
        {
            $response = self::fetch(
                method: 'POST',
                endpoint: '/submit_ticket.php',
                query: [ 'submit' => 1 ],
                body: [
                    'name' => $name,
                    'email' => $email,
                    'priority' => $priority,
                    'subject' => $subject,
                    'category' => $category,
                    'message' => $message,
                    ...$attributes,
                    // - spam mandatory fields to prevent being flagged as spammer
                    'hx' => 3,
                    'hy' => ''
                ]
            );

            // Do whatever with the response
        }

        public static function create_webhook(array $attributes = [])
        {
            self::fetch(
                method: 'POST',
                endpoint: '/tokens/' . Options::get('TRELLO_API_TOKEN') . '/webhooks',
                body: $attributes
            );
        }
    }
