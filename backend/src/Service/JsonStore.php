<?php

namespace App\Service;

class JsonStore
{
    /**
     * @return array decoded contents, or an empty array if the file doesn't exist yet
     */
    public function read(string $path): array
    {
        if (!file_exists($path) || 0 === filesize($path)) {
            return [];
        }

        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new \RuntimeException("Cannot read {$path}");
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException("Corrupt JSON in {$path}: ".$e->getMessage(), 0, $e);
        }

        return is_array($data) ? $data : [];
    }

    /**
     * Writes via a temp file + rename so readers never observe a partially written file.
     */
    public function write(string $path, array $data): void
    {
        $this->ensureDirectoryExists($path);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $tmp = $path.'.'.bin2hex(random_bytes(8)).'.tmp';
        if (false === file_put_contents($tmp, $json, LOCK_EX)) {
            throw new \RuntimeException("Cannot write to {$tmp}");
        }

        if (file_exists($path)) {
            @copy($path, $path.'.bak');
        }

        if (!rename($tmp, $path)) {
            @unlink($tmp);
            throw new \RuntimeException("Cannot replace {$path}");
        }
    }

    /**
     * Holds an exclusive lock for the duration of $fn, serializing concurrent
     * read-modify-write cycles against the same file so parallel requests
     * can't silently overwrite each other's changes.
     */
    public function withLock(string $path, callable $fn)
    {
        $this->ensureDirectoryExists($path);

        $lock = fopen($path.'.lock', 'c');
        if (false === $lock) {
            throw new \RuntimeException("Cannot open lock file for {$path}");
        }

        flock($lock, LOCK_EX);
        try {
            return $fn();
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Convenience wrapper for the common case: read, transform, write, all under one lock.
     */
    public function update(string $path, callable $modifier): void
    {
        $this->withLock($path, function () use ($path, $modifier) {
            $data = $this->read($path);
            $data = $modifier($data);
            $this->write($path, $data);
        });
    }

    private function ensureDirectoryExists(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException("Cannot create directory {$dir}");
        }
    }
}
