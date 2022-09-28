<?php
    namespace native\middlewares;
    use native\libs\Middleware;
    use native\libs\Options;

    class Signature extends Middleware {
        public function __invoke($req, $res, &$next) {
            if ( Options::get('ANTI_TAMPERING_ENABLED') !== true ) return;

            if( empty($req->headers['x-adriel-signature']) ) {
                $next = false;
                return $res->send_malformed();
            }

            $supplied_signature = $req->headers['x-adriel-signature'];   
            $supplied_content = $req->method === 'GET' ? 'uri' : 'raw';

            $computed_signature = base64_encode(hash_hmac('sha256', $req->$supplied_content, Options::get('SIGNATURE_SECRET'), true));

            if( $computed_signature === $supplied_signature ) return;

            $next = false;
            return $res->send_malformed();
        }
    }
