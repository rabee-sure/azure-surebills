<?php

namespace App\Services;

use App\Models\BasicSettings;

class BasicSettingsService
{
    public function getSettings(): array
    {
        return BasicSettings::query()
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->getSettings()[$key] ?? $default;
    }
}
