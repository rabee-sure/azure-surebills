<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Admins Report
    |--------------------------------------------------------------------------
    |
    | Isolated Laravel 8 configuration for the scheduled active-admins Excel
    | report. Application code must read these values via config(), not env().
    |
    */

    'email' => env('ACTIVE_ADMINS_REPORT_EMAIL'),

    // Cron expression consumed by Illuminate\Console\Scheduling\Schedule::cron()
    'schedule' => env('ACTIVE_ADMINS_REPORT_SCHEDULE', '0 8 * * *'),

    'disk' => 'local',

    'directory' => 'reports/active_admins',

];
