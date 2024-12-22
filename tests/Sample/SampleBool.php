<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleBool
{
    public function __construct(
        private(set) readonly bool $isValid,
    )
    {
    }
}
