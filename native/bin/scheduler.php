<?php
    // Ensure this is executed by the PHP CLI (via CRON)
    if(php_sapi_name() !== 'cli') {
        echo 'Error: This script must be run with the PHP CLI';
        exit();
    }

    use native\libs\Database;
    use native\libs\Options;

    // Practical path constants
    define('DIR_ROOT',   __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
    define('DIR_APP',    DIR_ROOT . DIRECTORY_SEPARATOR . 'app');
    define('DIR_NATIVE', DIR_ROOT . DIRECTORY_SEPARATOR . 'native');

    /**
     * External Dependencies
     */
    require DIR_APP . '/dependencies/autoload.php';

    /**
     * Local Autoloader
     */
    require DIR_NATIVE . '/autoloader.php';

    /**
     * Global syntactic sugars
     */
    require DIR_NATIVE . '/libs/sugar.php';

    // Configuration retrieval
    Options::load(DIR_ROOT . "/configuration/.custom.env");

    // Database setup
    Database::load();

    /**
     * Retrieve extra settings from database, if enabled
     */
    if(Options::get('EXTRA_SETTINGS_ENABLED'))
        Options::load_from_database();

    // Entrypoint
    // ----------
    $service = native_srvc('jobs');
    $jobs = $service->get_all();

    if ( empty($jobs) )
        exit();

    $now = time();
    foreach($jobs as $job) {
        // 1. Job is running but is exclusive
        if ( $job['is_exclusive'] && $job['is_running'] )
            continue;

        // 2. Job scheduled once for the future but we're not there yet
        if ( !empty($job['scheduled_for']) ) {
            if ( strtotime($job['scheduled_for']) > $now )
                continue;
        }

        // 3. Job recurrent and scheduled to start in the future but we're not there yet
        if ( !empty($job['scheduled_from']) ) {
            if ( strtotime($job['scheduled_from']) > $now )
                continue;
        }

        // 4. Job scheduled once for the future, but was already run previously
        if ( !empty($job['scheduled_for']) ) {
            if ( !empty($job['last_run_at']) ) {

                // Logs retention mechanism after job was executed
                $now = time();
                $to_delete_at = strtotime($job['last_run_at']) + strtotime(Options::get('JOBS_RETENTION_TIME'), 0);

                if ( $now > $to_delete_at )
                    $service->delete($job['pk']);

                continue;
            }
        }

        // 5. Job is recurrent but the frequency hasn't been passed yet
        if ( !empty($job['schedule_frequency']) ) {
            $last_run_at = strtotime( empty($job['last_run_at']) ? $job['created_at'] : $job['last_run_at'] );
            $frequency = strtotime($job['schedule_frequency'], 0);

            if ( $last_run_at + $frequency > $now )
                continue;
        }

        // Job can be run
        $class = $job['class'];
        $instance = new $class($job['pk']);

        $context = json_decode($job['context'], true);
        $report = 'The job executed succesfully';

        try {
            $service->update(
                $job['pk'],
                [
                    'is_running' => true,
                    'last_run_at' => date('c')
                ]
            );

            $new_report = $instance->run($context);

            if ( !empty($new_report) )
                $report = $new_report;

        } catch (Throwable $e) {
            $report = $e;

        } finally {
            $service->update(
                $job['pk'],
                [
                    'report' => $report,
                    'is_running' => false
                ]
            );
        }
    }

