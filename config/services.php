<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services.
    |
    */

    'priority_bank' => [
        'api_url' => env('PRIORITY_BANK_API_URL', 'https://prioritybank.gekymedia.com'),
        'api_token' => env('PRIORITY_BANK_API_TOKEN'),
        'timeout' => env('PRIORITY_BANK_API_TIMEOUT', 10),
        'max_retries' => env('PRIORITY_BANK_API_MAX_RETRIES', 3),
    ],

    'blacktask' => [
        'api_url' => env('BLACKTASK_API_URL', 'http://blacktask.test'),
        'api_key' => env('BLACKTASK_API_KEY'),
        'enabled' => env('BLACKTASK_SYNC_ENABLED', false),
        'timeout' => env('BLACKTASK_API_TIMEOUT', 10),
    ],

    'gekychat' => [
        // Platform API is on api subdomain, not chat subdomain
        // Routes are at: api.gekychat.test/platform/oauth/token
        // So base_url should be just the domain (no /api prefix)
        'base_url' => env('GEKYCHAT_API_URL', env('APP_ENV') === 'local' ? 'http://api.gekychat.test' : 'https://api.gekychat.com'),
        'client_id' => env('GEKYCHAT_CLIENT_ID'),
        'client_secret' => env('GEKYCHAT_CLIENT_SECRET'),
        'system_bot_user_id' => (int) env('GEKYCHAT_SYSTEM_BOT_USER_ID', 0),
    ],

];

