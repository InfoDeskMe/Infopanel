<?php

namespace App\Command;

use App\Service\JsonStore;
use App\Service\NewsDataMigrator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:migrate-json',
    description: 'Migrates data.json and data_ticker.json from the legacy keyed-object format to the versioned array format.'
)]
class MigrateJsonCommand extends Command
{
    public function __construct(
        private readonly JsonStore $jsonStore,
        private readonly NewsDataMigrator $migrator,
        private readonly ParameterBagInterface $params,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dataDir = $this->params->get('app.data_dir');

        foreach (['/data.json', '/data_ticker.json'] as $suffix) {
            $path = $dataDir.$suffix;
            $raw = $this->jsonStore->read($path);

            if (!$this->migrator->needsMigration($raw)) {
                $io->writeln("{$suffix}: already up to date, skipped.");
                continue;
            }

            $this->jsonStore->write($path, $this->migrator->migrate($raw));
            $io->writeln("{$suffix}: migrated to versioned array format.");
        }

        return Command::SUCCESS;
    }
}
