<?php
    namespace native\libs;

    /**
     * Client HTTP - Hermes
     */
    class Hermes {

        /**
         * Client HTTP - curl
         */
        private $client;

        private $default_headers;

        /**
         * Base URL
         */
        private $base_url;

        /**
         * Constructeur
         */
        function __construct($params = []) {
            $this->client = curl_init();
            $this->default_headers = [ 'Content-Type' => 'application/json', 'Accept' => 'application/json' ];

            if(!empty($params['base'])) $this->base_url = $params['base'];
            else $this->base_url = '';
        }

        /**
         * @description Envoie une requête HTTP REST
         * @param params : Un tableau associatif de paramètres
         *                 -> url : L'URL à requêter
         *                 -> method : Le verbe HTTP à utiliser
         *                 -> headers : Un tableau associatif de headers HTTP à inclure
         *                 -> content : Un objet PHP à envoyer dans le corps de la requête
         */
        public function request($params) {
            /**
             * Extraction des paramètres
             */
            $method = $params['method'];
            $url = $this->base_url . $params['url'];
            $query = $params['query'] ?? [];
            $headers = $params['headers'] ?? [];
            $body = $params['content'] ?? [];

            /**
             * Configuration générale de la requête
             */
            $shouldDecodeJSON = $params['settings']['decodeJSON'] ?? true;

            /**
             * Ajout des headers par défaut aux headers spécifiés
             */
            $headers = array_merge([], $headers);

            /**
             * Construction de la chaîne de query params
             */
            $queryParts = [];
            foreach($query as $key => $value) 
                array_push($queryParts, "$key=$value");
            $queryString = join('&', $queryParts);

            /**
             * Ajout de la query string à l'URL
             */
            if(count($queryParts) > 0) $url .= '?' . $queryString;

            /**
             * Transformation des paramètres ( encodage url au lieu de json ligne 80)
             */
            $method = strtoupper($method);
            $headers = array_map(function($k,$v){ return "$k: $v"; }, array_keys($headers), array_values($headers));
            $body = empty($body) ? [] : http_build_query($body);
        

            /**
             * Configuration du client HTTP
             */
            curl_setopt($this->client, CURLOPT_URL, $url);
            curl_setopt($this->client, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($this->client, CURLOPT_HTTPHEADER, $headers);
            if(!empty($body)) curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);
            curl_setopt($this->client, CURLOPT_RETURNTRANSFER, true);

            /**
             * Préparation à la réception des headers dans la réponse
             */
            $response_headers = [];
            curl_setopt(
                $this->client,
                CURLOPT_HEADERFUNCTION,
                function($curl, $header) use (&$response_headers) {
                    $len = strlen($header);
                    $header = explode(':', $header, 2);

                    if (count($header) < 2) 
                      return $len;

                    $response_headers[strtoupper(trim($header[0]))] = trim($header[1]);

                    return $len;
                }
            );

            /**
             * Exécution de la requête
             */
            $result = curl_exec($this->client) ?? [];
            $error = curl_error($this->client) ?? "";

            /**
             * Extraction des données de la réponse
             */
            $response = [
                'code' => curl_getinfo($this->client, CURLINFO_HTTP_CODE),
                'headers' => $response_headers,
                'body' => $result,
                'error' => $error
            ];

            if($shouldDecodeJSON) $response['content'] = json_decode($result, true);

            return (object) $response;
        }

        /**
         * Destructeur
         */
        public function __destruct() {
            curl_close($this->client);
        }
    }
