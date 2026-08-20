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
        'system_id' => env('PRIORITY_BANK_SYSTEM_ID', 'priority_agriculture'),
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

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        'base_url' => env('PAYSTACK_BASE_URL', 'https://api.paystack.co'),
    ],

    'hubtel' => [
        'client_id' => env('HUBTEL_CLIENT_ID'),
        'client_secret' => env('HUBTEL_CLIENT_SECRET'),
        'api_key' => env('HUBTEL_API_KEY'),
        'api_secret' => env('HUBTEL_API_SECRET'),
        'merchant_account_number' => env('HUBTEL_MERCHANT_ACCOUNT_NUMBER'),
    ],

    'ussd' => [
        'api_key' => env('USSD_API_KEY'),
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => (int) env('OPENAI_REQUEST_TIMEOUT', 60),
    ],


    // Google Drive backups (per-project OAuth — same Google account, separate refresh token)
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
        'scopes' => [
            'https://www.googleapis.com/auth/drive.file',
        ],
        'access_token_cache_key' => env('GOOGLE_ACCESS_TOKEN_CACHE_KEY', 'google_access_token_agribusiness'),
        'drive_backup_folder' => env('GOOGLE_DRIVE_BACKUP_FOLDER', 'Priority Agribusiness Backups'),
        'backups_redirect_route' => env('GOOGLE_BACKUPS_REDIRECT_ROUTE', 'backups.index'),
        'backups_dashboard_route' => env('GOOGLE_BACKUPS_DASHBOARD_ROUTE', 'dashboard'),
        'google_auth_route' => env('GOOGLE_AUTH_ROUTE', 'google-auth.start'),
        'backups_status_route' => env('GOOGLE_BACKUPS_STATUS_ROUTE', 'backups.status'),
        'backup_paths' => ['public'],
    ],

];
