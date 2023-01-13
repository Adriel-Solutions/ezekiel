<?php
    // Performance monitoring
    $ezekiel_time_start = hrtime(true);

    require __DIR__ . '/../index.php';

    $app->dispatch();

    // Performance monitoring
    $ezekiel_time_end = hrtime(true);
    $ezekiel_time_elapsed = round(($ezekiel_time_end - $ezekiel_time_start) / 1e+6, 2);
    /* echo "Time elapsed : $ezekiel_time_elapsed ms"; */
