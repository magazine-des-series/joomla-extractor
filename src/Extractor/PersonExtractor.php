<?php

declare(strict_types=1);

namespace Extractor;

use Manager\DatabaseManager;
use Model\Resource;

class PersonExtractor implements ExtractorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @var ExtractorInterface
     */
    private $tvSeriesExtractor;

    /**
     * @param ExtractorInterface $tvSeriesExtractor
     * @param DatabaseManager    $databaseManager
     */
    public function __construct(ExtractorInterface $tvSeriesExtractor, DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
        $this->tvSeriesExtractor = $tvSeriesExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Resource $resource): array
    {
        return array_filter(
            array_merge(
                $this->extractFromArticle(),
                $this->extractFromTvSeries()
            ),
            function (array $person): bool {
                return !in_array($person['name'], ['Shaw'], true);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        return Resource::PERSON()->equals($resource);
    }

    /**
     * @return array
     */
    private function extractFromArticle(): array
    {
        $query = $this->databaseManager->createQueryBuilder()
            ->select(
                [
                    'jos_content.title as name',
                    'jos_content.fulltext as description',
                    'jos_categories.title as job',
                ]
            )
            ->from('jos_content', 'jos_content')
            ->leftJoin('jos_content', 'jos_categories', 'jos_categories', 'jos_categories.id = jos_content.catid')
            ->where('jos_content.catid in ( 57 , 58 , 59 ) AND jos_content.id <> 115');

        return $query->execute()->fetchAll();
    }

    /**
     * @return array
     */
    private function extractFromTvSeries(): array
    {
        $actors = [];
        foreach ($this->tvSeriesExtractor->extract(Resource::TV_SERIES()) as $tvSeries) {
            $actors = array_merge(
                $actors,
                array_map(
                    function (string $actorName): array {
                        return [
                            'name' => $actorName,
                        ];
                    },
                    $tvSeries['actors']
                )
            );
        }

        return $actors;
    }
}
