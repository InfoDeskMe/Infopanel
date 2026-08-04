<?php

namespace App\Service;

/**
 * Converts the legacy data.json / data_ticker.json shape (entries keyed
 * "news0", "news1", ... by their position at creation time) into a plain
 * array under a "news" key. The old keying scheme reused count($data) as
 * the next key, so deleting an entry and adding a new one could reuse and
 * silently overwrite an existing key.
 */
class NewsDataMigrator
{
    public function needsMigration(array $raw): bool
    {
        if (empty($raw)) {
            return false;
        }

        return !array_key_exists('version', $raw) || !array_key_exists('news', $raw);
    }

    public function migrate(array $raw): array
    {
        if (empty($raw)) {
            return ['version' => 2, 'news' => []];
        }
        if (array_key_exists('version', $raw) && array_key_exists('news', $raw)) {
            return $raw;
        }

        return ['version' => 2, 'news' => array_values($raw)];
    }
}
