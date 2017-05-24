<?php

declare(strict_types=1);

namespace Normalizer;

use Manager\ApiManager;
use Model\Resource;

class TvSeriesNormalizer implements NormalizerInterface
{
    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @var NormalizerInterface
     */
    private $personNormalizer;

    /**
     * @param ApiManager          $apiManager
     * @param NormalizerInterface $personNormalizer
     */
    public function __construct(ApiManager $apiManager, NormalizerInterface $personNormalizer)
    {
        $this->apiManager = $apiManager;
        $this->personNormalizer = $personNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Resource $resource, array $data): array
    {
        $data = array_merge(
            [
                'description' => $data['description'],
            ],
            $this->normalizeActors($data),
            $this->normalizeName($data)
        );

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
        return Resource::TV_SERIES()->equals($resource);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function normalizeActors(array $data): array
    {
        $actors = array_filter(
            array_map(
                function (string $actorName): array {
                    return $this->personNormalizer->normalize(Resource::PERSON(), ['name' => $actorName]);
                },
                $data['actors']
            ),
            function (array $actor): bool {
                return isset($actor['@id']);
            }
        );

        return [
            'actors' => array_map(
                function (array $actors): string {
                    return $actors['@id'];
                },
                $actors
            ),
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function normalizeName(array $data): array
    {
        return [
            'name' => preg_replace('`avec$`', '', $data['name']),
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function retrieveInitialData(array $data): array
    {
        $data = $this->apiManager->findOne(Resource::TV_SERIES(), ['name' => $data['name']]);

        return null === $data ? [] : $data;
    }
}
