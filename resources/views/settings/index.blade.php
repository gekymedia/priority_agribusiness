@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="settings-page">
    <div class="page-header">
        <h1 class="page-title">System Settings</h1>
        <p class="page-subtitle">Manage notification settings and test notification channels</p>
    </div>

    <div class="row g-4">
        <!-- Notification Testing -->
        <div class="col-lg-8">
            <div class="agri-card mb-4">
                <div class="agri-card-header">
                    <h3><i class="fas fa-bell me-2"></i>Test Notifications</h3>
                </div>
                <div class="agri-card-body">
                    <p class="text-muted mb-4">Test your notification channels to ensure they are configured correctly.</p>

                    <div class="notification-channels mb-4">
                        <label class="form-label fw-bold mb-3">Select Notification Channel</label>
                        <div class="row g-3">
                            <!-- Email Channel -->
                            <div class="col-md-6 col-lg-3">
                                <label class="channel-card">
                                    <input type="radio" name="notification_channel" value="email" checked onchange="handleChannelChange()">
                                    <div class="channel-content">
                                        <i class="fas fa-envelope channel-icon" style="color: #3b82f6;"></i>
                                        <span class="channel-name">Email</span>
                                        <div class="channel-status">
                                            @php
                                                $mailDriver = config('mail.default');
                                                $mailHost = config('mail.mailers.smtp.host');
                                                $emailDriver = config('notifications.email.driver', 'log');
                                                $mailConfigured = !empty($mailDriver) && $mailDriver !== 'log' && !empty($mailHost) && $emailDriver !== 'log';
                                            @endphp
                                            @if($mailConfigured)
                                                <span class="status-configured"><i class="fas fa-check-circle"></i> Configured</span>
                                            @else
                                                <span class="status-not-configured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- SMS Channel -->
                            <div class="col-md-6 col-lg-3">
                                <label class="channel-card">
                                    <input type="radio" name="notification_channel" value="sms" onchange="handleChannelChange()">
                                    <div class="channel-content">
                                        <i class="fas fa-sms channel-icon" style="color: #10b981;"></i>
                                        <span class="channel-name">SMS</span>
                                        <div class="channel-status">
                                            @php
                                                $smsDriver = config('notifications.sms', 'log');
                                                $arkeselConfigured = !empty(env('ARKESEL_SMS_API_KEY')) && !empty(env('ARKESEL_SMS_SENDER_ID'));
                                                $hubtelConfigured = !empty(env('HUBTEL_CLIENT_ID')) && !empty(env('HUBTEL_CLIENT_SECRET'));
                                                $smsConfigured = $arkeselConfigured || $hubtelConfigured;
                                                
                                                $providerName = '';
                                                if ($arkeselConfigured && ($smsDriver === 'arkesel' || !$smsDriver)) {
                                                    $providerName = 'Arkesel';
                                                } elseif ($hubtelConfigured && $smsDriver === 'hubtel') {
                                                    $providerName = 'Hubtel';
                                                }
                                            @endphp
                                            @if($smsConfigured)
                                                <span class="status-configured"><i class="fas fa-check-circle"></i> {{ $providerName ?: 'Configured' }}</span>
                                            @else
                                                <span class="status-not-configured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- WhatsApp Channel -->
                            <div class="col-md-6 col-lg-3">
                                <label class="channel-card">
                                    <input type="radio" name="notification_channel" value="whatsapp" onchange="handleChannelChange()">
                                    <div class="channel-content">
                                        <i class="fab fa-whatsapp channel-icon" style="color: #25D366;"></i>
                                        <span class="channel-name">WhatsApp</span>
                                        <div class="channel-status">
                                            @php
                                                $whatsappDriver = config('notifications.whatsapp', 'log');
                                                $whatsappConfigured = $whatsappDriver !== 'log' && !empty(env('WA_CLOUD_PHONE_ID')) && !empty(env('WA_CLOUD_TOKEN'));
                                            @endphp
                                            @if($whatsappConfigured)
                                                <span class="status-configured"><i class="fas fa-check-circle"></i> Configured</span>
                                            @else
                                                <span class="status-not-configured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- Telegram Channel -->
                            <div class="col-md-6 col-lg-3">
                                <label class="channel-card">
                                    <input type="radio" name="notification_channel" value="telegram" onchange="handleChannelChange()">
                                    <div class="channel-content">
                                        <i class="fab fa-telegram channel-icon" style="color: #0088cc;"></i>
                                        <span class="channel-name">Telegram</span>
                                        <div class="channel-status">
                                            @php
                                                $telegramDriver = config('notifications.telegram.driver', 'log');
                                                $telegramBotToken = config('notifications.telegram.bot_token', '');
                                                $telegramConfigured = $telegramDriver !== 'log' && !empty($telegramBotToken);
                                            @endphp
                                            @if($telegramConfigured)
                                                <span class="status-configured"><i class="fas fa-check-circle"></i> Configured</span>
                                            @else
                                                <span class="status-not-configured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            
                            <!-- GekyChat Channel -->
                            <div class="col-md-6 col-lg-3">
                                <label class="channel-card">
                                    <input type="radio" name="notification_channel" value="gekychat" onchange="handleChannelChange()">
                                    <div class="channel-content">
                                        <i class="fas fa-comments channel-icon" style="color: #8b5cf6;"></i>
                                        <span class="channel-name">GekyChat</span>
                                        <div class="channel-status">
                                            @php
                                                $gekychatDriver = config('notifications.gekychat.driver', 'log');
                                                $gekychatClientId = config('notifications.gekychat.client_id', '');
                                                $gekychatClientSecret = config('notifications.gekychat.client_secret', '');
                                                $gekychatConfigured = $gekychatDriver !== 'log' && !empty($gekychatClientId) && !empty($gekychatClientSecret);
                                            @endphp
                                            @if($gekychatConfigured)
                                                <span class="status-configured"><i class="fas fa-check-circle"></i> Configured</span>
                                            @else
                                                <span class="status-not-configured"><i class="fas fa-exclamation-circle"></i> Not Configured</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="recipient-input mb-4">
                        <label id="recipientLabel" for="notification_recipient" class="form-label fw-bold">
                            Email Address <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               id="notification_recipient" 
                               class="form-control" 
                               placeholder="Enter email address (e.g., test@example.com)">
                        <div class="form-text">Enter the recipient to receive the test notification</div>
                    </div>

                    <div id="notificationStatus" class="mb-3"></div>

                    <button type="button" id="sendNotificationBtn" class="btn btn-primary" onclick="sendTestNotification()">
                        <i class="fas fa-paper-plane me-2"></i>Send Test Notification
                    </button>
                </div>
            </div>

            <!-- CRUD Notification Settings -->
            <div class="agri-card mb-4">
                <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.9), rgba(167, 139, 250, 0.9));">
                    <h3><i class="fas fa-cog me-2"></i>CRUD Notification Settings</h3>
                </div>
                <div class="agri-card-body">
                    <p class="text-muted mb-4">Control which CRUD events trigger notifications to system administrators.</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered notification-settings-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Module</th>
                                    <th class="text-center">Created</th>
                                    <th class="text-center">Updated</th>
                                    <th class="text-center">Deleted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $modules = [
                                        'egg_production' => 'Egg Production',
                                        'egg_sales' => 'Egg Sales',
                                        'bird_sales' => 'Bird Sales',
                                        'expenses' => 'Expenses',
                                        'employees' => 'Employees',
                                    ];
                                    $events = ['created', 'updated', 'deleted'];
                                @endphp
                                
                                @foreach($modules as $moduleKey => $moduleLabel)
                                <tr>
                                    <td class="fw-bold">
                                        <i class="fas fa-{{ $moduleKey === 'egg_production' ? 'egg' : ($moduleKey === 'egg_sales' ? 'shopping-cart' : ($moduleKey === 'bird_sales' ? 'dove' : ($moduleKey === 'expenses' ? 'receipt' : 'users'))) }} me-2 text-muted"></i>
                                        {{ $moduleLabel }}
                                    </td>
                                    @foreach($events as $event)
                                    @php
                                        $setting = isset($notificationSettings[$moduleKey]) 
                                            ? $notificationSettings[$moduleKey]->firstWhere('event', $event) 
                                            : null;
                                        $isEnabled = $setting ? $setting->enabled : true;
                                    @endphp
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input notification-toggle" 
                                                   type="checkbox" 
                                                   role="switch"
                                                   data-module="{{ $moduleKey }}"
                                                   data-event="{{ $event }}"
                                                   {{ $isEnabled ? 'checked' : '' }}
                                                   onchange="toggleNotificationSetting(this)">
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        When enabled, notifications are sent to all system administrators via configured channels (SMS, Email, GekyChat).
                    </div>
                </div>
            </div>

            <!-- Cache Management -->
            <div class="agri-card">
                <div class="agri-card-header" style="background: linear-gradient(135deg, rgba(244, 67, 54, 0.9), rgba(239, 83, 80, 0.9));">
                    <h3><i class="fas fa-broom me-2"></i>Cache Management</h3>
                </div>
                <div class="agri-card-body">
                    <p class="text-muted mb-4">Clear application cache to refresh configurations and views.</p>
                    
                    <button type="button" class="btn btn-warning" onclick="clearCache()">
                        <i class="fas fa-broom me-2"></i>Clear Application Cache
                    </button>
                    <div class="form-text mt-2">This will clear config, view, and application cache.</div>
                </div>
            </div>
        </div>

        <!-- Configuration Status -->
        <div class="col-lg-4">
            <div class="agri-card mb-4">
                <div class="agri-card-header">
                    <h3><i class="fas fa-info-circle me-2"></i>Configuration Status</h3>
                </div>
                <div class="agri-card-body">
                    <div class="config-status-list">
                        <div class="config-item">
                            <div class="config-name">
                                <i class="fas fa-envelope me-2"></i>Email
                            </div>
                            <div class="config-value">
                                @if($mailConfigured)
                                    <span class="badge bg-success">Configured</span>
                                @else
                                    <span class="badge bg-secondary">Log Mode</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-name">
                                <i class="fas fa-sms me-2"></i>SMS
                            </div>
                            <div class="config-value">
                                @if($smsConfigured)
                                    <span class="badge bg-success">{{ $providerName ?: 'Configured' }}</span>
                                @else
                                    <span class="badge bg-secondary">Log Mode</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-name">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </div>
                            <div class="config-value">
                                @if($whatsappConfigured)
                                    <span class="badge bg-success">Configured</span>
                                @else
                                    <span class="badge bg-secondary">Log Mode</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-name">
                                <i class="fab fa-telegram me-2"></i>Telegram
                            </div>
                            <div class="config-value">
                                @if($telegramConfigured)
                                    <span class="badge bg-success">Configured</span>
                                @else
                                    <span class="badge bg-secondary">Log Mode</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="config-item">
                            <div class="config-name">
                                <i class="fas fa-comments me-2"></i>GekyChat
                            </div>
                            <div class="config-value">
                                @if($gekychatConfigured)
                                    <span class="badge bg-success">Configured</span>
                                @else
                                    <span class="badge bg-secondary">Log Mode</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environment Info -->
            <div class="agri-card">
                <div class="agri-card-header">
                    <h3><i class="fas fa-server me-2"></i>Environment</h3>
                </div>
                <div class="agri-card-body">
                    <div class="config-status-list">
                        <div class="config-item">
                            <div class="config-name">Environment</div>
                            <div class="config-value">
                                <span class="badge {{ config('app.env') === 'production' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst(config('app.env')) }}
                                </span>
                            </div>
                        </div>
                        <div class="config-item">
                            <div class="config-name">Debug Mode</div>
                            <div class="config-value">
                                <span class="badge {{ config('app.debug') ? 'bg-warning' : 'bg-success' }}">
                                    {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                        <div class="config-item">
                            <div class="config-name">Timezone</div>
                            <div class="config-value">
                                <span class="text-muted">{{ config('app.timezone') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .channel-card {
        display: block;
        cursor: pointer;
    }
    
    .channel-card input[type="radio"] {
        display: none;
    }
    
    .channel-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.25rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    
    .channel-card:hover .channel-content {
        border-color: var(--primary);
        background: white;
    }
    
    .channel-card input[type="radio"]:checked + .channel-content {
        border-color: var(--primary);
        background: rgba(46, 125, 50, 0.05);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
    }
    
    .channel-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .channel-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    
    .channel-status {
        font-size: 0.75rem;
    }
    
    .status-configured {
        color: #10b981;
    }
    
    .status-not-configured {
        color: #ef4444;
    }
    
    .config-status-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .config-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .config-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .config-name {
        font-weight: 500;
        color: #374151;
    }
    
    .notification-settings-table .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }
    
    .notification-settings-table .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .notification-toggle.updating {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<script>
    function handleChannelChange() {
        const channel = document.querySelector('input[name="notification_channel"]:checked').value;
        const recipientInput = document.getElementById('notification_recipient');
        const recipientLabel = document.getElementById('recipientLabel');
        
        if (channel === 'email') {
            recipientInput.type = 'email';
            recipientInput.placeholder = 'Enter email address (e.g., test@example.com)';
            recipientLabel.innerHTML = 'Email Address <span class="text-danger">*</span>';
        } else if (channel === 'telegram') {
            recipientInput.type = 'text';
            recipientInput.placeholder = 'Enter Telegram chat ID or username (e.g., @username or 123456789)';
            recipientLabel.innerHTML = 'Telegram Chat ID/Username <span class="text-danger">*</span>';
        } else if (channel === 'gekychat') {
            recipientInput.type = 'tel';
            recipientInput.placeholder = 'Enter phone number (e.g., 0XXXXXXXXX or +233XXXXXXXXX)';
            recipientLabel.innerHTML = 'Phone Number <span class="text-danger">*</span>';
        } else {
            recipientInput.type = 'tel';
            recipientInput.placeholder = 'Enter phone number (e.g., 0XXXXXXXXX or +233XXXXXXXXX)';
            recipientLabel.innerHTML = 'Phone Number <span class="text-danger">*</span>';
        }
    }

    function sendTestNotification() {
        const channel = document.querySelector('input[name="notification_channel"]:checked').value;
        const recipient = document.getElementById('notification_recipient').value;
        const submitBtn = document.getElementById('sendNotificationBtn');
        const statusDiv = document.getElementById('notificationStatus');

        if (!recipient) {
            statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Please enter a recipient</div>';
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
        statusDiv.innerHTML = '';

        fetch('{{ route("settings.test-notification") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                channel: channel,
                recipient: recipient
            })
        })
        .then(async response => {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                const text = await response.text();
                throw new Error('Server returned HTML instead of JSON. Status: ' + response.status);
            }
        })
        .then(data => {
            if (data.success) {
                statusDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + data.message + '</div>';
                document.getElementById('notification_recipient').value = '';
            } else {
                let message = data.message || 'An error occurred';
                if (data.missing_config && data.missing_config.length > 0) {
                    message += '<br><br><strong>Missing Configuration:</strong><ul class="mb-0 mt-2">';
                    data.missing_config.forEach(config => {
                        message += '<li>' + config + '</li>';
                    });
                    message += '</ul>';
                }
                if (data.errors) {
                    message += '<br><br><strong>Errors:</strong><ul class="mb-0 mt-2">';
                    Object.keys(data.errors).forEach(key => {
                        data.errors[key].forEach(err => {
                            message += '<li>' + err + '</li>';
                        });
                    });
                    message += '</ul>';
                }
                statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + message + '</div>';
            }
        })
        .catch(error => {
            statusDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>An error occurred: ' + error.message + '</div>';
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Send Test Notification';
        });
    }

    function clearCache() {
        if (!confirm('Clear application cache? This may temporarily increase page load times.')) return;
        
        fetch('{{ route("settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Failed to clear cache: ' + error.message);
        });
    }

    function toggleNotificationSetting(checkbox) {
        const module = checkbox.dataset.module;
        const event = checkbox.dataset.event;
        const enabled = checkbox.checked;
        
        checkbox.classList.add('updating');
        
        fetch('{{ route("settings.notification-settings") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                module: module,
                event: event,
                enabled: enabled
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                checkbox.checked = !enabled;
                alert('Failed to update setting: ' + data.message);
            }
        })
        .catch(error => {
            checkbox.checked = !enabled;
            alert('Failed to update setting: ' + error.message);
        })
        .finally(() => {
            checkbox.classList.remove('updating');
        });
    }
</script>
@endsection
