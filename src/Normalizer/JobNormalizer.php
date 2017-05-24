<?php

declare(strict_types=1);

namespace Normalizer;

use Manager\ApiManager;
use Model\Resource;

class JobNormalizer implements NormalizerInterface
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
        $this->apiManager = $apiManager;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Resource $resource, array $data): array
    {
        $data = [
            'title' => $data['title'],
        ];

        return array_merge(
            $this->retrieveInitialData($data),
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        return Resource::JOB()->equals($resource);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function retrieveInitialData(array $data): array
    {
        $data = $this->apiManager->findOne(Resource::JOB(), ['title' => $data]);

        return null === $data ? [] : $data;
    }
}
