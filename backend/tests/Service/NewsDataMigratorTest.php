<?php

namespace App\Tests\Service;

use App\Service\NewsDataMigrator;
use PHPUnit\Framework\TestCase;

class NewsDataMigratorTest extends TestCase
{
    private NewsDataMigrator $migrator;

    protected function setUp(): void
    {
        $this->migrator = new NewsDataMigrator();
    }

    public function testEmptyDataNeedsNoMigration(): void
    {
        $this->assertFalse($this->migrator->needsMigration([]));
    }

    public function testAlreadyVersionedDataNeedsNoMigration(): void
    {
        $data = ['version' => 2, 'news' => []];
        $this->assertFalse($this->migrator->needsMigration($data));
    }

    public function testLegacyKeyedObjectNeedsMigration(): void
    {
        $data = [
            'news0' => ['headline' => 'A', 'id' => '0'],
            'news1' => ['headline' => 'B', 'id' => '132132'],
        ];
        $this->assertTrue($this->migrator->needsMigration($data));
    }

    public function testMigrateConvertsLegacyFormatPreservingAllEntries(): void
    {
        $legacy = [
            'news0' => ['headline' => 'A', 'id' => '0'],
            'news2' => ['headline' => 'C', 'id' => '123120'],
        ];

        $migrated = $this->migrator->migrate($legacy);

        $this->assertSame(2, $migrated['version']);
        $this->assertCount(2, $migrated['news']);
        $this->assertSame(['headline' => 'A', 'id' => '0'], $migrated['news'][0]);
        $this->assertSame(['headline' => 'C', 'id' => '123120'], $migrated['news'][1]);
    }

    public function testMigrateOnEmptyDataProducesEmptyVersionedStructure(): void
    {
        $this->assertSame(['version' => 2, 'news' => []], $this->migrator->migrate([]));
    }

    public function testMigrateIsIdempotentForAlreadyVersionedData(): void
    {
        $data = ['version' => 2, 'news' => [['id' => 'x']]];
        $this->assertSame($data, $this->migrator->migrate($data));
    }
}
