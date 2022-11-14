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

    'sidebar-label' => 'General Settings',

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
            'key' => 'sun',
            'type' => 'toggle',
            'label' => 'Sun',
            'panel' => 'Transfer Repeater',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'mon',
            'type' => 'toggle',
            'label' => 'Mon',
            'panel' => 'Transfer Repeater',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'tue',
            'type' => 'toggle',
            'label' => 'Tue',
            'panel' => 'Transfer Repeater',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'wed',
            'type' => 'toggle',
            'label' => 'Wed',
            'panel' => 'Transfer Repeater',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
        ],
        [
            'key' => 'thr',
            'type' => 'toggle',
            'label' => 'Thr',
            'panel' => 'Transfer Repeater',
            // 'help' => 'For the upcoming release. <a href="/docs#feature_42">Read more here.</a>',
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