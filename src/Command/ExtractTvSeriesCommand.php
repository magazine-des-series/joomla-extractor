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

class ExtractTvSeriesCommand extends Command
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
        $this->setName('extract:tv_series');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $tvSeries = $this->extractor->extract(Resource::TV_SERIES());

        $output->writeln('Normalize extracted TV series...');
        $progress = new ProgressBar($output, count($tvSeries));
        $tvSeries = array_map(
            function (array $tvSeries) use ($progress): array {
                $tvSeries = $this->normalizer->normalize(Resource::TV_SERIES(), $tvSeries);
                $progress->advance();

                return $tvSeries;
            },
            $tvSeries
        );
        $progress->finish();
        $output->write(PHP_EOL.'<info>Normalize completed</info>'.PHP_EOL);

        $output->writeln('---');

        $output->writeln('Send TV series to API...');
        $progress = new ProgressBar($output, count($tvSeries));
        foreach ($tvSeries as $tvSeries) {
            $this->apiManager->save(Resource::TV_SERIES(), array_merge($tvSeries, $tvSeries));
            $progress->advance();
        }
        $progress->finish();
        $output->writeln(PHP_EOL.'<info>Sending completed</info>'.PHP_EOL);

        return 0;
    }
}
