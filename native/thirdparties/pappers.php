<?php

    namespace native\thirdparties;

    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class Pappers extends Thirdparty {

        private static string $base_url = "https://api.pappers.fr/v2";

        private static function fetch(string $endpoint, array $query = []) : array
        {
            $final_url = self::$base_url . $endpoint;
            $final_url .= '?';
            $final_url .= 'api_token=' . Options::get('PAPPERS_API_KEY');

            if(!empty($query)) 
                $final_url .= '&' . http_build_query($query);

            $http = new Client();
            $response = $http->get($final_url);
            return json_decode($response->getBody()->getContents(), true);
        }

        public static function search_by_siret(string $number) : array
        {
            return [ self::fetch('/entreprise', [ 'siret' => $number ]) ];
        }

        public static function search_by_name(string $name) : array
        {
            $response = self::fetch('/suggestions', [ 'q' => $name , 'longueur' => Options::get('PAPPERS_RESULT_LIMIT') ]);
            return $response['resultats_nom_entreprise'];
        }

    }
