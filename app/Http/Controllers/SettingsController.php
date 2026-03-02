<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Notifications\Notifier;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function testNotification(Request $request)
    {
        $request->headers->set('Accept', 'application/json');

        try {
            $validated = $request->validate([
                'channel' => 'required|in:email,sms,whatsapp,telegram,gekychat',
                'recipient' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $channel = $validated['channel'];
        $recipient = $validated['recipient'];
        $missingConfig = [];

        try {
            $notifier = app(Notifier::class);
            $testMessage = 'This is a test notification from ' . config('app.name') . '. Your ' . ucfirst($channel) . ' configuration is working correctly!';

            switch ($channel) {
                case 'email':
                    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid email address format.'
                        ], 422);
                    }

                    $mailDriver = config('mail.default');
                    $mailHost = config('mail.mailers.smtp.host');
                    $mailPort = config('mail.mailers.smtp.port');
                    $mailUsername = config('mail.mailers.smtp.username');
                    $mailPassword = config('mail.mailers.smtp.password');
                    $emailDriver = config('notifications.email.driver', 'log');

                    if ($emailDriver === 'log') {
                        $missingConfig[] = 'EMAIL_DRIVER is set to log (not configured)';
                    }

                    if (empty($mailDriver) || $mailDriver === 'log') {
                        $missingConfig[] = 'MAIL_MAILER is not configured or set to log';
                    }

                    if ($mailDriver === 'smtp') {
                        if (empty($mailHost)) {
                            $missingConfig[] = 'MAIL_HOST is not configured';
                        }
                        if (empty($mailPort)) {
                            $missingConfig[] = 'MAIL_PORT is not configured';
                        }
                        if (empty($mailUsername)) {
                            $missingConfig[] = 'MAIL_USERNAME is not configured';
                        }
                        if (empty($mailPassword)) {
                            $missingConfig[] = 'MAIL_PASSWORD is not configured';
                        }
                    }

                    if (!empty($missingConfig)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Email configuration is incomplete.',
                            'missing_config' => $missingConfig
                        ], 422);
                    }

                    $result = $notifier->email($recipient, $testMessage, 'Test Notification - ' . config('app.name'));
                    return response()->json([
                        'success' => true,
                        'message' => 'Test email sent successfully to ' . $recipient
                    ]);

                case 'sms':
                    $recipient = $this->normalizeGhanaPhone($recipient);

                    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $recipient)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid phone number format. Please use format 0XXXXXXXXX or +233XXXXXXXXX.'
                        ], 422);
                    }

                    $smsDriver = config('notifications.sms');

                    if (!$smsDriver) {
                        if (env('ARKESEL_SMS_API_KEY')) {
                            $smsDriver = 'arkesel';
                        } elseif (env('HUBTEL_CLIENT_ID') && env('HUBTEL_CLIENT_SECRET')) {
                            $smsDriver = 'hubtel';
                        } else {
                            $smsDriver = 'log';
                        }
                    }

                    if ($smsDriver === 'arkesel') {
                        if (empty(env('ARKESEL_SMS_API_KEY'))) {
                            $missingConfig[] = 'ARKESEL_SMS_API_KEY is not configured';
                        }
                        if (empty(env('ARKESEL_SMS_SENDER_ID'))) {
                            $missingConfig[] = 'ARKESEL_SMS_SENDER_ID is not configured (must be an approved sender ID from your Arkesel account)';
                        }
                    } elseif ($smsDriver === 'hubtel') {
                        if (empty(env('HUBTEL_CLIENT_ID'))) {
                            $missingConfig[] = 'HUBTEL_CLIENT_ID is not configured';
                        }
                        if (empty(env('HUBTEL_CLIENT_SECRET'))) {
                            $missingConfig[] = 'HUBTEL_CLIENT_SECRET is not configured';
                        }
                    } else {
                        $missingConfig[] = 'No SMS provider configured (Arkesel or Hubtel)';
                    }

                    if (!empty($missingConfig)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'SMS configuration is incomplete.',
                            'missing_config' => $missingConfig
                        ], 422);
                    }

                    $result = $notifier->sms($recipient, $testMessage);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test SMS sent successfully to ' . $recipient
                    ]);

                case 'whatsapp':
                    $recipient = $this->normalizeGhanaPhone($recipient);

                    if (!preg_match('/^\+?[1-9]\d{1,14}$/', $recipient)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid phone number format. Please use format 0XXXXXXXXX or +233XXXXXXXXX.'
                        ], 422);
                    }

                    $whatsappDriver = config('notifications.whatsapp', 'log');
                    if ($whatsappDriver === 'log') {
                        $missingConfig[] = 'WHATSAPP_DRIVER is set to log (not configured)';
                    }
                    if ($whatsappDriver === 'meta') {
                        if (empty(env('WA_CLOUD_PHONE_ID'))) {
                            $missingConfig[] = 'WA_CLOUD_PHONE_ID is not configured';
                        }
                        if (empty(env('WA_CLOUD_TOKEN'))) {
                            $missingConfig[] = 'WA_CLOUD_TOKEN is not configured';
                        }
                    }

                    if (!empty($missingConfig)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'WhatsApp configuration is incomplete.',
                            'missing_config' => $missingConfig
                        ], 422);
                    }

                    $result = $notifier->whatsapp($recipient, $testMessage);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test WhatsApp message sent successfully to ' . $recipient
                    ]);

                case 'telegram':
                    $telegramDriver = config('notifications.telegram.driver', 'log');
                    $telegramBotToken = config('notifications.telegram.bot_token', '');

                    if ($telegramDriver === 'log') {
                        $missingConfig[] = 'TELEGRAM_DRIVER is set to log (not configured)';
                    }
                    if (empty($telegramBotToken)) {
                        $missingConfig[] = 'TELEGRAM_BOT_TOKEN is not configured';
                    }

                    if (!empty($missingConfig)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Telegram configuration is incomplete.',
                            'missing_config' => $missingConfig
                        ], 422);
                    }

                    $result = $notifier->telegram($recipient, $testMessage);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test Telegram message sent successfully to ' . $recipient
                    ]);

                case 'gekychat':
                    if (empty($recipient)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Recipient is required for GekyChat.'
                        ], 422);
                    }

                    $gekychatDriver = config('notifications.gekychat.driver', 'log');
                    $gekychatApiUrl = config('notifications.gekychat.api_url', '');
                    $gekychatClientId = config('notifications.gekychat.client_id', '');
                    $gekychatClientSecret = config('notifications.gekychat.client_secret', '');

                    if ($gekychatDriver === 'log') {
                        $missingConfig[] = 'GEKYCHAT_DRIVER is set to log (not configured)';
                    }
                    if (empty($gekychatApiUrl)) {
                        $missingConfig[] = 'GEKYCHAT_API_URL is not configured';
                    }
                    if (empty($gekychatClientId)) {
                        $missingConfig[] = 'GEKYCHAT_CLIENT_ID is not configured';
                    }
                    if (empty($gekychatClientSecret)) {
                        $missingConfig[] = 'GEKYCHAT_CLIENT_SECRET is not configured';
                    }

                    if (!empty($missingConfig)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'GekyChat configuration is incomplete.',
                            'missing_config' => $missingConfig
                        ], 422);
                    }

                    $recipient = $this->normalizeGhanaPhone($recipient);
                    $result = $notifier->gekychat($recipient, $testMessage);
                    return response()->json([
                        'success' => true,
                        'message' => 'Test GekyChat message sent successfully to ' . $recipient
                    ]);

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid notification channel.'
                    ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalize Ghana phone number to E.164 format
     */
    private function normalizeGhanaPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '+233' . substr($phone, 1);
        } elseif (str_starts_with($phone, '233') && strlen($phone) === 12) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }

    public function clearCache()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }
}
