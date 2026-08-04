<?php

namespace App\Repository;

interface ContentRepositoryInterface
{
    public function findAll(): array;

    /**
     * @return string the newly generated ID
     */
    public function add(array $item): string;

    /**
     * @return bool whether an entry with this ID was found and updated
     */
    public function update(string $id, array $item): bool;

    /**
     * @return bool whether an entry with this ID was found and removed
     */
    public function remove(string $id): bool;
}
