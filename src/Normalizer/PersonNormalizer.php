<?php

declare(strict_types=1);

namespace Normalizer;

use Manager\ApiManager;
use Model\Resource;

class PersonNormalizer implements NormalizerInterface
{
    /**
     * @var ApiManager
     */
    private $apiManager;

    /**
     * @var NormalizerInterface
     */
    private $jobNormalizer;

    /**
     * @param ApiManager          $apiManager
     * @param NormalizerInterface $jobNormalizer
     */
    public function __construct(ApiManager $apiManager, NormalizerInterface $jobNormalizer)
    {
        $this->apiManager = $apiManager;
        $this->jobNormalizer = $jobNormalizer;
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
            $this->normalizeJob($data),
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
        return Resource::PERSON()->equals($resource);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function normalizeJob(array $data): array
    {
        if (!isset($data['job'])) {
            return [];
        }

        $job = $this->jobNormalizer->normalize(Resource::JOB(), ['title' => $data['job']]);
        if (!isset($job['@id'])) {
            return [];
        }

        return [
            'jobs' => [
                $job['@id'],
            ],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function normalizeName(array $data): array
    {
        $name = $data['name'];
        if ('.' === substr($name, -1)) {
            $name = substr($name, 0, -1);
        }

        $name = str_replace('O\' ', 'O\'', $name);

        $names = explode(' ', $name);
        $lastName = array_pop($names);
        $firstName = implode(' ', $names);

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function retrieveInitialData(array $data): array
    {
        $data = $this->apiManager->findOne(
            Resource::PERSON(),
            [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
            ]
        );

        return null === $data ? [] : $data;
    }
}
