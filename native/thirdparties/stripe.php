<?php
    namespace native\thirdparties;

    use native\libs\Options;

    class Stripe {
        private static \Stripe\StripeClient $_client;

        private static function _setup_client() : void {
            if(!empty(self::$_client)) return;

            self::$_client = new \Stripe\StripeClient(Options::get('STRIPE_SECRET_API_KEY'));
        }

        public static function create_customer(string $name, string $email, string $country_code, string $vat_number = null) {
            self::_setup_client();

            $tax_settings = [];
            if(!empty($vat_number))
                $tax_settings = [ 'tax_id_data' => [ [ 'type' => 'eu_vat' , 'value' => $vat_number ] ] ];

            $customer = self::$_client->customers->create([
                'email' => $email,
                'name' => $name,
                'address' => [
                    'country' => $country_code,
                ],
                ...$tax_settings
                /* 'preferred_locales' => [ 'fr' ] */
            ]);

            return $customer;
        }

        // VAT ID not updatable on Stripe, and it shouldn't be (once typed in, why change?)
        public static function update_customer(string $id, ?string $name = null, ?string $email = null, ?string $country_code = null, ?string $vat_number = null) : void
        {
            self::_setup_client();

            $payload = [];

            // @TODO How to make that more elegant / generalizable ?
            if(!empty($name))
                $payload['name'] = $name;

            if(!empty($email))
                $payload['email'] = $email;

            if(!empty($country_code))
                $payload['address'] = [ 'country' => $country_code ];

            self::$_client->customers->update(
                $id,
                [
                    'email' => $email,
                    'name' => $name,
                    'address' => [
                        'country' => $country_code,
                    ],
                    /* 'preferred_locales' => [ 'fr' ] */
                ],
            );

            if(!empty($vat_number)) {
                $tax_ids = self::$_client->customers->allTaxIds($id);
                if(!empty($tax_ids)) 
                    self::$_client->customers->deleteTaxId($id, $tax_ids->data[0]->id);

                self::$_client->customers->createTaxId(
                    $id,
                  [ 'type' => 'eu_vat', 'value' => $vat_number ]
                );
            }
        }

        public static function create_subscription(string $customer_id, string $price_id, bool $is_taxed = false) : \Stripe\Subscription
        {
            self::_setup_client();

            $tax_settings = [];
            if($is_taxed)
                $tax_settings = [ 'default_tax_rates' => [ Options::get('STRIPE_TAX_ID') ] ];

            $subscription = self::$_client->subscriptions->create([
                'customer' => $customer_id,
                'items' => [[
                    'price' => $price_id,
                ]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription',
                    'payment_method_types' => [ 'card' ]
                ],
                'expand' => ['latest_invoice.payment_intent'],
                ...$tax_settings
            ]);

            return $subscription;
        }

        public static function get_subscription(string $subscription_id) : \Stripe\Subscription
        {
            self::_setup_client();

            $subscription = self::$_client->subscriptions->retrieve(
                $subscription_id,
                [ 'expand' => ['latest_invoice.payment_intent'] ],
            );

            return $subscription;
        }

        public static function cancel_subscription(string $subscription_id) : void
        {
            self::_setup_client();

            /* self::$_client->subscriptions->update( */
            /*     $subscription_id, */
            /*     [ */
            /*         'cancel_at_period_end' => true */
            /*     ] */
            /* ); */
            self::$_client->subscriptions->cancel($subscription_id);
        }

        public static function get_payment_intent(string $payment_intent_id) : \Stripe\PaymentIntent
        {
            self::_setup_client();

            $payment_intent = self::$_client->paymentIntents->retrieve($payment_intent_id);

            return $payment_intent;
        }

        public static function get_customer_portal(string $customer_id, string $return_url) : \Stripe\BillingPortal\Session
        {
            self::_setup_client();

            $portal = self::$_client->billingPortal->sessions->create([
              'customer' => $customer_id,
              'return_url' => $return_url,
            ]);

            return $portal;
        }
    }
