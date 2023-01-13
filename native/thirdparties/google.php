<?php

    namespace native\thirdparties;

    use Google\Client as GoogleClient;
    use native\libs\Thirdparty;
    use GuzzleHttp\Client;
    use native\libs\Options;
    use Throwable;

    class Google extends Thirdparty {
        private static GoogleClient $client;

        private static function setup_client()
        {
            if(!empty(self::$client))
                return;

            self::$client = new GoogleClient();
            self::$client->setApplicationName(Options::get('GOOGLE_APPLICATION_NAME'));
            self::$client->setClientId(Options::get('GOOGLE_CLIENT_ID'));
            self::$client->setClientSecret(Options::get('GOOGLE_CLIENT_SECRET'));
        }

        public static function create_authorization_url(array $scopes, string $redirect_url, string $state = null)
        {
            self::setup_client();

            foreach($scopes as $scope)
                self::$client->addScope($scope);

            self::$client->setRedirectUri($redirect_url);
            self::$client->setAccessType('offline');
            self::$client->setPrompt('consent');
            self::$client->setIncludeGrantedScopes(true); 

            if(!empty($state))
                self::$client->setState($state);

            return self::$client->createAuthUrl();
        }

        public static function trade_code_for_access_token(array $scopes, string $redirect_url, string $code) : array
        {
            self::setup_client();

            foreach($scopes as $scope)
                self::$client->addScope($scope);

            self::$client->setRedirectUri($redirect_url);
            self::$client->setIncludeGrantedScopes(true); 

            $access_token = self::$client->fetchAccessTokenWithAuthCode($code);

            return $access_token;
        }

        public static function set_access_token(array $access_token) : void
        {
            self::setup_client();

            self::$client->setAccessToken($access_token);
        }

        public static function get_client() {
            self::setup_client();
            return self::$client; 
        }
    }


