<?php
    namespace native\libs;

    /**
     * This internal library is responsible for defining handy 
     * constants that are used through the whole project
     */

    class Constants {
        public static int $HTTP_SUCCESS_CODE = 200;
        public static string $HTTP_SUCCESS_STATUS = 'Success';

        public static int $HTTP_MALFORMED_CODE = 400;
        public static string $HTTP_MALFORMED_STATUS = 'Bad request';

        public static int $HTTP_UNAUTHORIZED_CODE = 401;
        public static string $HTTP_UNAUTHORIZED_STATUS = 'Unauthorized';

        public static int $HTTP_FORBIDDEN_CODE = 403;
        public static string $HTTP_FORBIDDEN_STATUS = 'Forbidden';

        public static int $HTTP_NOTFOUND_CODE = 404;
        public static string $HTTP_NOTFOUND_STATUS = 'Not found';

        public static int $HTTP_CONFLICT_CODE = 409;
        public static string $HTTP_CONFLICT_STATUS = 'Conflict';

        public static int $HTTP_ERROR_CODE = 500;
        public static string $HTTP_ERROR_STATUS = 'Failure';

        public static string $APP_VERSION = '0.0.0';
    }
