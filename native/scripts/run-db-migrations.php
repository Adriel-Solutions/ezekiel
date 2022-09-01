<?php

    // Ensure the script is executed by the PHP CLI
    if(php_sapi_name() !== 'cli') {
        echo 'Error: This script must be run with the PHP CLI';
        exit();
    }

    function graceful_exit($message) {
        print($message);
        print("\n");
        print('Exited.');
        print("\n");
        exit();
    }

    require __DIR__ . '/../autoloader.php';

    use native\libs\Database;
    use native\libs\Options;

    // Configuration retrieval
    Options::load(__DIR__ . "/../../configuration/.custom.env");

    // Database setup
    Database::load();

    // Script entrypoint
    // -----------------
    print("Script : run-db-migrations\n");

    $is_encryption_enabled = Options::get('DB_ENCRYPTION_ENABLED');
    $file_prefix = $is_encryption_enabled ? 'migration-encrypted' : 'migration';

    // Retrieve all the SQL migration files of the project
    $dir_migrations = __DIR__ . '/../../setup/database';
    $base_files = scandir($dir_migrations);
    $base_files = array_map(function($bf) use ($dir_migrations) { return $dir_migrations . '/' . $bf; }, $base_files);

    // Retrieve all the SQL migration files of the project's modules
    $dir_modules = __DIR__  . '/../../app/modules';
    $modules = scandir($dir_modules);
    $modules_files = [];
    foreach($modules as $module) {
        if(in_array($module, ['.' , '..' , '.gitkeep' ])) continue;
        $module_files = scandir($dir_modules . '/' . $module . '/migrations/');

        foreach($module_files as $module_file) {
            if(in_array($module_file, ['.' , '..'])) continue;
            $modules_files[] = $dir_modules . '/' . $module . '/migrations/' . $module_file;
        }
    }

    // Merge Base and Modules SQL migrations
    $files = array_merge($base_files, $modules_files);

    // Retrieve all the SQL files starting with migration-xxx
    $migrations = [];
    foreach($files as $f) {
        $filename = pathinfo($f, PATHINFO_BASENAME);
        if(!str_ends_with($filename, '.sql')) continue;
        if(!str_starts_with($filename, $file_prefix)) continue;
        $migrations[] = $f;
    }

    if(empty($migrations)) graceful_exit('No migration found');

    // Exclude all migrations that were run already
    foreach($migrations as $k => $m) {
        $rows = Database::query(
            'SELECT 1 FROM migrations WHERE name = :name',
            [ 'name' => $m ]
        );

        if(empty($rows)) continue;

        unset($migrations[$k]);
    }

    if(empty($migrations)) graceful_exit('No migration needs to be run');

    // Order migrations by date
    usort(
        $migrations,
        function($a, $b) use ($file_prefix) {
            $a = pathinfo($a, PATHINFO_BASENAME);
            $b = pathinfo($b, PATHINFO_BASENAME);

            $a_date = substr($a, strlen($file_prefix) + 1, 8);
            $b_date = substr($b, strlen($file_prefix) + 1, 8);

            $a_time = DateTime::createFromFormat('Ymd', $a_date);
            $b_time = DateTime::createFromFormat('Ymd', $b_date);

            if($a_time === $b_time) return 0;

            return $a_time < $b_time ? -1 : 1;
        }
    );

    // Execute every migration and insert it into database to prevent double execution
    $sql_base = 'PGPASSWORD=%s psql -v "ON_ERROR_STOP=1" -U %s -h %s -d %s < %s 2>&1';
    foreach($migrations as $m) {
        $command = sprintf(
            $sql_base,
            Options::get('DB_PASS'),
            Options::get('DB_USER'),
            Options::get('DB_HOST'),
            Options::get('DB_NAME'),
            "$m"
        );

        $name = pathinfo($m, PATHINFO_BASENAME);
        print("Applying migration : $name -> ");

        // Run the migration
        $output = null;
        $exit_code = null;
        exec($command, $output, $exit_code);

        if($exit_code !== 0) {
            print("failed.\n");
            print("Error : " . join(',', $output) . "\n");
            print("Script : interrupted\n");
            break;
        }

        print("applied.\n");

        // Store the migration in database
        Database::query(
            'INSERT INTO migrations ( name ) VALUES ( :name )',
            [ 'name' => $m ]
        );
    }

    print("Script : ended\n");
