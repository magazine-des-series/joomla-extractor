<?php

declare(strict_types=1);

namespace Model;

use Doctrine\Common\Inflector\Inflector;
use MyCLabs\Enum\Enum;

class Resource extends Enum
{
    public const JOB = 'job';

    public const PERSON = 'person';

    public const TV_SERIES = 'tv_series';

    /**
     * @return string
     */
    public function getUri(): string
    {
        return Inflector::pluralize($this->value);
    }
}
