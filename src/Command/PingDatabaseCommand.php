<?php

declare(strict_types=1);

namespace Command;

use Manager\DatabaseManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PingDatabaseCommand extends Command
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    public function __construct(DatabaseManager $apiManager)
    {
        parent::__construct();

        $this->databaseManager = $apiManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('ping:database');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->databaseManager->ping()) {
            $output->writeln('<info>Ping OK</info>');

            return 0;
        }

        $output->writeln('<error>Ping KO</error>');

        return 1;
    }
}
