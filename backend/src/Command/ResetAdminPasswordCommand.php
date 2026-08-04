<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:reset-admin-password',
    description: 'Sets a new randomly generated password for an admin account (e.g. when locked out).'
)]
class ResetAdminPasswordCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::OPTIONAL, 'Email address of the account to reset; defaults to the first admin found');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $repository = $this->entityManager->getRepository(Admin::class);
        $admin = $email !== null ? $repository->findOneBy(['email' => $email]) : $repository->findOneBy([], ['id' => 'ASC']);

        if ($admin === null) {
            $io->error($email !== null ? "No account found for {$email}." : 'No admin account exists yet -- use app:create-admin instead.');

            return Command::FAILURE;
        }

        $plainPassword = bin2hex(random_bytes(12));
        $admin->setPassword($this->passwordHasher->hashPassword($admin, $plainPassword));
        $admin->setMustChangePassword(true);
        $this->entityManager->flush();

        $io->success('Password reset.');
        $io->writeln("Email:    {$admin->getEmail()}");
        $io->writeln("Password: {$plainPassword}");
        $io->warning('This password is shown only once and will be required to change on first login.');

        return Command::SUCCESS;
    }
}
