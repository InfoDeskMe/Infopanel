<?php

namespace App\Tests\Service;

use App\Service\JsonStore;
use PHPUnit\Framework\TestCase;

class JsonStoreTest extends TestCase
{
    private string $dir;
    private JsonStore $store;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir().'/json_store_test_'.uniqid();
        mkdir($this->dir, 0777, true);
        $this->store = new JsonStore();
    }

    protected function tearDown(): void
    {
        $this->removeRecursively($this->dir);
    }

    private function removeRecursively(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        foreach (array_diff(scandir($dir), ['.', '..']) as $entry) {
            $path = $dir.'/'.$entry;
            is_dir($path) ? $this->removeRecursively($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function testReadReturnsEmptyArrayForMissingFile(): void
    {
        $this->assertSame([], $this->store->read($this->dir.'/does-not-exist.json'));
    }

    public function testWriteThenReadRoundTrips(): void
    {
        $path = $this->dir.'/data.json';
        $this->store->write($path, ['version' => 2, 'news' => [['id' => 'a', 'headline' => 'Test']]]);

        $this->assertSame(
            ['version' => 2, 'news' => [['id' => 'a', 'headline' => 'Test']]],
            $this->store->read($path)
        );
    }

    public function testWriteDoesNotLeaveTempFilesBehind(): void
    {
        $path = $this->dir.'/data.json';
        $this->store->write($path, ['a' => 1]);

        $entries = array_diff(scandir($this->dir), ['.', '..']);
        $this->assertSame(['data.json'], array_values($entries));
    }

    public function testWriteCreatesBackupOfPreviousVersion(): void
    {
        $path = $this->dir.'/data.json';
        $this->store->write($path, ['version' => 1]);
        $this->store->write($path, ['version' => 2]);

        $this->assertFileExists($path.'.bak');
        $this->assertSame(['version' => 1], json_decode(file_get_contents($path.'.bak'), true));
        $this->assertSame(['version' => 2], $this->store->read($path));
    }

    public function testReadThrowsOnCorruptJson(): void
    {
        $path = $this->dir.'/data.json';
        file_put_contents($path, '{ this is not valid json');

        $this->expectException(\RuntimeException::class);
        $this->store->read($path);
    }

    public function testWriteCreatesMissingParentDirectory(): void
    {
        $path = $this->dir.'/nested/deep/data.json';
        $this->store->write($path, ['ok' => true]);

        $this->assertSame(['ok' => true], $this->store->read($path));
    }

    public function testUpdateAppliesModifierUnderLock(): void
    {
        $path = $this->dir.'/data.json';
        $this->store->write($path, ['count' => 1]);

        $this->store->update($path, function (array $data) {
            ++$data['count'];

            return $data;
        });

        $this->assertSame(['count' => 2], $this->store->read($path));
    }
}
