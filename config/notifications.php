<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Driver options: 'arkesel', 'hubtel', 'log'
    | 
    | Priority: If both Arkesel and Hubtel are configured, Arkesel will be used.
    | Set SMS_DRIVER explicitly to override auto-detection.
    | 
    | 'arkesel' - Uses Arkesel SMS API (requires ARKESEL_SMS_API_KEY)
    | 'hubtel' - Uses Hubtel SMS API (requires HUBTEL_CLIENT_ID & HUBTEL_CLIENT_SECRET)
    | 'log' - Log messages instead of sending (for development)
    */
    'sms' => env('SMS_DRIVER', null),
    
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Driver options: 'meta', 'log'
    | 'meta' uses Meta Cloud API (WhatsApp Business)
    */
    'whatsapp' => env('WHATSAPP_DRIVER', 'log'),
    
    /*
    |--------------------------------------------------------------------------
    | Default Country Code
    |--------------------------------------------------------------------------
    |
    | Default country code for phone number formatting (Ghana: 233)
    */
    'default_country_code' => env('DEFAULT_COUNTRY_CODE', '233'),
    
    /*
    |--------------------------------------------------------------------------
    | Telegram Configuration
    |--------------------------------------------------------------------------
    |
    | Driver options: 'telegram', 'log'
    */
    'telegram' => [
        'driver' => env('TELEGRAM_DRIVER', 'log'),
        'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | GekyChat Configuration
    |--------------------------------------------------------------------------
    |
    | Driver options: 'api', 'log'
    */
    'gekychat' => [
        'driver' => env('GEKYCHAT_DRIVER', 'log'),
        'api_url' => env('GEKYCHAT_API_URL', env('APP_ENV') === 'local' ? 'http://api.gekychat.test' : 'https://api.gekychat.com'),
        'client_id' => env('GEKYCHAT_CLIENT_ID'),
        'client_secret' => env('GEKYCHAT_CLIENT_SECRET'),
        'system_bot_user_id' => (int) env('GEKYCHAT_SYSTEM_BOT_USER_ID', 0),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Driver options: 'mail', 'log'
    */
    'email' => [
        'driver' => env('EMAIL_DRIVER', 'log'),
    ],
];
