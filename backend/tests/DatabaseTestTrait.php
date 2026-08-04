<?php

namespace App\Tests;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Builds a throwaway schema from the current entity mapping (not the real
 * migrations, which use MySQL-specific SQL) against the sqlite database
 * configured in .env.test, so functional tests don't need a real MySQL
 * server or network access.
 */
trait DatabaseTestTrait
{
    private EntityManagerInterface $entityManager;

    private function setUpDatabase(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    private function createUser(string $email, array $roles, string $plainPassword, bool $mustChangePassword = false): Admin
    {
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new Admin();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setMustChangePassword($mustChangePassword);
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
