<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Path
    |--------------------------------------------------------------------------
    |
    | Path to the JSON file where settings are stored.
    |
    */

    'path' => storage_path('app/settings.json'),

    /*
    |--------------------------------------------------------------------------
    | Sidebar Label
    |--------------------------------------------------------------------------
    |
    | The text that Nova displays for this tool in the navigation sidebar.
    |
    */

    'sidebar-label' => 'Settings',

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | The good stuff :). Each setting defined here will render a field in the
    | tool. The only required key is `key`, other available keys include `type`,
    | `label`, `help`, `placeholder`, `language`, and `panel`.
    |
    */
    'settings' => [
        [
            'key' => 'mobile_number',
            'type' => 'text',
            'label' => 'mobile_number',
            'panel' => 'system info',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'transfer_automatic',
            'type' => 'toggle',
            'label' => 'Transfer Automatic',
            'panel' => 'Transfer',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'transfer_day',
            'type' => 'select',
            'label' => 'Transfer Day',
            'options' => [
                '0' => 'Sunday',
                '1' => 'Monday',
                '2' => 'Tuesday',
                '3' => 'Wednesday',
                '4' => 'Thursday',
                '5' => 'Friday',
                '6' => 'Saturday',
            ],
            'panel' => 'Transfer',
        ],
        [
            'key' => 'transfer_minimum',
            'type' => 'Number',
            'label' => 'Transfer Minimum',
            'panel' => 'Transfer',
        ],
        [
            'key' => 'transfer_emails',
            'type' => 'textarea',
            'label' => 'Transfer emails',
            'panel' => 'Transfer',
            'help' => 'Plz Use a Comma as a Separator',

        ],


    ],

];
