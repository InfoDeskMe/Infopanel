<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Converts the legacy Admin.roles format {"role":"ROLE_ADMIN"} into the
 * flat list format ["ROLE_ADMIN"] that Symfony's security component expects.
 * Without this, restricting access by role (SEC-1) would lock out every
 * existing account created before the fix.
 */
final class Version20260724184057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Normalize Admin.roles from {"role":"X"} to ["X"]';
    }

    public function up(Schema $schema): void
    {
        $rows = $this->connection->fetchAllAssociative('SELECT id, roles FROM admin');

        foreach ($rows as $row) {
            $decoded = json_decode($row['roles'], true);

            if (!is_array($decoded)) {
                continue;
            }

            if (array_key_exists('role', $decoded) && is_string($decoded['role'])) {
                $normalized = [$decoded['role']];
            } elseif (array_is_list($decoded)) {
                // already in the correct format
                continue;
            } else {
                // unknown legacy shape: fall back to any string values found
                $normalized = array_values(array_filter($decoded, 'is_string'));
            }

            $this->connection->executeStatement(
                'UPDATE admin SET roles = :roles WHERE id = :id',
                ['roles' => json_encode(array_values(array_unique($normalized))), 'id' => $row['id']]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $rows = $this->connection->fetchAllAssociative('SELECT id, roles FROM admin');

        foreach ($rows as $row) {
            $decoded = json_decode($row['roles'], true);

            if (!is_array($decoded) || !array_is_list($decoded) || empty($decoded)) {
                continue;
            }

            $this->connection->executeStatement(
                'UPDATE admin SET roles = :roles WHERE id = :id',
                ['roles' => json_encode(['role' => $decoded[0]]), 'id' => $row['id']]
            );
        }
    }
}
