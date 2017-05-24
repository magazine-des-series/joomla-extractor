<?php

declare(strict_types=1);

namespace Extractor;

use Manager\DatabaseManager;
use Model\Resource;

class JobExtractor implements ExtractorInterface
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
                    'jos_categories.title as title',
                ]
            )
            ->from('jos_categories', 'jos_categories')
            ->where('jos_categories.id in ( 57 , 58 , 59 )');

        return $query->execute()->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        return Resource::JOB()->equals($resource);
    }
}
