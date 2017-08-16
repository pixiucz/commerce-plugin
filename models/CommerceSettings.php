<?php namespace Pixiu\Commerce\Models;

use Model;

/**
 * Settings Model
 */
class CommerceSettings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    // A unique code
    public $settingsCode = 'commerce_settings';

    // Reference to field configuration
    public $settingsFields = 'fields.yaml';
}