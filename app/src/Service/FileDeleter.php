<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class FileDeleter
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function delete($uploadDir, $filename)
    {
        $filesystem = new Filesystem();
        $file = $uploadDir . $filename;
        try {
            if($filesystem->exists($file)) {
                $filesystem->remove($file);
            } else {
                echo "No file : " . $file ;

            }
        } catch (FileException $e){
            $this->logger->error('failed to delete image ' . $e->getMessage());
            throw new FileException('Failed to delete file');
        }
    }
}