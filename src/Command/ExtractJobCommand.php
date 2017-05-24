<?php

declare(strict_types=1);

namespace Command;

use Extractor\ExtractorInterface;
use Manager\ApiManager;
use Model\Resource;
use Normalizer\NormalizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractJobCommand extends Command
{
    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @param ExtractorInterface  $extractor
     * @param ApiManager          $apiManager
     * @param NormalizerInterface $normalizer
     */
    public function __construct(ExtractorInterface $extractor, ApiManager $apiManager, NormalizerInterface $normalizer)
    {
        parent::__construct();

        $this->apiManager = $apiManager;
        $this->extractor = $extractor;
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setName('extract:job');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobs = $this->extractor->extract(Resource::JOB());

        $output->writeln('Normalize extracted jobs...');
        $progress = new ProgressBar($output, count($jobs));
        $jobs = array_map(
            function (array $job) use ($progress): array {
                $job = $this->normalizer->normalize(Resource::JOB(), $job);
                $progress->advance();

                return $job;
            },
            $jobs
        );
        $progress->finish();
        $output->write(PHP_EOL.'<info>Normalization completed</info>'.PHP_EOL);
        $output->writeln('---');

        $output->writeln('Send jobs to API...');
        $progress = new ProgressBar($output, count($jobs));
        foreach ($jobs as $job) {
            $this->apiManager->save(Resource::JOB(), $job);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(PHP_EOL.'<info>Sending completed</info>'.PHP_EOL);

        return 0;
    }
}
