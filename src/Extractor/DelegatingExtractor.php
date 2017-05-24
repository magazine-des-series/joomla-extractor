<?php

declare(strict_types=1);

namespace Extractor;

use Model\Resource;

class DelegatingExtractor implements ExtractorInterface
{
    /**
     * @var ExtractorInterface[]
     */
    private $extractors = [];

    /**
     * @param ExtractorInterface $extractor
     */
    public function addExtractor(ExtractorInterface $extractor): void
    {
        $this->extractors[] = $extractor;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Resource $resource): array
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->support($resource)) {
                return $extractor->extract($resource);
            }
        }

        throw new \RuntimeException(sprintf('Impossible to extract "%s"', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        foreach ($this->extractors as $extractor) {
            if ($extractor->support($resource)) {
                return true;
            }
        }

        return false;
    }
}
