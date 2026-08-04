<?php

namespace App\Tests\Service;

use App\Service\FileDeleter;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FileDeleterTest extends TestCase
{
    private string $root;
    private string $uploadDir;
    private FileDeleter $deleter;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir().'/file_deleter_test_'.uniqid();
        $this->uploadDir = $this->root.'/uploads/images/';
        mkdir($this->uploadDir, 0777, true);
        $this->deleter = new FileDeleter(new NullLogger());
    }

    protected function tearDown(): void
    {
        foreach ((array) glob($this->uploadDir.'*') as $file) {
            @unlink($file);
        }
        @rmdir($this->uploadDir);
        foreach ((array) glob($this->root.'/*') as $file) {
            @unlink($file);
        }
        @rmdir($this->root);
    }

    public function testDeletesAFileInsideTheUploadDirectory(): void
    {
        $file = $this->uploadDir.'abc123.png';
        file_put_contents($file, 'x');

        $this->deleter->delete($this->uploadDir, 'abc123.png');

        $this->assertFileDoesNotExist($file);
    }

    /**
     * The SEC-2 case: a filename that walks out of the upload directory to
     * an existing file elsewhere on disk must be rejected, not deleted.
     */
    public function testRejectsPathTraversalToAFileOutsideTheUploadDirectory(): void
    {
        $secret = $this->root.'/secret.txt';
        file_put_contents($secret, 'do not delete me');

        $this->expectException(\InvalidArgumentException::class);
        try {
            $this->deleter->delete($this->uploadDir, '../../secret.txt');
        } finally {
            $this->assertFileExists($secret, 'the traversal target must survive the attempt');
        }
    }

    public function testDeletingANonExistentFileIsANoOpNotAnError(): void
    {
        $this->deleter->delete($this->uploadDir, 'never-existed.png');
        $this->addToAssertionCount(1); // reaching this line without an exception is the assertion
    }
}
