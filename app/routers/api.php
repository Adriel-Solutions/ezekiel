<?php
    
    namespace app\routers;

    use native\libs\Hooks;
    use native\libs\Router;

    class Api extends Router {
        protected function load() : void
        {
            /**
             * Add support for :
             * - HTTP default security good practice
             * - CORS for JS XHR
             * - JSON + Form user input decoding
             * - Anti-request tampering
             */
            $this->use(native_mdw('security'));
            $this->use(native_mdw('cors'));
            $this->use(native_mdw('json'));
            $this->use(native_mdw('form'));
            $this->use(native_mdw('signature'));

            Hooks::fire('before_mount_api', $this);
        }
    }
