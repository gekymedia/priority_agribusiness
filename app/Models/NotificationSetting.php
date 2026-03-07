<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class NotificationSetting extends Model
{
    protected $fillable = [
        'module',
        'event',
        'enabled',
        'channels',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'channels' => 'array',
    ];

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public static function isEnabled(string $module, string $event): bool
    {
        $cacheKey = "notification_setting_{$module}_{$event}";

        try {
            return Cache::remember($cacheKey, 300, function () use ($module, $event) {
                return self::resolveEnabled($module, $event);
            });
        } catch (\Throwable $e) {
            return self::resolveEnabled($module, $event);
        }
    }

    protected static function resolveEnabled(string $module, string $event): bool
    {
        $setting = self::where('module', $module)
            ->where('event', $event)
            ->first();

        return $setting ? $setting->enabled : true;
    }

    public static function getChannels(string $module, string $event): array
    {
        $setting = self::where('module', $module)
            ->where('event', $event)
            ->first();
        
        return $setting && $setting->channels ? $setting->channels : ['sms', 'email', 'gekychat'];
    }

    public static function clearCache(): void
    {
        $modules = ['egg_production', 'egg_sales', 'bird_sales', 'expenses', 'employees'];
        $events = ['created', 'updated', 'deleted'];

        try {
            foreach ($modules as $module) {
                foreach ($events as $event) {
                    Cache::forget("notification_setting_{$module}_{$event}");
                }
            }
        } catch (\Throwable $e) {
            // Ignore if cache store unavailable (e.g. cache table missing)
        }
    }

    public static function seedDefaults(): void
    {
        $modules = [
            'egg_production' => 'Egg Production',
            'egg_sales' => 'Egg Sales',
            'bird_sales' => 'Bird Sales',
            'expenses' => 'Expenses',
            'employees' => 'Employees',
        ];
        
        $events = ['created', 'updated', 'deleted'];
        
        foreach ($modules as $module => $label) {
            foreach ($events as $event) {
                self::firstOrCreate(
                    ['module' => $module, 'event' => $event],
                    ['enabled' => true, 'channels' => ['sms', 'email', 'gekychat']]
                );
            }
        }
    }
}
