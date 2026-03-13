<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }
        return static::castValue($setting->value, $setting->type);
    }

    public static function setValue(string $key, $value, ?string $type = null, ?string $group = null): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        if ($type) {
            $setting->type = $type;
        }
        if ($group) {
            $setting->group = $group;
        }
        $setting->save();
    }

    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    public static function bulkUpdate(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $type = match (true) {
                is_int($value) => 'integer',
                is_bool($value) => 'boolean',
                is_array($value) || is_object($value) => 'json',
                is_float($value) => 'float',
                default => 'string',
            };
            $group = str_starts_with($key, 'priority_bank_') ? 'priority_bank' : 'general';
            self::setValue($key, $value, $type, $group);
        }
    }
}
