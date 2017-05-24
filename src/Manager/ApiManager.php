<?php

declare(strict_types=1);

namespace Manager;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Model\Resource;

class ApiManager
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param resource $resource
     *
     * @return array
     */
    public function all(Resource $resource): array
    {
        $response = $this->client->request('GET', $resource->getUri());

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('Impossible to retrieve collection of "%s".', $resource));
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param resource $resource
     * @param array    $data
     *
     * @return array
     */
    public function find(Resource $resource, array $data): array
    {
        $uri = sprintf('%s?%s', $resource->getUri(), http_build_query($data));
        $response = $this->client->request('GET', $uri);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException(sprintf('Impossible to search a collection "%s".', $resource));
        }

        return json_decode($response->getBody()->getContents(), true)['hydra:member'];
    }

    /**
     * @param resource $resource
     * @param array    $data
     *
     * @return array|null
     */
    public function findOne(Resource $resource, array $data): ?array
    {
        $result = $this->find($resource, $data);
        if (empty($data)) {
            return null;
        }

        return $result[0];
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        try {
            if (200 === $this->client->request('GET', '')->getStatusCode()) {
                return true;
            }

            return false;
        } catch (ConnectException $exception) {
            return false;
        }
    }

    /**
     * @param resource $resource
     * @param array    $data
     *
     * @return array
     */
    public function save(Resource $resource, array $data): array
    {
        if (isset($data['@id'])) {
            $response = $this->client->request('PUT', $data['@id'], ['json' => $data]);
        } else {
            $response = $this->client->request('POST', $resource->getUri(), ['json' => $data]);
        }

        if (!in_array($response->getStatusCode(), [200, 201], true)) {
            throw new \RuntimeException(sprintf('Impossible to save "%s": %s', $resource, json_encode($data)));
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
