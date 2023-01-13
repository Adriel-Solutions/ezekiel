<?php

    namespace native\thirdparties;

    use SoapClient;

    class Vies {
        // Expected format : LLXXXXX (Letter-Letter-ManyDigits )
        public static function is_valid(string $vat_number) : bool 
        {
            if(strlen($vat_number) < 3) return false;

            $first_two = ucfirst(substr($vat_number, 0, 2));

            if(!ctype_alpha($first_two)) return false;

            $last_chars = substr($vat_number, 2);

            if(!ctype_digit($last_chars)) return false;

            $opts = [ 'http' => [ 'user_agent' => 'PHPSoapClient' ] ];
            $context = stream_context_create($opts);

            $client = new SoapClient(
                'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl',
                [ 'stream_context' => $context, 'cache_wsdl' => WSDL_CACHE_NONE]
            );

            $result = $client->checkVat([
                'countryCode' => $first_two,
                'vatNumber'   => $last_chars
            ]);

            $is_valid = $result->valid === true;

            return $is_valid;
        }
    }
