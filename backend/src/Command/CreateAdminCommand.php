<?php

namespace App\Command;

use App\Entity\Admin;
use App\Security\Roles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates the initial administrator account with a randomly generated password. Does nothing if an admin already exists.'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL, 'Email address for the initial admin', 'admin@admin.com');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        if ($this->entityManager->getRepository(Admin::class)->count([]) > 0) {
            $io->writeln('An admin account already exists, skipping.');

            return Command::SUCCESS;
        }

        $plainPassword = bin2hex(random_bytes(12));

        $admin = new Admin();
        $admin->setEmail($email);
        $admin->setRoles([Roles::ROLE_ADMIN]);
        $admin->setMustChangePassword(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $plainPassword));

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success('Initial admin account created.');
        $io->writeln("Email:    {$email}");
        $io->writeln("Password: {$plainPassword}");
        $io->warning('This password is shown only once and will be required to change on first login.');

        return Command::SUCCESS;
    }
}
