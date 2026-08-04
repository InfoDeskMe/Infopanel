<?php

namespace App\Repository;

use App\Service\JsonStore;

class SettingsRepository
{
    private const DEFAULTS = [
        'generalReload' => 60000,
        'newsNumber' => 4,
        'layout_type' => 0,
        'tickerSpeed' => 10,
        'tickerOrientation' => 'left',
        'tickerHeight' => '80',
        'showTicker' => 1,
        'slideDelay' => 8000,
    ];

    public function __construct(
        private readonly JsonStore $jsonStore,
        private readonly string $path,
    ) {
    }

    public function load(): array
    {
        $data = $this->jsonStore->read($this->path);

        return empty($data) ? self::DEFAULTS : $data;
    }

    public function save(array $settings): void
    {
        $this->jsonStore->write($this->path, $settings);
    }
}
