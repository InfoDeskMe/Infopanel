<?php

namespace App\Tests\Repository;

use App\Repository\JsonContentRepository;
use App\Service\JsonStore;
use App\Service\NewsDataMigrator;
use PHPUnit\Framework\TestCase;

class JsonContentRepositoryTest extends TestCase
{
    private string $path;
    private JsonContentRepository $repository;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/json_content_repo_test_'.uniqid().'.json';
        $this->repository = new JsonContentRepository(new JsonStore(), new NewsDataMigrator(), $this->path);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->path.'*') ?: [] as $file) {
            @unlink($file);
        }
    }

    public function testAddGeneratesIdAndAppends(): void
    {
        $id = $this->repository->add(['headline' => 'First']);

        $this->assertNotEmpty($id);
        $all = $this->repository->findAll();
        $this->assertCount(1, $all);
        $this->assertSame($id, $all[0]['id']);
        $this->assertSame('First', $all[0]['headline']);
    }

    /**
     * This is the DATA-2 regression: creating entries with keys derived from
     * count($data) meant deleting the middle entry and adding a new one
     * silently overwrote the last remaining entry, because the new key
     * collided with an existing one.
     */
    public function testDeletingMiddleEntryThenAddingNewOneDoesNotOverwriteAnything(): void
    {
        $idA = $this->repository->add(['headline' => 'A']);
        $idB = $this->repository->add(['headline' => 'B']);
        $idC = $this->repository->add(['headline' => 'C']);

        $this->repository->remove($idB);
        $idD = $this->repository->add(['headline' => 'D']);

        $all = $this->repository->findAll();
        $headlinesById = array_column($all, 'headline', 'id');

        $this->assertCount(3, $all);
        $this->assertSame('A', $headlinesById[$idA]);
        $this->assertSame('C', $headlinesById[$idC]);
        $this->assertSame('D', $headlinesById[$idD]);
        $this->assertArrayNotHasKey($idB, $headlinesById);
    }

    public function testUpdateModifiesOnlyTheMatchingEntry(): void
    {
        $idA = $this->repository->add(['headline' => 'A']);
        $idB = $this->repository->add(['headline' => 'B']);

        $found = $this->repository->update($idB, ['headline' => 'B-edited']);

        $this->assertTrue($found);
        $all = $this->repository->findAll();
        $headlinesById = array_column($all, 'headline', 'id');
        $this->assertSame('A', $headlinesById[$idA]);
        $this->assertSame('B-edited', $headlinesById[$idB]);
    }

    public function testUpdateWithUnknownIdReturnsFalseAndChangesNothing(): void
    {
        $this->repository->add(['headline' => 'A']);

        $found = $this->repository->update('does-not-exist', ['headline' => 'X']);

        $this->assertFalse($found);
        $this->assertCount(1, $this->repository->findAll());
    }

    public function testRemoveWithUnknownIdReturnsFalse(): void
    {
        $this->repository->add(['headline' => 'A']);

        $found = $this->repository->remove('does-not-exist');

        $this->assertFalse($found);
        $this->assertCount(1, $this->repository->findAll());
    }

    public function testRemovingTheLastEntryLeavesAnEmptyArrayNotAMissingFile(): void
    {
        $id = $this->repository->add(['headline' => 'Only one']);
        $this->repository->remove($id);

        $this->assertSame([], $this->repository->findAll());
        $this->assertFileExists($this->path);
    }

    public function testFindAllOnFreshRepositoryIsEmptyNotAnError(): void
    {
        $this->assertSame([], $this->repository->findAll());
    }
}
