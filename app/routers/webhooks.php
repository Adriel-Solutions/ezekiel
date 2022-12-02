<?php
    namespace app\routers;

    use native\libs\Router;
    use native\libs\Hooks;

    class Webhooks extends Router {
        protected function load() : void
        {  
            $this->use(native_mdw('security'));
            $this->use(native_mdw('cors'));
            $this->use(native_mdw('json'));
            $this->use(native_mdw('form'));
            $this->use(native_mdw('signature'));

            Hooks::fire('before_mount_webhooks', $this);
        }
    }
