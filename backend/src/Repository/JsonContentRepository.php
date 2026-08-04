<?php

namespace App\Repository;

use App\Service\JsonStore;
use App\Service\NewsDataMigrator;

/**
 * Backs both the news and the ticker-news lists -- they only ever differed
 * by file path and field set, never by CRUD behavior.
 */
class JsonContentRepository implements ContentRepositoryInterface
{
    public function __construct(
        private readonly JsonStore $jsonStore,
        private readonly NewsDataMigrator $migrator,
        private readonly string $path,
    ) {
    }

    public function findAll(): array
    {
        $this->migrateIfNeeded();
        $data = $this->migrator->migrate($this->jsonStore->read($this->path));

        return $data['news'];
    }

    public function add(array $item): string
    {
        $id = bin2hex(random_bytes(8));

        $this->jsonStore->update($this->path, function (array $raw) use ($item, $id) {
            $data = $this->migrator->migrate($raw);
            $data['news'][] = ['id' => $id] + $item;

            return $data;
        });

        return $id;
    }

    public function update(string $id, array $item): bool
    {
        $found = false;

        $this->jsonStore->update($this->path, function (array $raw) use ($id, $item, &$found) {
            $data = $this->migrator->migrate($raw);
            $index = array_search($id, array_column($data['news'], 'id'), true);
            if (false !== $index) {
                $data['news'][$index] = ['id' => $id] + $item;
                $found = true;
            }

            return $data;
        });

        return $found;
    }

    public function remove(string $id): bool
    {
        $found = false;

        $this->jsonStore->update($this->path, function (array $raw) use ($id, &$found) {
            $data = $this->migrator->migrate($raw);
            $index = array_search($id, array_column($data['news'], 'id'), true);
            if (false !== $index) {
                array_splice($data['news'], $index, 1);
                $found = true;
            }

            return $data;
        });

        return $found;
    }

    private function migrateIfNeeded(): void
    {
        $this->jsonStore->withLock($this->path, function () {
            $raw = $this->jsonStore->read($this->path);
            if ($this->migrator->needsMigration($raw)) {
                $this->jsonStore->write($this->path, $this->migrator->migrate($raw));
            }
        });
    }
}
