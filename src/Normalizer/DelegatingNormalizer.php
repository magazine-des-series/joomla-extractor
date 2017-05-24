<?php

declare(strict_types=1);

namespace Normalizer;

use Model\Resource;

class DelegatingNormalizer implements NormalizerInterface
{
    /**
     * @var NormalizerInterface[]
     */
    private $normalizers = [];

    /**
     * @param NormalizerInterface $normalizer
     */
    public function addNormalizer(NormalizerInterface $normalizer): void
    {
        $this->normalizers[] = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(Resource $resource, array $data): array
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->support($resource)) {
                return $normalizer->normalize($resource, $data);
            }
        }

        throw new \RuntimeException(sprintf('Impossible to normalize "%s"', $resource));
    }

    /**
     * {@inheritdoc}
     */
    public function support(Resource $resource): bool
    {
        foreach ($this->normalizers as $normalizer) {
            if ($normalizer->support($resource)) {
                return true;
            }
        }

        return false;
    }
}
