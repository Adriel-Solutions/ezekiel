<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class Monica extends Thirdparty {

        private static function fetch(string $method, string $endpoint, array $query = [], array $body = []) : array
        {
            $method = strtolower($method);

            $final_url = Options::get('MONICA_URL') . $endpoint;
            $token = Options::get('MONICA_API_TOKEN');

            if(!empty($query)) 
                $final_url .= '?' . http_build_query($query);

            $http = new Client();
            $params = [ 'headers' => [ 'Authorization' => 'Bearer ' . $token , 'Content-Type' => 'application/json' ] ];

            if('post' === $method || 'put' === $method)
                $response = $http->$method($final_url, [ 'json' => $body, ...$params ]);
            else
                $response = $http->$method($final_url, $params);

            return json_decode($response->getBody()->getContents(), true);
        }

        public static function create_contact(string $fullname, string $email, string $job, string $company, string $phone = null)
        {
            $name_parts = explode(' ', $fullname, 2);

            // Create contact
            $response = self::fetch(
                method: 'POST',
                endpoint: '/contacts',
                body: [
                    'first_name' => $name_parts[0],
                    'last_name' => $name_parts[1] ?? '',
                    'gender_id' => 3,
                    'is_deceased' => false,
                    'is_deceased_date_known' => false,
                    'is_birthdate_known' => false,
                ]
            );

            $contact_id = $response['data']['id'];

            // Update company details
            self::fetch(
                method: 'PUT',
                endpoint: '/contacts/' . $contact_id . '/work',
                body: [
                    'job' => $job,
                    'company' => $company
                ]
            );

            // Update email
            self::fetch(
                method: 'POST',
                endpoint: '/contactfields',
                body: [
                    'contact_id' => $contact_id,
                    'contact_field_type_id' => 1,
                    'data' => $email
                ]
            );

            // Update phone
            if(!empty($phone))
                self::fetch(
                    method: 'POST',
                    endpoint: '/contactfields',
                    body: [
                        'contact_id' => $contact_id,
                        'contact_field_type_id' => 2,
                        'data' => $phone
                    ]
                );
        }
    }
