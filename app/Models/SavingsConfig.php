<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingsConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
        'type', // string, integer, float, boolean
        'is_editable'
    ];

    protected $casts = [
        'is_editable' => 'boolean',
        'value' => 'string'
    ];

    /**
     * Get a configuration value
     */
    public static function getValue($key, $default = null)
    {
        $config = self::where('key', $key)->first();
        
        if (!$config) {
            return $default;
        }

        // Cast the value based on type
        switch ($config->type) {
            case 'integer':
                return (int) $config->value;
            case 'float':
                return (float) $config->value;
            case 'boolean':
                return (bool) $config->value;
            default:
                return $config->value;
        }
    }

    /**
     * Set a configuration value
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        $config = self::where('key', $key)->first();
        
        if (!$config) {
            $config = self::create([
                'key' => $key,
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
                'is_editable' => true
            ]);
        } else {
            $config->update([
                'value' => (string) $value,
                'type' => $type,
                'description' => $description ?: $config->description
            ]);
        }

        return $config;
    }

    /**
     * Get all editable configurations
     */
    public static function getEditableConfigs()
    {
        return self::where('is_editable', true)->orderBy('key')->get();
    }

    /**
     * Initialize default savings configurations
     */
    public static function initializeDefaults()
    {
        $defaults = [
            [
                'key' => 'daily_goal',
                'value' => '80000',
                'type' => 'float',
                'description' => 'Daily savings goal in Naira (₦)'
            ],
            [
                'key' => 'max_daily_collections',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Maximum number of collections per day'
            ],
            [
                'key' => 'min_collection_amount',
                'value' => '15000',
                'type' => 'float',
                'description' => 'Minimum amount to collect per transaction (₦)'
            ],
            [
                'key' => 'max_collection_amount',
                'value' => '20000',
                'type' => 'float',
                'description' => 'Maximum amount to collect per transaction (₦)'
            ],
            [
                'key' => 'collection_interval_hours',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Hours between automatic collections'
            ],
            [
                'key' => 'min_balance_required',
                'value' => '15000',
                'type' => 'float',
                'description' => 'Minimum balance required for collection (₦)'
            ],
            [
                'key' => 'default_monthly_goal',
                'value' => '1600000',
                'type' => 'float',
                'description' => 'Default monthly savings goal (₦)'
            ],
            [
                'key' => 'collection_frequency',
                'value' => 'twice_daily',
                'type' => 'string',
                'description' => 'Collection frequency (once_daily, twice_daily, hourly)'
            ]
        ];

        foreach ($defaults as $default) {
            self::setValue(
                $default['key'],
                $default['value'],
                $default['type'],
                $default['description']
            );
        }
    }
}
