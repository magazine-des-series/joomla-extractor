<?php

declare(strict_types=1);

namespace Command;

use Manager\ApiManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PingApiCommand extends Command
{
    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @param ApiManager $apiManager
     */
    public function __construct(ApiManager $apiManager)
    {
        parent::__construct();

        $this->apiManager = $apiManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('ping:api');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->apiManager->ping()) {
            $output->writeln('<info>Ping OK</info>');

            return 0;
        }

        $output->writeln('<error>Ping KO</error>');

        return 1;
    }
}
