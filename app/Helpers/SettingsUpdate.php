<?php

namespace App\Helpers;
use App\Models\Setting;

class SettingsUpdate
{
    public static function update($key ,$value)
    {
        return Setting::where('key', $key)->update(['value' => $value]);
    }
}
