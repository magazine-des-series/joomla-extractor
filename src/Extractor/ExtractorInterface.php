<?php

declare(strict_types=1);

namespace Extractor;

use Model\Resource;

interface ExtractorInterface
{
    /**
     * @param resource $resource
     *
     * @return array
     */
    public function extract(Resource $resource): array;

    /**
     * @param resource $resource
     *
     * @return bool
     */
    public function support(Resource $resource): bool;
}
