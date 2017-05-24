<?php

declare(strict_types=1);

namespace Extractor;

use Manager\DatabaseManager;
use Model\Resource;

class TvSeriesExtractor implements ExtractorInterface
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Resource $resource): array
    {
        $query = $this->databaseManager->createQueryBuilder()
            ->select(
                [
                    'jos_content.title as name',
                    'jos_content.fulltext as description',
                ]
            )
            ->from('jos_content', 'jos_content')
            ->leftJoin('jos_content', 'jos_categories', 'jos_categories', 'jos_categories.id = jos_content.catid')
            ->where('jos_categories.section in ( 8 , 30 )');

        return array_map(
            function (array $tvSeries): array {
                $actors = '';
                $name = $tvSeries['name'];
                if (false !== strpos($name, ' avec ')) {
                    $actors = explode(' avec ', $name)[1];
                    $name = explode(' avec ', $name)[0];
                }

                $actors = str_replace(' et ', ', ', $actors);
                if (false !== strpos($actors, ', ')) {
                    $actors = explode(', ', $actors);
                }
                if (!is_array($actors)) {
                    $actors = [];
                }

                return array_merge(
                    $tvSeries,
                    [
                        'name' => $name,
                        'actors' => $actors,
                    ]
                );
            },
            $query->execute()->fetchAll()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        return Resource::TV_SERIES()->equals($resource);
    }
}
