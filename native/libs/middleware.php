<?php
    namespace native\libs;

    use native\libs\Request;
    use native\libs\Response;

    class Middleware {
        // @override
        public function __invoke(Request $req, Response $res, bool &$next) {  }
    }
