<?php

declare(strict_types=1);

namespace Command;

use Extractor\ExtractorInterface;
use Manager\ApiManager;
use Manager\DatabaseManager;
use Model\Resource;
use Normalizer\NormalizerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExtractPersonCommand extends Command
{
    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @var DatabaseManager
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
        $this->setName('extract:person');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $people = $this->extractor->extract(Resource::PERSON());

        $output->writeln('Normalize extracted people...');
        $progress = new ProgressBar($output, count($people));
        $people = array_map(
            function (array $person) use ($progress): array {
                $person = $this->normalizer->normalize(Resource::PERSON(), $person);
                $progress->advance();

                return $person;
            },
            $people
        );
        $progress->finish();
        $output->write(PHP_EOL.'<info>Normalization completed</info>'.PHP_EOL);

        $output->writeln('---');

        $output->writeln('Send people to API...');
        $progress = new ProgressBar($output, count($people));
        foreach ($people as $person) {
            $this->apiManager->save(Resource::PERSON(), array_merge($person, $person));
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(PHP_EOL.'<info>Sending completed</info>'.PHP_EOL);

        return 0;
    }
}
