<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileDeleter
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function delete(string $uploadDir, string $filename): void
    {
        $base = realpath($uploadDir);
        if (false === $base) {
            throw new \RuntimeException("Upload directory does not exist: {$uploadDir}");
        }

        $target = realpath($uploadDir.$filename);
        if (false === $target) {
            // Nothing to delete.
            return;
        }

        if (!str_starts_with($target, $base.DIRECTORY_SEPARATOR)) {
            throw new \InvalidArgumentException('Invalid path');
        }

        $filesystem = new Filesystem();
        try {
            $filesystem->remove($target);
        } catch (IOExceptionInterface $e) {
            $this->logger->error('failed to delete image '.$e->getMessage());
            throw new \RuntimeException('Failed to delete file', 0, $e);
        }
    }
}
