<?php
    // Practical path constants
    define('DIR_ROOT',   __DIR__ . DIRECTORY_SEPARATOR . '..');
    define('DIR_APP',    DIR_ROOT . DIRECTORY_SEPARATOR . 'app');
    define('DIR_NATIVE', DIR_ROOT . DIRECTORY_SEPARATOR . 'native');

    /**
     * External Dependencies
     */
    require DIR_ROOT . '/dependencies/autoload.php';

    /**
     * Local Autoloader
     */
    require DIR_NATIVE . '/autoloader.php';

    /**
     * Global syntactic sugars
     */
    require DIR_NATIVE . '/libs/sugar.php';

    /**
     * Namespace setup
     */
    use \native\libs\Options;
    use \native\libs\Router;
    use \native\libs\Database;
    use \native\libs\I18n;
    use \native\libs\Logger;

    /**
     * Project configuration
     */
    Options::load(DIR_ROOT . "/configuration/.custom.env");

    /**
     * PHP Logging
     */
    if('DEBUG' === Options::get('MODE')) {
        error_reporting(E_ALL);
        ini_set('ignore_repeated_errors', TRUE);
        ini_set('display_errors', TRUE);
        ini_set('log_errors',FALSE);
    }
    else {
        error_reporting(E_ALL);
        ini_set('ignore_repeated_errors', TRUE);
        ini_set('display_errors', FALSE);
        ini_set('log_errors', TRUE);
        ini_set('error_log', DIR_ROOT . '/storage/logs/php/error.log');
    }

    /**
     * Sessions lifetime increase
     */
    ini_set('session.gc_maxlifetime', strtotime(Options::get('SESSIONS_DURATION'), 0));
    ini_set('session.cookie_lifetime', strtotime(Options::get('SESSIONS_DURATION'), 0));

    /**
     * Database connection
    */
    Database::load();

    /**
     * Retrieve extra settings from database, if enabled
     */
    if(Options::get('EXTRA_SETTINGS_ENABLED'))
        Options::load_from_database();

    /**
     * I18n initialization
     */
    I18n::load();

    /**
     * Timezone setup for dates
     */
    date_default_timezone_set(Options::get('TIMEZONE'));

    /**
     * Log initialization
     */
    Logger::load();

    /**
     * The app is just a global HTTP router
     */
    $app = new Router();

    /**
     * Run user-defined stuff on the app's router before it's mounted
     */
    \app\Bootstrap::setup($app);

    /**
     * Mount the main "lookup table" for routes
     */
    $app->mount(native_router('index'));
