<?php

declare(strict_types=1);

namespace Normalizer;

use Model\Resource;

interface NormalizerInterface
{
    /**
     * @param resource $resource
     * @param array    $data
     *
     * @return array
     */
    public function normalize(Resource $resource, array $data): array;

    /**
     * @param resource $resource
     *
     * @return bool
     */
    public function support(Resource $resource): bool;
}
