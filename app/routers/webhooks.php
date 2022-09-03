<?php
    namespace app\routers;

    use native\libs\Router;
    use native\libs\Hooks;

    class Webhooks extends Router {
        protected function load() : void
        {  
            Hooks::fire('before_mount_webhooks', $this);
        }
    }
