<?php
    namespace app\routers;

    use native\libs\Router;
    use native\libs\Hooks;
    use native\libs\Response;

    class Front extends Router {
        protected function load() : void
        {
            Hooks::fire('before_mount_front', $this);
        }
    }
